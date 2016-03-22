<?php

namespace app\components\paypal;

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

class PaypalController extends Controller {

    private $_api_context;

    public function __construct() {

        // setup PayPal api context
        $paypal_conf = Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential($paypal_conf['client_id'], $paypal_conf['secret']));
        $this->_api_context->setConfig($paypal_conf['settings']);
    }

    /**
     * Make a payment with paypal
     * @param $products array list of products
     * @param $total total payment
     * @param $subTotal total price of all product
     * @param $orderId tracking number of an order id
     * @param $returnUrl url that user get back when making successful payment
     * @param $cancelUrl url that user get back when cancel the payment
     * @param $shipping shipping fee
     * @param $tax the tax
     * @param $curency currency of the payment
     * */
    public static function makePayment($products = [], $total = 0, $subTotal = 0, $orderId = 0, $returnUrl = '', $cancelUrl = '', $shipping = 0, $tax = 0, $curency = "USD") {
        if (empty($products) || empty($total) || empty($subTotal)) {
            return false;
        }

        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $items = [];
        //foreach ($products as $row) {
        $item = new \PayPal\Api\Item();
        $item->setName($products['name'])
                ->setCurrency('USD')
                ->setQuantity(1)
                ->setSku($products['product_code']) // Similar to `item_number` in Classic API
                ->setPrice(($total - $shipping - $tax)); //$products['price_tlc']
        $items[] = $item;
        //}


        $itemList = new \PayPal\Api\ItemList();
        $itemList->setItems($items);

        $details = new \PayPal\Api\Details();
        if (!empty($shipping)) {
            $details->setShipping($shipping);
        }
        if (!empty($tax)) {
            $details->setTax($tax);
        }

        $details->setSubtotal(($total - $shipping - $tax));

        $amount = new \PayPal\Api\Amount();
        $amount->setCurrency("USD")
                ->setTotal($total)
                ->setDetails($details);

        $transaction = new \PayPal\Api\Transaction();
        $transaction->setAmount($amount)
                ->setItemList($itemList)
//			->setDescription("Payment description")
                ->setInvoiceNumber(uniqid());

        $redirectUrls = new \PayPal\Api\RedirectUrls();
        $redirectUrls->setReturnUrl($returnUrl)
                ->setCancelUrl($cancelUrl);

        $payment = new \PayPal\Api\Payment();
        $payment->setIntent("sale")
                ->setPayer($payer)
                ->setRedirectUrls($redirectUrls)
                ->setTransactions(array($transaction));

        $request = clone $payment;
        try {
            $payment->create($this->_api_context);
        } catch (Exception $ex) {
            //An error is occured during the process, please try again.
            return false;
        }

        $approvalUrl = $payment->getApprovalLink();

        return $approvalUrl;
    }

    /**
     * Check payment if it success
     * */
    public static function checkSuccess($dataRequest) {
        if (isset($dataRequest['success']) && $dataRequest['success'] == 'true') {
            $paymentId = $dataRequest['paymentId'];
            $payment = \PayPal\Api\Payment::get($paymentId, $this->_api_context);

            $execution = new \PayPal\Api\PaymentExecution();
            $execution->setPayerId($dataRequest['PayerID']);

            try {
                $result = $payment->execute($execution, $this->_api_context);
                try {
                    $payment = \PayPal\Api\Payment::get($paymentId, $this->_api_context);
                } catch (Exception $ex) {
                    return false;
                }
            } catch (Exception $ex) {
                return false;
            }

            return true;
        } else {
            dump("User Cancelled the Approval");
            exit;
        }
    }

    public static function processNotification() {
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2) {
                $myPost[$keyval[0]] = urldecode($keyval[1]);
            }
        }
        // read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
        $req = 'cmd=_notify-validate';
        if (function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }
        foreach ($myPost as $key => $value) {
            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }

        // Step 2: POST IPN data back to PayPal to validate
        $ch = curl_init('https://www.paypal.com/cgi-bin/webscr');
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
        // In wamp-like environments that do not come bundled with root authority certificates,
        // please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set 
        // the directory path of the certificate as shown below:
        // curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
        if (!($res = curl_exec($ch))) {
            // error_log("Got " . curl_error($ch) . " when processing IPN data");
            curl_close($ch);
            exit;
        } else {
            $model = new \app\models\Logs();
            $model->type = 'Paypal Notification';
            $model->created = time();
            $result = json_encode([]);
            if (strcmp($res, "VERIFIED") == 0) {
                $result = json_encode($_POST);
            } else if (strcmp($res, "INVALID") == 0) {
                // IPN invalid, log for manual investigation
                $result = json_encode($_POST);
            }
            $model->content = $result;
            if (!empty(json_decode($result))) {
                $model->save();
            }
        }
        curl_close($ch);
    }

}

?>
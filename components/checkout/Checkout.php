<?php

namespace app\components\checkout;

class Checkout {

    const MERCHANT_CODE = "CKODUB2HPPTEST";
    const MERCHANT_PASS = "Password1!";
    const VERIFY_KEY = "20290352-C1F2-411D-9E37-B8149C87F495";
    //const GATEWAY_CREATE_TOKEN = "https://api.checkout.com/hpayment-TokenService/CreateToken.ashx";
    const GATEWAY_CREATE_TOKEN = "https://api.checkout.com/tokenservice/createtoken.ashx";
    const GATEWAY_HPAYMENT = "https://secure.checkout.com/hpayment-tokenretry/pay.aspx";
    const GATEWAY_IFRAME = "https://secure.checkout.com/ipayment/LuxuryCloset/iframe.aspx";

    public static function makePayment($products = [], $total = 0, $subTotal = 0, $orderId = 0, $returnUrl = '', $cancelUrl = '', $shipping = 0, $tax = 0) {
        if (empty($products) || empty($total) || empty($subTotal)) {
            return false;
        }

        $request_array = array(
            // Mandatory fields
            'paymentmode' => '0',
            'amount' => $total,
            'currencysymbol' => 'USD',
            'merchantcode' => Yii::$app->params['constants']['checkout']['code'], // This is the sandbox marchant code. Change this to your marchant code when you are in live mode.
            'password' => Yii::$app->params['constants']['checkout']['pass'], // This is the sandbox password. Change this to your password when you are in live mode.
            'action' => '1',
            'trackid' => $products['product_code'],
            'returnurl' => $returnUrl, // Change this url according to your
            'cancelurl' => $cancelUrl, // Change this url according to your,
            'bill_customerip' => self::get_client_ip() // Get customer IP address
        );

        if (!empty($products['shipping_information'])) {
            $request_array['bill_cardholder'] = $products['shipping_information']['firstname'] . ' ' . $products['shipping_information']['lastname'];
            $request_array['bill_address'] = $products['shipping_information']['address'];

            //$request_array['bill_country'] = $products['shipping_information'][''];

            $request_array['bill_email'] = $products['shipping_information']['email'];
            $request_array['bill_postal'] = $products['shipping_information']['postcode'];
            $request_array['bill_city'] = $products['shipping_information']['city'];
            $request_array['bill_state'] = $products['shipping_information']['state_province'];
            $request_array['bill_phone'] = $products['shipping_information']['phone'];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::GATEWAY_CREATE_TOKEN);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/json; charset=utf-8"));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_array));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $server_output = curl_exec($ch);
        curl_close($ch);
        //read JSon response
        $response = json_decode($server_output, TRUE);
        $Payment_Token = "";
        foreach ((array) $response as $key => $value) {
            if ($key == "PaymentToken") {
                $Payment_Token = $value;
            }
        }
        $hash = "";
        if ($Payment_Token <> "") {
            $hash = hash("sha512", $Payment_Token . Yii::$app->params['constants']['checkout']['verifyKey']);
        }

        $ProductDescription = '{"products":[';
        $ProductDescription .='{"itemnumber":"1","name":"' . $products['name'] . '","unitprice":"' . $total . '","quantity":"1","amount":"' . $total . '"},';
        $ProductDescription .=']}';

        $HpayURL = self::GATEWAY_HPAYMENT . '?pt=' . $Payment_Token . '&sig=' . $hash . '&ProductDesc=' . urlencode($ProductDescription);
        return $HpayURL;
    }

    public static function checkSuccess($trackid = 0) {
        //Transaction response handler
        //?error_code_tag=&error_text=&result=Successful&responsecode=0&tranid=10000000&authcode=&trackid=&merchantid=&sig=
        //create signature
        //append values by sorting  the keys in ascending order excluding sig.
        //e.g. authcode,error_code_tag,error_text,merchantid,responsecode,result,trackid,tranid
        $VerifyKey = Yii::$app->params['constants']['checkout']['verifyKey']; // checkout.com give a key for your account, put it here
        $arrKeys = $_GET;

        ksort($arrKeys, SORT_STRING | SORT_FLAG_CASE);

        $responseValues = "";
        foreach ($arrKeys as $key => $val) {
            if ($key != 'sig' && $key != 'success' && $key != 'order_id' && $key != 'installment_id') {
                $responseValues .= $_GET[$key];
            }
        }
        $HashResponse = hash("sha512", $responseValues . strtoupper($VerifyKey));
        if (strtoupper($_GET["sig"]) == strtoupper($HashResponse) && $_GET['trackid'] == $trackid) {
            return true;
        } else {
            return false;
        }
    }

    public static function getIframeUrl($products = [], $total = 0, $subTotal = 0, $currency_code, $orderId = 0, $returnUrl = '', $cancelUrl = '', $shipping = 0, $tax = 0) {
        if (empty($products) || empty($total) || empty($subTotal)) {
            return false;
        }

        $request_array = array(
            'paymentmode' => '0',
            'amount' => round($total),
            'currencysymbol' => $currency_code,
            'merchantcode' => Yii::$app->params['constants']['checkout']['code'], // This is the sandbox marchant code. Change this to your marchant code when you are in live mode.
            'password' => Yii::$app->params['constants']['checkout']['pass'], // This is the sandbox password. Change this to your password when you are in live mode.
            'action' => '4',
            'trackid' => $products['product_code'],
            'returnurl' => $returnUrl, // Change this url according to your
            'cancelurl' => $cancelUrl, // Change this url according to your,
            'bill_customerip' => self::get_client_ip() // Get customer IP address
        );

        if (!empty($products['shipping_information'])) {
            $request_array['bill_cardholder'] = $products['shipping_information']['firstname'] . ' ' . $products['shipping_information']['lastname'];
            $request_array['bill_address'] = str_replace(',', '', $products['shipping_information']['address']);

            $request_array['bill_country'] = $products['shipping_information']['country_name'];

            $request_array['bill_email'] = $products['shipping_information']['email'];
            $request_array['bill_postal'] = $products['shipping_information']['postcode'];
            $request_array['bill_city'] = $products['shipping_information']['city'];
            $request_array['bill_state'] = $products['shipping_information']['state_province'];
            $request_array['bill_phone'] = $products['shipping_information']['phone'];
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::GATEWAY_CREATE_TOKEN);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/json; charset=utf-8"));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_array));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $server_output = curl_exec($ch);
        curl_close($ch);
        //read JSon response
        $response = json_decode($server_output, TRUE);
        $Payment_Token = "";
        foreach ((array) $response as $key => $value) {
            if ($key == "PaymentToken") {
                $Payment_Token = $value;
            }
        }
        $hash = "";
        if ($Payment_Token <> "") {
            $hash = hash("sha512", $Payment_Token . Yii::$app->params['constants']['checkout']['verifyKey']);
        } else {
            return false;
        }

        return self::GATEWAY_IFRAME . '?lang=en&pt=' . $Payment_Token . '&sig=' . $hash;
    }

    public static function checkIframeSuccess() {
        //PHP  version 5.4
        define('MODULE_PAYMENT_CHECKOUT_HOSTED_PAYMENT_CONST_YES', 'Yes');
        define('MODULE_PAYMENT_CHECKOUT_HOSTED_PAYMENT_CONST_NO', 'No');
        define('MODULE_PAYMENT_CHECKOUT_HOSTED_PAYMENT_CONST_TEST', 'Test');
        define('MODULE_PAYMENT_CHECKOUT_HOSTED_PAYMENT_CONST_LIVE', 'Live');

        define('MODULE_PAYMENT_CHECKOUT_HOSTED_PAYMENT_CONST_AUTH', 'Authorization');
        define('MODULE_PAYMENT_CHECKOUT_HOSTED_PAYMENT_CONST_CAPTURE', 'Capture');

        define('MODULE_PAYMENT_CHECKOUT_HOSTED_PAYMENT_PARAM_NAME_TRANSACTION_ID', 'tranid');
        define('MODULE_PAYMENT_CHECKOUT_HOSTED_PAYMENT_PARAM_NAME_TRACK_ID', 'trackid');
        define('MODULE_PAYMENT_CHECKOUT_HOSTED_PAYMENT_PARAM_NAME_RESULT', 'result');
        define('MODULE_PAYMENT_CHECKOUT_HOSTED_PAYMENT_PARAM_NAME_SUCCESSFUL', 'Successful');
        define('MODULE_PAYMENT_CHECKOUT_HOSTED_PAYMENT_PARAM_NAME_RESPONSECODE', 'responsecode');

        //===========================================================
        //create signature
        //append values by sorting  the keys in ascending order excluding sig.
        //e.g. authcode,error_code_tag,error_text,merchantid,responsecode,result,trackid,tranid
        $VerificationResult = false;
        $arrKeys = $_GET;

        ksort($arrKeys, SORT_STRING | SORT_FLAG_CASE);
        $Signature = $_GET["sig"];

        $responseValues = "";

        foreach ($arrKeys as $key => $val) {
            if ($key != 'sig' && $key != 'success' && $key != 'order_id' && $key != 'installment_id' && $key != 'full_payment') {
                $responseValues .= urldecode($val);
            }
        }

        $HashResponse = hash("sha512", $responseValues . strtoupper(Yii::$app->params['constants']['checkout']['verifyKey']));

        if (strtoupper($Signature) == strtoupper($HashResponse)) {
            if ($_GET["type"] == "error") {
                $VerificationResult = false;
            } else {
                if ($_GET[MODULE_PAYMENT_CHECKOUT_HOSTED_PAYMENT_PARAM_NAME_RESULT] == MODULE_PAYMENT_CHECKOUT_HOSTED_PAYMENT_PARAM_NAME_SUCCESSFUL) {
                    if ($_GET[MODULE_PAYMENT_CHECKOUT_HOSTED_PAYMENT_PARAM_NAME_RESPONSECODE] == '0') {
                        //if ($track_id == $_GET[MODULE_PAYMENT_CHECKOUT_HOSTED_PAYMENT_PARAM_NAME_TRACK_ID])
                        //{
                        $VerificationResult = true;
                        //}
                    }
                }
            }
        } else {
            $VerificationResult = false;
        }
        return $VerificationResult;
    }

    public static function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = '127.0.0.1';
        return $ipaddress;
    }

}

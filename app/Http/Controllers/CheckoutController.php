<?php

/**
 * Cart Controller
 * */

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\components\Common;
use app\components\paypal\Paypal;
use app\components\checkout\Checkout;
use app\models\Product;
use app\models\ProductActiveRecord;
use app\models\Cart;
use app\models\OrderBilling;
use app\models\OrderDelivery;
use app\models\OrderInstallment;
use app\models\Country;
use app\models\Order;
use app\models\OrderProduct;
use app\models\Voucher;
use app\models\Bucket;
use app\models\User;
use app\models\Home;

class CheckoutController extends Controller {

    public $layout = 'main.twig';
    private $paymentMethods = [];
    private $deliveryMethod = [];

    public function beforeAction($action) {
        if (!parent::beforeAction($action)) {
            return false;
        }
        $this->paymentMethods = [
            'credit_cart' => Yii::t('app', 'Credit Card'),
            'paypal' => Yii::t('app', 'Paypal'),
            'bank_transfer' => Yii::t('app', 'Bank Transfer'),
            'cash_on_delivery' => Yii::t('app', 'Cash On Delivery'),
            'cash_on_location' => Yii::t('app', 'Cash On Location')
        ];
        $this->deliveryMethod = [
            'delivery' => 'delivery',
            'pick_up' => 'pick up'
        ];
        return true;
    }

    private static function isInstallment($installment = 0) {
        if (empty($installment)) {
            if (Yii::$app->user->isGuest && !empty($_COOKIE['cart_product']) && !empty($_COOKIE['cart_installments'])) {
                $cart = json_decode($_COOKIE['cart_product']);
                if (count($cart) == 1) {
                    if ($cart[0]->item == $_COOKIE['cart_installments']) {
                        // if($_COOKIE['cart_product'] == $_COOKIE['cart_installments']){ // with cart_product is a string
                        return true;
                    }
                }
            }
            if (!Yii::$app->user->isGuest) {
                $products = Cart::getProducts();
                if (!empty($products) && count($products) == 1 && !empty($products[0]->installments)) {
                    return true;
                }
            }
        }
        return false;
    }

    private static function getProductsInCart() {
        $products = [];
        if (Yii::$app->user->isGuest && !empty($_COOKIE['cart_product'])) {
            $cart = json_decode($_COOKIE['cart_product']);
            $cart_items = '';
            foreach ($cart as $key => $value) {
                $cart_items .= $value->item . ',';
            }

            $products = Product::getProductsInCart(rtrim($cart_items, ','));
        }
        if (!Yii::$app->user->isGuest) {
            $products = Product::getProductsInCart();
        }
        return $products;
    }

    /**
     * Manage the cart
     * */
    public function actionCart() {
        if (Yii::$app->view->theme->pathMap['@app/views'] == "@app/themes/mobile/views") {
            $this->layout = "checkout.twig";
        }
        $model = new Cart();
        if (Yii::$app->request->post('checkout')) {
            // $this->redirect('?r=checkout/installment');
            // redirect with parameters: $this->redirect(array('controller/action', 'param1'=>'value1', 'param2'=>'value2',...))
            return $this->redirect(array('checkout/installment'));
        }
        $products = self::getProductsInCart();

        $canInstallment = [];
        foreach ($products as $row) {
            $installment = (Product::INSTALLMENT_DAYS - round((time() - $row['activate_schedule']) / (60 * 60 * 24)));
            if ($installment <= 0) {
                $canInstallment[] = "'" . $row['name'] . "'";
            }
        }
        $installments = false;
        if (Yii::$app->user->isGuest) {
            if (count($products) == 1 && (!empty($_COOKIE['cart_installments']) && ($products[0]['id'] == $_COOKIE['cart_installments']))) {
                $installments = true;
            }
        } else {
            if (count($products) == 1) {
                if (!empty(Cart::find()->where([
                                    'product_id' => $products[0]['id'],
                                    'user_id' => Yii::$app->user->id,
                                    'installments' => 1
                                ])->all())) {
                    $installments = true;
                }
            }
        }

        $total_cost = 0;
        foreach ($products as $key => $value) {
            $total_cost += $value['price_tlc'];
        }
        $this->view->params['ecomm_prodid'] = $products;
        $this->view->params['ecomm_pagetype'] = 'cart';
        $this->view->params['ecomm_totalvalue'] = round($total_cost);

        $continue_url = 'filter';
        return $this->render('cart.twig', [
                    'products' => $products,
                    'continue_url' => $continue_url,
                    'cart_system_message' => Yii::$app->session->getFlash('cart_system_message'),
                    'canInstallment' => implode(Yii::t('app', ' and '), $canInstallment),
                    'is_show_installments' => $installments,
                    'model' => $model,
                    'installment_rate' => (float) \app\models\Option::getConfig('installment_rate'),
                    'alias_categories' => ArrayHelper::map(Bucket::aliasCategories(), 'id', 'alias'),
                    'upload_url' => \app\models\Option::getConfig('upload_url')
        ]);
    }

    /**
     * add a product to the cart
     * @param $product_id
     * @return redirection to cart page 
     * */
    public function actionAdd_to_cart($product_id = 0, $installments = 0) {
        $userId = Yii::$app->user->id;
        if (empty($product_id) || empty($userId)) {
            return $this->goBack();
        } else {
            $quantity = 1;
            if (Cart::addProduct($userId, $product_id, $installments, $quantity)) {
                return $this->redirect(['checkout/cart']);
            }
        }
    }

    public function actionRemove_from_cart($product_id = 0) {
        $userId = Yii::$app->user->id;
        // dump($userId);
        // if(empty($product_id) || empty($userId)){
        if (empty($product_id)) {
            return $this->goBack();
        } else {
            $remove_anynomouse_user = Cart::removeProduct(0, $product_id);
            $remove_authenticated_user = Cart::removeProduct($userId, $product_id);
            if ($remove_authenticated_user || $remove_anynomouse_user) {
                Yii::$app->session->setFlash('cart_system_message', Yii::t('app', 'You have successfully removed item from cart.'));
                return $this->redirect(['checkout/cart']);
            }
        }
    }

    public function actionRemove_from_order($order_id = 0, $product_id = 0) {
        if (empty($product_id) || Yii::$app->user->isGuest || empty($order_id)) {
            return $this->redirect(['checkout/confirm', 'order_id' => $order_id]);
        } else {
            $order = Order::findOne($order_id);
            if (empty($order) || $order->created_by_id != Yii::$app->user->id || $order->payment_status != 'Not received') {
                return $this->redirect(['checkout/confirm', 'order_id' => $order_id]);
            }
            $product = OrderProduct::find()->where(['order_id' => $order->id, 'product_id' => $product_id])->one();
            if (!empty($product)) {
                if ($product->delete()) {
                    $products = OrderProduct::find()->where(['order_id' => $order->id])->all();
                    if (empty($products)) {
                        $billing = OrderBilling::find()->where(['order_id' => $order->id])->one();
                        if (!empty($billing)) {
                            $billing->delete();
                        }
                        $delivery = OrderDelivery::find()->where(['order_id' => $order->id])->one();
                        if (!empty($delivery)) {
                            $delivery->delete();
                        }
                        $order->delete();
                    }
                }
            }
        }
        return $this->redirect(['checkout/confirm', 'order_id' => $order_id]);
    }

    /**
     * Count number of items in cart
     * @return mixed
     * */
    public function actionNumber_in_cart() {
        $cartInfor = Cart::getProductIds();
        if (empty($cartInfor)) {
            echo '';
        } else {
            echo count(explode(',', $cartInfor));
        }
        exit;
    }

    /**
     * Get products list in the cart of current user
     * @return mixed
     * */
    public function actionProduct_in_cart() {
        $cartInfor = Cart::getProductIds();
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (empty($cartInfor)) {
            // echo 'test';
            return [];
        } else {
            $pro_list = [];
            $product_ids = explode(',', $cartInfor);
            foreach ($product_ids as $key => $value) {
                $product = ProductActiveRecord::findOne([$value]);
                $pro_list[] = ['item' => $value, 'price' => $product->price_tlc];
                // dump($product, false);
            }
            // dump($pro_list);
            return $pro_list;
            echo count(explode(',', $cartInfor));
        }
        exit;
    }

    /**
     * Check if a product is inside the cart
     * @return string 
     * */
    public function actionCheck_product_cart($product_id = 0) {
        if ((Cart::find()->where(['product_id' => $product_id, 'user_id' => Yii::$app->user->id])->all())) {
            echo "in cart";
        } elseif ((Cart::find()->where(['product_id' => $product_id])->all())) {
            echo "exist";
        } else {
            echo "no";
        }
        exit();
    }

    /**
     * Temporary save the installment payment information to session
     * */
    public function actionInstallment() {

        if (empty($data = Yii::$app->request->post())) {
            return Common::jsonOut('fail', 402);
        } elseif (empty($data['installment_amount']) || !is_numeric($data['installment_amount']) || intval($data['installment_amount']) <= 0) {
            return Common::jsonOut(Yii::t('app', 'Please input a valid value for Installment amount.'), 402);
        } else {
            $payment = [];

            if (Yii::$app->session->has('Payment')) {
                $payment = Yii::$app->session->get('Payment');
            }

            $payment['Installment']['amount'] = $data['installment_amount'];
            if (!empty($data['payingDetails'])) {
                $payment['Installment']['paying_detail'] = $data['payingDetails'];
            }

            $payment['Installment']['completed_pay'] = $data['completed_pay'];
            Yii::$app->session->set('Payment', $payment);

            return Common::jsonOut('Ok');
        }

        return Common::jsonOut(json_encode(Yii::$app->request->post()));
    }

    public function actionDelivery() {
        if (empty($data = Yii::$app->request->post())) {
            return Common::jsonOut('fail');
        } else {
            $payment = [];
            $shipping = 0;

            if (Yii::$app->session->has('Payment')) {
                $payment = Yii::$app->session->get('Payment');
            }

            User::saveUserOnOrder($data['OrderBilling']);

            if (!empty($data['OrderBilling'])) {
                $payment['OrderBilling'] = $data['OrderBilling'];
            }
            if (strlen(implode($data['OrderDelivery'])) && !empty($data['OrderBilling']['different_delivery'])) {
                $payment['OrderDelivery'] = $data['OrderDelivery'];
            } else {
                $payment['OrderDelivery'] = $data['OrderBilling'];
            }

            if (!empty($data['DeliveryMethod'])) {
                $payment['DeliveryMethod'] = $data['DeliveryMethod'];

                if (!empty($data['product_ids'])) {
                    //Calculate subtotal for checkout and paypal payment
                    $productArr = ProductActiveRecord::find()->where(['id' => $data['product_ids']])->all();
                    $subTotal = round(Cart::totalPayment($productArr));

                    if ($payment['DeliveryMethod']['delivery'] == $this->deliveryMethod['pick_up']) {
                        $shipping = 0;
                    } else {
                        /*
                         * LIST-262
                         * 3. Free global shipping over $1000
                         * 4. Free shipping in UAE
                         */
                        if ($subTotal > 1000) {
                            $shipping = 0;
                        } elseif (!empty($payment['OrderDelivery'])) {
                            $uae_country = Country::findOne(['name' => 'united arab emirates']);

                            if ($uae_country->id == $payment['OrderDelivery']['country_id']) {
                                $shipping = 0;
                            } else {
                                $shipping = Product::getShippingFee($data['product_ids'], $payment);
                            }
                        } else {
                            $shipping = Product::getShippingFee($data['product_ids'], $payment);
                        }
                    }
                }

                if (!empty($payment['Installment']['paying_detail'])) {
                    $productOrderIds = [];
                    $installmentOrderShipping = [];
                    foreach ($payment['Installment']['paying_detail'] as $row) {
                        if (!empty($row['order_id'])) {
                            if (empty($productOrderIds[$row['order_id']])) {
                                $productOrderIds[$row['order_id']] = [];
                            }
                            array_push($productOrderIds[$row['order_id']], $row['product_id']);
                        }
                    }
                    if (!empty($productOrderIds)) {
                        foreach ($productOrderIds as $key => $value) {
                            $installmentOrderShipping[$key] = Product::getShippingFee($value, $payment);
                        }
                        $payment['Installment']['shipping'] = $installmentOrderShipping;
                    }
                }
            }

            Yii::$app->session->set('Payment', $payment);

            if (isset($payment['Installment']['completed_pay']) && empty($payment['Installment']['completed_pay']) && $payment['Installment']['completed_pay'] != 0) {
                $shipping = false;
            }

            // Tax - LIST-432
            $tax = 0;
            $us_country = Country::findOne(['country_code' => 'US']);

            if ($us_country->id == $payment['OrderDelivery']['country_id']) {

                if (is_numeric($shipping)) {
                    $tax = ($subTotal) * 9 / 100;
                }
            }

            return Common::jsonOut(['response' => 'Ok', 'shipping' => $shipping, 'tax' => $tax]);
        }
    }

    public function actionReview() {
        if (Yii::$app->request->post('add')) {
            echo "testing";
            return $this->redirect(array('checkout/confirm'));
        }
        return $this->render('review.twig');
    }

    /**
     * Make the payment
     * */
    public function actionPayment() {
        if (empty($data = Yii::$app->request->post()) || !Yii::$app->session->has('Payment')) {
            return $this->redirect(['checkout/confirm']);
        } else {
            if (empty($data['Product']['id'])) {
                return $this->redirect(['checkout/cart']);
            } else {
                if (empty(Yii::$app->user->id)) {
                    return $this->redirect(['checkout/cart']);
                }
                $paymentInfo = Yii::$app->session->get('Payment'); //Get Installment, Billing, Delivery, Voucher from session that saved in previous step
                $data['trackid'] = time();

                if (isset($_COOKIE['theluxcurrency'])) {
                    $data['currency_code'] = $_COOKIE['theluxcurrency'];
                } else {
                    $data['currency_code'] = 'usd';
                }

                $products = []; //List of products
                $orderModel = new Order(); // Recorded order
                $order_id = 1;

                if (!empty($data['order_id'])) {
                    $order_ids = explode('_', $data['order_id']);

                    if (count($order_ids) == 1) {
                        $order_id = $data['order_id'];
                        $orderModel = Order::findOne($data['order_id']);
                    } elseif (count($order_ids) > 1) {
                        $orderModels = Order::find()->where(['id' => $order_ids])->all();
                    }
                } else {
                    $orderModel->currency = $data['currency_code'];
                }

                //Calculate subtotal for checkout and paypal payment
                $productArr = ProductActiveRecord::find()->where(['id' => $data['Product']['id']])->all();
                $data['sub_total'] = round(Cart::totalPayment($productArr));

                //Calculate shipping fee for checkout and paypal payment
                /*
                 * LIST-262
                 * 3. Free global shipping over $1000
                 * 4. Free shipping in UAE
                 */
                if ($data['sub_total'] > 1000) {
                    $data['shipping_fee'] = 0;
                } elseif ($paymentInfo['DeliveryMethod']['delivery'] == 'pick up') {
                    $data['shipping_fee'] = 0;
                } elseif (!empty($paymentInfo['OrderDelivery'])) {
                    $uae_country = Country::findOne(['name' => 'united arab emirates']);

                    if ($uae_country->id == $paymentInfo['OrderDelivery']['country_id']) {
                        $data['shipping_fee'] = 0;
                    } else {
                        $data['shipping_fee'] = Product::getShippingFee($data['Product']['id'], $paymentInfo);
                    }
                } else {
                    $data['shipping_fee'] = Product::getShippingFee($data['Product']['id'], $paymentInfo);
                }

                // Tax - LIST-432
                $data['tax'] = 0;
                $us_country = Country::findOne(['country_code' => 'US']);

                if ($us_country->id == $paymentInfo['OrderDelivery']['country_id']) {
                    $data['tax'] = $data['sub_total'] * 9 / 100;
                }

                if (empty($data['installment_payment'])) {
                    $installment_id = 0;

                    //Calculate total amount for checkout and paypal payment
                    if (empty($data['order_id'])) {
                        $data['total_amount'] = $data['sub_total'] + (float) $data['tax'] + $data['shipping_fee'] - (empty($paymentInfo['Voucher']) ? 0 : (float) $paymentInfo['Voucher']);
                        if ($data['total_amount'] < 0) {
                            $data['total_amount'] = 0;
                        }
                    } else {
                        $data['total_amount'] = Order::getTotalAmount($paymentInfo, $data['order_id']);
                    }
                } else {
                    $installment_id = 1;

                    $data['total_amount'] = $data['installment_payment'];
                }

                $paymentInfo['data'] = $data;
                Yii::$app->session->set('Payment', $paymentInfo); //Save to session for checkout and paypal payment.

                $returnUrl = Url::to(['checkout/confirm', 'success' => 'true', 'full_payment' => 1, 'congratulation_mess' => OrderDelivery::getCongratulationType($paymentInfo), 'order_id' => $order_id, 'installment_id' => $installment_id], true);
                $cancelUrl = Url::to(['checkout/confirm', 'success' => 'false', 'order_id' => $order_id, 'installment_id' => $installment_id], true);

                //'Credit Card','Paypal','Bank Transfer','Cash On Delivery','Cash On Location'
                switch ($data['Payment']['payment']) {
                    case 'credit_cart': //Checkout with checkout.com
                        $orderBilling = [];
                        if (!empty($paymentInfo['OrderBilling'])) {
                            $orderBilling = $paymentInfo['OrderBilling'];
                        }

                        return $this->redirect(['checkout/summary']);

                    case 'paypal': //Checkout with paypal
                        $rtPaypal = Paypal::makePayment([
                                    'name' => Yii::t('app', 'Order number: {orderId}', ['orderId' => $order_id]),
                                    'product_code' => $data['trackid'],
                                    'price_tlc' => $data['sub_total']
                                        ], $data['total_amount'], $data['sub_total'], $order_id, $returnUrl, $cancelUrl, ((($data['total_amount'] < $data['sub_total']) && !empty($data['installment_payment'])) ? 0 : $data['shipping_fee']));
                        if ($rtPaypal) {
                            return $this->redirect($rtPaypal);
                        }

                        break;
                    case 'bank_transfer':
                    case 'cash_on_delivery':
                    case 'cash_on_location':
                        if (empty($orderModels)) {
                            $orderModel->saveOrder($paymentInfo, true);
                            return $this->redirect([
                                        'checkout/confirm',
                                        'full_payment' => 1,
                                        'order_id' => $orderModel->id,
                                        'congratulation_mess' => OrderDelivery::getCongratulationType($paymentInfo)
                            ]);
                        } else {
                            $isShipping = true;
                            $orderShippingId = 0;
                            foreach ($orderModels as $row) {

                                if ($orderShippingId != 0) {
                                    $row->order_ship_fee_id = $orderShippingId;
                                }
                                $row->saveOrder($paymentInfo, $isShipping);
                                if ($isShipping) {
                                    $orderShippingId = $row->id;
                                }
                                $isShipping = false;
                            }

                            return $this->redirect([
                                        'checkout/confirm',
                                        'full_payment' => 1,
                                        'order_id' => $paymentInfo['data']['order_id'],
                                        'congratulation_mess' => OrderDelivery::getCongratulationType($paymentInfo)
                            ]);
                        }

                        break;
                }
            }
        }
    }

    /**
     * Save one order, this function is called after user made a payment with paypal or checkout.com
     * @param $paiedOrder Order model
     * @param $dataRequest data that get back from checkout.com or Paypal
     * @param $paymentInfo Payment information that user have made. It is gotten from session
     * @param $shipping shipping fee
     * */
    private static function saveOneOrder($paiedOrder = [], $dataRequest, $paymentInfo, $isShipping = 0) {
        if (empty($paiedOrder)) {
            return 0;
        }

        if (!empty($returnArr = $paiedOrder->saveOrder($paymentInfo, $isShipping))) {//Save the order
            if (empty($paiedOrder->installments)) {
                if (!empty($dataRequest['tranid'])) {
                    $paiedOrder->checkout_id = $dataRequest['tranid'];
                } elseif (!empty($dataRequest['paymentId'])) {
                    $paiedOrder->paypal_id = $dataRequest['paymentId'];
                }
                $paiedOrder->save(); //Save checkout id or paypal id
            } else if (!empty($dataRequest['installment_id'])) {
                $installmentOrder = OrderInstallment::findOne($returnArr['installment_id']);
                if (!empty($installmentOrder)) {
                    if (!empty($dataRequest['tranid'])) {
                        $paiedOrder->checkout_id = $dataRequest['tranid'];
                    } elseif (!empty($dataRequest['paymentId'])) {
                        $paiedOrder->paypal_id = $dataRequest['paymentId'];
                    }
                    $installmentOrder->save(); //Save checkout id or paypal id
                }
            }
            return $paiedOrder->id;
        }
        return 0;
    }

    /**
     * Check cart information before making a payment, process order after payment from checkout.com or Paypal.
     * @param $is_installment true: checkout with installment
     * */
    public function actionConfirm($is_installment = 0, $full_payment = 0, $order_id = 0, $complete = 0, $congratulation_mess = 0, $change_installment = 0) {//$full_payment = 1 means that the payment is successful.
        // Check if current user is the user that ordered the item(s) or not -> only allow order's owner to access relevant checkout page
        // Redirect to homepage for all these cases
        if ($order_id) {
            $order = Order::findOne($order_id);
            if (empty($order) || empty($order->created_by_id) || $order->created_by_id != Yii::$app->user->id) {
                $this->redirect(Yii::$app->gethomeUrl());
            }
        }
        $this->layout = "checkout.twig";
        $multiInstallmentPayment = 0;

        //Process successful payment
        if (!empty($dataRequest = Yii::$app->request->get())) {
            //Process payment from paypal or checkout.com
            if (isset($dataRequest['success']) && $dataRequest['success'] == 'true' && (isset($dataRequest['paymentId']) || isset($dataRequest['tranid'])) && Yii::$app->session->has('Payment')) {
                //Process order after making payment with checkout.com or paypal

                $paymentInfo = Yii::$app->session->get('Payment');

                $paiedOrder = new Order();
                $paiedOrders = [];
                if (!empty($paymentInfo['data']['order_id'])) {
                    $order_ids = explode('_', $paymentInfo['data']['order_id']);
                    if (count($order_ids) == 1) {
                        $paiedOrder = Order::findOne($paymentInfo['data']['order_id']);
                    } elseif (count($order_ids) > 1) {
                        $paiedOrders = Order::find()->where(['id' => $order_ids])->all();
                    }
                }

                if (isset($dataRequest['paymentId'])) {
                    if (Paypal::checkSuccess($dataRequest)) {//Check Paypal payment
                        if (empty($paiedOrders)) {
                            $order_id = self::saveOneOrder($paiedOrder, $dataRequest, $paymentInfo, 1);
                            if ($order_id) {
                                $full_payment = 1;
                            }
                        } else {
                            $isShipping = 1;
                            $orderShippingId = 0;
                            $order_tmp_id = [];
                            foreach ($paiedOrders as $row) {
                                if ($orderShippingId != 0) {
                                    $row->order_ship_fee_id = $orderShippingId;
                                }
                                $order_tmp_id[] = self::saveOneOrder($row, $dataRequest, $paymentInfo, $isShipping);
                                if ($order_tmp_id) {
                                    $full_payment = 1;
                                }
                                if ($isShipping) {
                                    $orderShippingId = $row->id;
                                }
                                $isShipping = 0;
                            }
                            $order_id = implode('_', $order_tmp_id);
                        }
                    }
                } else if (isset($dataRequest['tranid'])) {
                    //if(Checkout::checkSuccess($paymentInfo['data']['trackid'])){//Check checkout payment
                    //Check checkout payment
                    if (Checkout::checkIframeSuccess()) {

                        if (empty($paiedOrders)) {
                            $order_id = self::saveOneOrder($paiedOrder, $dataRequest, $paymentInfo, 1);
                            if ($order_id) {
                                $full_payment = 1;
                            }
                        } else {
                            $isShipping = 1;
                            $orderShippingId = 0;
                            $order_tmp_id = [];
                            foreach ($paiedOrders as $row) {
                                if ($orderShippingId != 0) {
                                    $row->order_ship_fee_id = $orderShippingId;
                                }
                                $order_tmp_id[] = self::saveOneOrder($row, $dataRequest, $paymentInfo, $isShipping);
                                if ($order_tmp_id) {
                                    $full_payment = 1;
                                }
                                if ($isShipping) {
                                    $orderShippingId = $row->id;
                                }
                                $isShipping = 0;
                            }
                            $order_id = implode('_', $order_tmp_id);
                        }
                    } else {
                        return $this->redirect(['checkout/summary?error=invalid_card']);
                    }
                }
                $congratulation_mess = OrderDelivery::getCongratulationType($paymentInfo);
            } else if (isset($dataRequest['success'])) {
                if ($dataRequest['success'] == 'false') {
                    return $this->redirect(['checkout/cart']);
                } else if (!empty($dataRequest['type']) && $dataRequest['type'] == 'error' && !empty($dataRequest['error_code_tag'])) {
                    return $this->redirect(['checkout/summary?error=invalid_card']);
                }
            }
        }

        // POST
        //Remove previous payment information
        if (Yii::$app->session->has('Payment')) {
            Yii::$app->session->remove('Payment');
        }
        //Predefine some variables
        $order = [];
        $canInstallment = [];
        $products = [];
        $billingInfor = [];
        $deliveryInfor = [];
        $totalPayment = 0;
        $cartId = 0;
        $paymentMade = -1;
        $deliveryModel = new OrderDelivery();
        $billingModel = new OrderBilling();
        //Get information if the payment already successful
        $installmentRate = (float) \app\models\Option::getConfig('installment_rate');
        $shipping_fee = 0;
        $tax = 0;
        $full_installment_payment = 0;

        //Get information if user haven't make any payment
        if (!empty($order_id)) {//Making payment from an order in the past, normally it's installment order
            $orderIds = explode('_', $order_id);

            if (count($orderIds) == 1) {//Checkout with one order
                $order = Order::findOne($order_id);
                if (!empty($full_payment) && !empty($order) && !empty($order->installments)) {
                    $is_installment = 1;
                }
            } else { //Checkout with more than one order (complete order from my purchases page)
                $order = Order::find()->where(['id' => $orderIds])->all();
                if ($full_payment) {
                    foreach ($order as $row) {
                        $shipping_fee += $row->shipping_fee;
                        $tax += $row->shipping_fee;
                        $full_installment_payment += $row->total_amount;
                    }
                }
                $multiInstallmentPayment = 1;
            }

            if (empty($order)) {
                return $this->redirect(['my-account/my-purchases']);
            }
            if (count($order) == 1 && $order->payment_status != 'Not received' && empty($order->installments) && $full_payment == 0) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'You cannot edit an order that is already paid.'));
                return $this->redirect(['my-account/my-purchases']);
            }

            $orderProducts = OrderProduct::find()->where(['order_id' => $orderIds])->all();
            if (empty($orderProducts)) {
                return $this->redirect(['my-account/my-purchases']);
            }

            //Get all products in the order/orders 
            foreach ($orderProducts as $row) {
                //$productInOrder = (array)json_decode($row['product_information']);
                //if($multiInstallmentPayment){
                $productFromOrderPro = (array) json_decode($row['product_information']);
                $productFromPro = Yii::$app->db->createCommand('SELECT * FROM products where id=' . $row['product_id'])->queryOne();
                $productInOrder = count($productFromOrderPro) >= count($productFromPro) ? $productFromOrderPro : $productFromPro;
                $orderOfProduct = Order::findOne($row['order_id']);
                $productFromOrderPro = (array) json_decode($row['product_information']);
                $productFromPro = Yii::$app->db->createCommand('SELECT * FROM products where id=' . $row['product_id'])->queryOne();
                $productInOrder = count($productFromOrderPro) >= count($productFromPro) ? $productFromOrderPro : $productFromPro;
                $productInOrder['paied_amount'] = (float) $orderOfProduct['total_amount'];
                $productInOrder['remaining_amout'] = (float) $orderOfProduct['sub_total'] - (float) $orderOfProduct['total_amount'];
                $productInOrder['prepay_amout'] = $productInOrder['remaining_amout'] * $installmentRate;
                $productInOrder['voucher_amount'] = $orderOfProduct['voucher_amount'];
                if ($complete) {
                    $productInOrder['prepay_amout'] = $productInOrder['remaining_amout'];
                }
                $productInOrder['order_id'] = $row['order_id'];
                //}
                $products[] = $productInOrder;
            }

            //Get billing information of order
            $billingInfor = OrderBilling::find()->where(['order_id' => $orderIds])->one();
            //Get delivery information of order
            $deliveryInfor = OrderDelivery::find()->where(['order_id' => $orderIds])->one();

            if (!empty($orderIds) && !empty($is_installment)) {
                $paymentMade = Order::getInstallmentMade($orderIds);
            }
        } else { //Get products from cart, totally new order
            if (self::isInstallment($is_installment) && $change_installment == 0) {
                return $this->redirect(['checkout/confirm', 'is_installment' => 1]);
            }
            $products = self::getProductsInCart();
        }

        if (empty($products)) {
            return $this->redirect(['checkout/cart']);
        }
        //Check if a product can be order by installment
        foreach ($products as $row) {
            if (!empty($row['activate_schedule'])) {
                $installment = (Product::INSTALLMENT_DAYS - round((time() - $row['activate_schedule']) / (60 * 60 * 24)));
            } else {
                $installment = 1;
            }

            if ($installment <= 0) {
                $canInstallment[] = "'" . $row['name'] . "'";
            }
        }

        //Get total payment
        $totalPayment = Cart::totalPayment($products);
        $cartId = 0;
        if (!Yii::$app->user->isGuest) {
            //get cart id
            $cartId = Cart::find()->where(['user_id' => Yii::$app->user->id])->one();

            if (!empty($cartId)) {
                $cartId = $cartId->id;
                $cart = Cart::findOne($cartId);
                if (empty($is_installment)) {
                    $cart->installments = 0;
                } else {
                    $cart->installments = 1;
                }
                $cart->save();
            }
        }

        //Calculate information for the checkout
        if (!empty($billingInfor)) {
            $billingModel = $billingInfor;
        } else if (!Yii::$app->user->isGuest) {
            if (!empty(Yii::$app->user->identity->country_id)) {
                $billingModel->country_id = Yii::$app->user->identity->country_id;
            }
        }
        $totalPayment = round($totalPayment, 0);
        if (!empty($deliveryInfor)) {
            $deliveryModel = $deliveryInfor;
        }

        $subTotalPayment = $totalPayment;

        if ($paymentMade > 0 && $totalPayment > $paymentMade) {
            $totalPayment = $totalPayment - $paymentMade;
        }
        if ($complete) {
            /* if(count($order) == 1){
              $totalPayment += ($order->shipping_fee)?$order->shipping_fee:0;
              } */
            $installmentPrePay = $totalPayment;
        } else {
            $installmentPrePay = round($totalPayment * $installmentRate);
        }
        if ($totalPayment == $paymentMade) {//Total money are full
            $installmentPrePay = 0;
        }
        $remainingInstallment = $totalPayment - $installmentPrePay;
        $voucherAmount = 0;
        if (count($order) == 1) {
            if (!empty($order->voucher_amount)) {
                $voucherAmount = $order->voucher_amount;
            }
        } else {
            foreach ($order as $row) {
                $voucherAmount += $row->voucher_amount;
            }
        }
        if ($complete) {
            $installmentPrePay = $totalPayment - $voucherAmount;
        } else {
            $remainingInstallment -= $voucherAmount;
        }
        if ($totalPayment == $paymentMade) {
            $remainingInstallment = 0;
            $installmentPrePay = 0;
        }

        $countries = Country::find()->where('country_zone_id <> 0')->all();

        $total_value = 0;
        if ($congratulation_mess != 0 && !empty($order->total_amount)) {
            $total_value = $order->total_amount;
        }
        if (!empty($_COOKIE['theluxcurrency'])) {
            $currencyRate = Yii::$app->params['currency_rate'][strtolower($_COOKIE['theluxcurrency'])];
        } else {
            $currencyRate = 1;
        }
        $this->view->params['ecomm_pagetype'] = (Yii::$app->request->get('full_payment')) ? "purchase" : "cart";
        // $this->view->params['ecomm_totalvalue'] = (count($order) == 0) ? round($totalPayment - $voucherAmount) : round($order['total_amount']);
        $this->view->params['ecomm_totalvalue'] = $total_value;
        $this->view->params['ecomm_prodid'] = $products;

        return $this->render('confirmation.twig', [
                    'products' => $products,
                    'order' => $order,
                    'canInstallment' => implode(Yii::t('app', ' and '), $canInstallment),
                    'is_purchase_in_installment' => $is_installment,
                    'installment_rate' => (float) \app\models\Option::getConfig('installment_rate'),
                    'payment_made' => $paymentMade,
                    'total_payment' => $totalPayment,
                    'installment_pre_pay' => $installmentPrePay,
                    'remaining_installment' => $remainingInstallment,
                    'voucher_amount' => $voucherAmount,
                    'sub_total_payment' => $subTotalPayment,
                    'delivery_model' => $deliveryModel,
                    'billing_model' => $billingModel,
                    'countries' => ArrayHelper::map($countries, 'id', 'name'),
                    'cart_id' => $cartId,
                    'full_payment' => $full_payment,
                    'billing_info' => $billingInfor,
                    'delivery_info' => $deliveryInfor,
                    'order_id' => $order_id,
                    'user' => Yii::$app->user->identity,
                    'multi_installment_payment' => $multiInstallmentPayment,
                    'complete' => $complete,
                    'congratulation_mess' => $congratulation_mess,
                    'uploads_url' => \app\models\Option::getConfig('upload_url'),
                    'taxes' => json_encode([]),
                    'shipping_fee' => $shipping_fee,
                    'tax' => $tax,
                    'full_installment_payment' => $full_installment_payment,
                    'currency_rate' => $currencyRate,
                    'currency_code' => !empty($_COOKIE['theluxcurrency']) ? $_COOKIE['theluxcurrency'] : 'USD'
        ]);
    }

    /**
     * Check when user fill the voucher
     * @param $code the code user filled
     * */
    public function actionVoucher($code = '') {
        $data = Yii::$app->session->get('Payment');
        $data['data'] = Yii::$app->request->get();

        $voucher = Voucher::getVoucherValue($data['data']['voucher'], $data);

        if (Yii::$app->session->has('Payment') && !empty($voucher) && $voucher > 0) {
            $sessionPayment = Yii::$app->session->get('Payment');
            $sessionPayment['Voucher'] = $voucher;
            $sessionPayment['Voucher_code'] = $code;
            Yii::$app->session->set('Payment', $sessionPayment);
        } else {
            $voucher = 0;
        }

        return Common::jsonOut($voucher);
    }

    public function actionSummary() {
        $this->layout = "checkout.twig";
        $paymentInfo = Yii::$app->session->get('Payment');
        //dump($paymentInfo);
        $products = ProductActiveRecord::find()->where(['id' => $paymentInfo['data']['Product']['id']])->all();
        //dump($paymentInfo);
        $countryName = '';
        $countryName2 = '';
        if (!empty($paymentInfo['OrderBilling'])) {
            $country = Country::findOne($paymentInfo['OrderBilling']['country_id']);
            if (!empty($country)) {
                $countryName = $country->name;
            }
        }

        if (!empty($paymentInfo['OrderBilling']['different_delivery'])) {
            $countryDelivery = Country::findOne($paymentInfo['OrderDelivery']['country_id']);
            if (!empty($countryDelivery)) {
                $countryName2 = $countryDelivery->name;
            }
        }

        $installment_id = 0;
        $order_id = 0;
        $is_purchase_in_installment = 0;
        $installment_pre_pay = 0;
        $payment_made = 0;
        if (!empty($paymentInfo['data']['installment_payment'])) {
            $is_purchase_in_installment = 1;
            $installment_pre_pay = $paymentInfo['data']['installment_payment'];
        }

        $returnUrl = Url::to(['checkout/confirm', 'success' => 'true', 'full_payment' => 1, 'order_id' => $order_id, 'installment_id' => $installment_id], true);
        $cancelUrl = Url::to(['checkout/confirm', 'success' => 'false', 'full_payment' => 1, 'order_id' => $order_id, 'installment_id' => $installment_id], true);

        $remaining_installment = ((float) $paymentInfo['data']['sub_total'] - $installment_pre_pay);

        if (!empty($paymentInfo['Installment']['amount'])) {
            $installment_pre_pay = $paymentInfo['Installment']['amount'];
            if (!empty($paymentInfo['data']['order_id'])) {
                $ids = explode('_', $paymentInfo['data']['order_id']);
                if (count($ids) == 1) {
                    $payment_made = OrderInstallment::getAmount($paymentInfo['data']['order_id']);
                } else {
                    foreach ($ids as $row) {
                        $payment_made += OrderInstallment::getAmount($row);
                    }
                }

                $remaining_installment -= $payment_made;
            }

            if (!empty($paymentInfo['data']['shipping_fee'])) {
                $remaining_installment += (float) $paymentInfo['data']['shipping_fee'];
            }

            if (!empty($paymentInfo['data']['tax'])) {
                $remaining_installment += (float) $paymentInfo['data']['tax'];
            }
        }

        $iFrame = '';

        if (!empty($paymentInfo['OrderBilling']['country_id']) && !empty(Country::findOne($paymentInfo['OrderBilling']['country_id'])->name)) {

            $paymentInfo['OrderBilling']['country_name'] = Country::findOne($paymentInfo['OrderBilling']['country_id'])->name;
        } else {
            $paymentInfo['OrderBilling']['country_name'] = '';
        }
        $iFrame = Checkout::getIframeUrl([
                    'name' => Yii::t('app', 'Order number: {orderId}', ['orderId' => $order_id]),
                    'product_code' => time(),
                    'price_payout' => $paymentInfo['data']['sub_total'],
                    'shipping_information' => $paymentInfo['OrderBilling']
                        ], (strtolower($paymentInfo['data']['currency_code']) == 'usd') ? $paymentInfo['data']['total_amount'] : ((float) ($paymentInfo['data']['total_amount'] * Yii::$app->params['currency_rate'][strtolower($paymentInfo['data']['currency_code'])])), $paymentInfo['data']['sub_total'], $paymentInfo['data']['currency_code'], $order_id, $returnUrl, $cancelUrl, (float) $paymentInfo['data']['shipping_fee']);

        if (empty($iFrame)) {
            return $this->redirect(['checkout/confirm']);
        }

        $hasErrorMsg = (Yii::$app->request->get('error', '') == 'invalid_card');

        return $this->render('summary.twig', [
                    'errorMessage' => $hasErrorMsg ? Yii::t('app', "Your credit card details could not be verified. Please try again.") : "",
                    'full_payment' => 0,
                    'products' => $products,
                    'uploads_url' => \app\models\Option::getConfig('upload_url'),
                    'sub_total_payment' => $paymentInfo['data']['sub_total'],
                    'shipping_fee' => $paymentInfo['data']['shipping_fee'],
                    'tax' => $paymentInfo['data']['tax'],
                    'total_payment' => $paymentInfo['data']['total_amount'],
                    'voucher_amount' => $paymentInfo['data']['voucher'],
                    'billing' => $paymentInfo['OrderBilling'],
                    'delivery' => $paymentInfo['OrderDelivery'],
                    'delivery_method' => $paymentInfo['DeliveryMethod']['delivery'],
                    'payment_option' => Yii::t('app', 'Credit Card (checkout.com)'),
                    'country' => $countryName,
                    'country2' => $countryName2,
                    'is_purchase_in_installment' => $is_purchase_in_installment,
                    'installment_pre_pay' => $installment_pre_pay,
                    'remaining_installment' => $remaining_installment,
                    'payment_made' => $payment_made,
                    'iframe' => $iFrame
        ]);
    }

    public function actionGuestAddCart($id = 0) {
        $cart = Cart::find()->where(['product_id' => $id])->one();
        if (empty($cart)) { /* no one add anything to cart -> allow create new */
            $cart = new Cart();
            $cart->product_id = $id;
            $cart->quantity = 1;
            $cart->user_id = 0;
            $cart->created = time();
            $cart->save();
            echo "success";
        } else { /* else means product is already inside somebody's cart */
            echo "fail";
        }
        exit();
    }

    public function actionGuestRemoveCart($id = 0) {
        // $cart = Cart::find()->where(['product_id'=>$id])->one();
        $cart = Cart::find()->where(['product_id' => $id])->all();
        if (!empty($cart)) {
            // Cart::deleteAll(['product_id'=>$id,'user_id'=>0]);
            Cart::deleteAll(['product_id' => $id]);
        }
        return Common::jsonOut('Ok');
    }

    public function actionCurrentCurrency() {
        $currency = 'USD';
        if (function_exists('getallheaders')) {
            $data = getallheaders();
            if (isset($data['X-Geoip']) && !empty($data['X-Geoip'])) {
                $listcountry = array("AF" => "AFN", "AL" => "ALL", "DZ" => "DZD", "AS" => "USD", "AD" => "EUR", "AO" => "AOA", "AI" => "XCD", "AG" => "XCD", "AR" => "ARP", "AM" => "AMD", "AW" => "AWG", "AU" => "AUD", "AT" => "EUR", "AZ" => "AZN", "BS" => "BSD", "BH" => "BHD", "BD" => "BDT", "BB" => "BBD", "BY" => "BYR", "BE" => "EUR", "BZ" => "BZD", "BJ" => "XOF", "BM" => "BMD", "BT" => "BTN", "BO" => "BOV", "BA" => "BAM", "BW" => "BWP", "BV" => "NOK", "BR" => "BRL", "IO" => "USD", "BN" => "BND", "BG" => "BGL", "BF" => "XOF", "BI" => "BIF", "KH" => "KHR", "CM" => "XAF", "CA" => "CAD", "CV" => "CVE", "KY" => "KYD", "CF" => "XAF", "TD" => "XAF", "CL" => "CLF", "CN" => "CNY", "CX" => "AUD", "CC" => "AUD", "CO" => "COU", "KM" => "KMF", "CG" => "XAF", "CD" => "CDF", "CK" => "NZD", "CR" => "CRC", "HR" => "HRK", "CU" => "CUP", "CY" => "EUR", "CZ" => "CZK", "CS" => "CSJ", "CI" => "XOF", "DK" => "DKK", "DJ" => "DJF", "DM" => "XCD", "DO" => "DOP", "EC" => "USD", "EG" => "EGP", "SV" => "USD", "GQ" => "EQE", "ER" => "ERN", "EE" => "EEK", "ET" => "ETB", "FK" => "FKP", "FO" => "DKK", "FJ" => "FJD", "FI" => "FIM", "FR" => "XFO", "GF" => "EUR", "PF" => "XPF", "TF" => "EUR", "GA" => "XAF", "GM" => "GMD", "GE" => "GEL", "DD" => "DDM", "DE" => "EUR", "GH" => "GHC", "GI" => "GIP", "GR" => "GRD", "GL" => "DKK", "GD" => "XCD", "GP" => "EUR", "GU" => "USD", "GT" => "GTQ", "GN" => "GNE", "GW" => "GWP", "GY" => "GYD", "HT" => "USD", "HM" => "AUD", "VA" => "EUR", "HN" => "HNL", "HK" => "HKD", "HU" => "HUF", "IS" => "ISJ", "IN" => "INR", "ID" => "IDR", "IR" => "IRR", "IQ" => "IQD", "IE" => "IEP", "IL" => "ILS", "IT" => "ITL", "JM" => "JMD", "JP" => "JPY", "JO" => "JOD", "KZ" => "KZT", "KE" => "KES", "KI" => "AUD", "KP" => "KPW", "KR" => "KRW", "KW" => "KWD", "KG" => "KGS", "LA" => "LAJ", "LV" => "LVL", "LB" => "LBP", "LS" => "ZAR", "LR" => "LRD", "LY" => "LYD", "LI" => "CHF", "LT" => "LTL", "LU" => "LUF", "MO" => "MOP", "MK" => "MKN", "MG" => "MGF", "MW" => "MWK", "MY" => "MYR", "MV" => "MVR", "ML" => "MAF", "MT" => "MTL", "MH" => "USD", "MQ" => "EUR", "MR" => "MRO", "MU" => "MUR", "YT" => "EUR", "MX" => "MXV", "FM" => "USD", "MD" => "MDL", "MC" => "MCF", "MN" => "MNT", "ME" => "EUR", "MS" => "XCD", "MA" => "MAD", "MZ" => "MZM", "MM" => "MMK", "NA" => "ZAR", "NR" => "AUD", "NP" => "NPR", "NL" => "NLG", "AN" => "ANG", "NC" => "XPF", "NZ" => "NZD", "NI" => "NIO", "NE" => "XOF", "NG" => "NGN", "NU" => "NZD", "NF" => "AUD", "MP" => "USD", "NO" => "NOK", "OM" => "OMR", "PK" => "PKR", "PW" => "USD", "PA" => "USD", "PG" => "PGK", "PY" => "PYG", "YD" => "YDD", "PE" => "PEH", "PH" => "PHP", "PN" => "NZD", "PL" => "PLN", "PT" => "TPE", "PR" => "USD", "QA" => "QAR", "RO" => "ROK", "RU" => "RUB", "RW" => "RWF", "RE" => "EUR", "SH" => "SHP", "KN" => "XCD", "LC" => "XCD", "PM" => "EUR", "VC" => "XCD", "WS" => "WST", "SM" => "EUR", "ST" => "STD", "SA" => "SAR", "SN" => "XOF", "RS" => "CSD", "SC" => "SCR", "SL" => "SLL", "SG" => "SGD", "SK" => "SKK", "SI" => "SIT", "SB" => "SBD", "SO" => "SOS", "ZA" => "ZAL", "ES" => "ESB", "LK" => "LKR", "SD" => "SDG", "SR" => "SRG", "SJ" => "NOK", "SZ" => "SZL", "SE" => "SEK", "CH" => "CHW", "SY" => "SYP", "TW" => "TWD", "TJ" => "TJR", "TZ" => "TZS", "TH" => "THB", "TL" => "USD", "TG" => "XOF", "TK" => "NZD", "TO" => "TOP", "TT" => "TTD", "TN" => "TND", "TR" => "TRL", "TM" => "TMM", "TC" => "USD", "TV" => "AUD", "SU" => "SUR", "UG" => "UGS", "UA" => "UAK", "AE" => "AED", "GB" => "GBP", "US" => "USS", "UM" => "USD", "UY" => "UYI", "UZ" => "UZS", "VU" => "VUV", "VE" => "VEB", "VN" => "VNC", "VG" => "USD", "VI" => "USD", "WF" => "XPF", "EH" => "MAD", "YE" => "YER", "YU" => "YUM", "ZR" => "ZRZ", "ZM" => "ZMK", "ZW" => "ZWC");
                $availableCurrency = array('USD', 'LBP', 'BHD', 'JOD', 'QAR', 'SAR', 'AED', 'OMR');
                if (in_array($listcountry[$data['X-Geoip']], $availableCurrency)) {
                    $currency = $listcountry[$data['X-Geoip']];
                }
            }
        }
        echo trim($currency);
        die();
    }

}

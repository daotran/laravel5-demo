<?php

namespace app\components\paypal;

namespace App\Http\Controllers;

use app\components\paypal\Paypal;

class PaypalController extends Controller {

    public $enableCsrfValidation = false;

    public function ppNotiFromPpprivateFundtion() {
        return Paypal::processNotification();
    }

}

?>
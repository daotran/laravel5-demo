<?php

return array(
    // set your paypal credential
    'client_id' => 'AQoBzeGrkzeERt8dk224bokoCpB_8dnlxMm07d8HdIXTdbuCWxQHkhPQyRHyS4aTaYScG4UnDC8CGQUZ',
    'secret' => 'EE0Itlb8NeeCDJ3PpMmorgC7ZkZjnRah0BHB-KoaJOWaK20s6_wkdCUiTg6Q4HjWS842KKhRWGog3Dc-',
    /**
     * SDK configuration 
     */
    'settings' => array(
        /**
         * Available option 'sandbox' or 'live'
         */
        'mode' => 'sandbox',
        /**
         * Specify the max request time in seconds
         */
        'http.ConnectionTimeOut' => 30,
        /**
         * Whether want to log to a file
         */
        'log.LogEnabled' => true,
        /**
         * Specify the file that want to write on
         */
        'log.FileName' => storage_path() . '/logs/paypal.log',
        /**
         * Available option 'FINE', 'INFO', 'WARN' or 'ERROR'
         *
         * Logging is most verbose in the 'FINE' level and decreases as you
         * proceed towards ERROR
         */
        'log.LogLevel' => 'ERROR'
    ),
);

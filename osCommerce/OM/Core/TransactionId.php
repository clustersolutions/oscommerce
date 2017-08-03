<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright (c) 2017 osCommerce; https://www.oscommerce.com
 * @license BSD; https://www.oscommerce.com/license/bsd.txt
 */

namespace osCommerce\OM\Core;

class TransactionId
{
    public static function get(string $key): int
    {
        $data = [
            'key' => $key
        ];

        return OSCOM::callDB('GetTransactionId', $data, 'Core');
    }
}

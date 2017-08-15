<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright (c) 2017 osCommerce; https://www.oscommerce.com
 * @license BSD; https://www.oscommerce.com/license/bsd.txt
 */

namespace osCommerce\OM\Core;

class Sanitize
{
    public static function simple(string $value = null): string
    {
        if (!isset($value)) {
            return '';
        }

        return trim(str_replace([
            "\r\n",
            "\n",
            "\r"
        ], '', $value));
    }
}

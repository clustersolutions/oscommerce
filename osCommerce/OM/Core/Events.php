<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2015 osCommerce; http://www.oscommerce.com
 * @license BSD; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core;

class Events
{
    protected static $data = [];

    public static function watch(string $event, $function)
    {
        static::$data[$event][] = $function;
    }

    public static function fire(string $event, array $parameters = [])
    {
        if (isset(static::$data[$event])) {
            foreach (static::$data[$event] as $f) {
                call_user_func($f, $parameters);
            }
        }
    }
}

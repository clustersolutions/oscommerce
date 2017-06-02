<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright (c) 2015 osCommerce; http://www.oscommerce.com
 * @license BSD; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core;

class Hash
{
    protected static $_drivers = [
      'Salt'
    ];

    public static function get(string $string, string $driver = null): string
    {
        if (!isset($driver)) {
            foreach (static::$_drivers as $d) {
                if (call_user_func(['osCommerce\\OM\\Core\\Hash\\' . $d, 'canUse'])) {
                    $driver = $d;

                    break;
                }
            }
        }

        return call_user_func(['osCommerce\\OM\\Core\\Hash\\' . $driver, 'get'], $string);
    }

    public static function validate(string $plain, string $hashed, string $driver = null): bool
    {
        if (!isset($driver)) {
            foreach (static::$_drivers as $d) {
                if (call_user_func(['osCommerce\\OM\\Core\\Hash\\' . $d, 'canUse'])) {
                    $driver = $d;

                    break;
                }
            }
        }

        return call_user_func(['osCommerce\\OM\\Core\\Hash\\' . $driver, 'validate'], $plain, $hashed);
    }

    public static function getRandomString(int $length, string $type = 'mixed'): string
    {
        if (!in_array($type, ['mixed', 'chars', 'digits'])) {
            trigger_error('osCommerce\\OM\\Core\\Hash::getRandomString() $type not recognized:' . $type, E_USER_ERROR);

            return '';
        }

        if ($length < 1) {
            trigger_error('osCommerce\\OM\\Core\\Hash::getRandomString() $length must be 1 or higher value', E_USER_ERROR);

            return '';
        }

        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $digits = '0123456789';

        $base = '';

        if (($type == 'mixed') || ($type == 'chars')) {
            $base .= $chars;
        }

        if (($type == 'mixed') || ($type == 'digits')) {
            $base .= $digits;
        }

        $base_length = strlen($base) - 1;

        $rand_value = '';

        for ($i = 0; $i < $length; $i++) {
            $rand_value .= $base[random_int(0, $base_length)];
        }

        return $rand_value;
    }
}

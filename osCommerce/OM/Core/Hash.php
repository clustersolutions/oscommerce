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

        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $digits = '0123456789';

        $base = '';

        if (($type == 'mixed') || ($type == 'chars')) {
            $base .= $chars;
        }

        if (($type == 'mixed') || ($type == 'digits')) {
            $base .= $digits;
        }

        $rand_value = '';

        do {
           $random = base64_encode(random_bytes($length));

            for ($i = 0, $n = strlen($random); $i < $n; $i++) {
                $char = substr($random, $i, 1);

                if (strpos($base, $char) !== false) {
                    $rand_value .= $char;
                }
            }
        } while (strlen($rand_value) < $length);

        if (strlen($rand_value) > $length) {
            $rand_value = substr($rand_value, 0, $length);
        }

        return $rand_value;
    }
}

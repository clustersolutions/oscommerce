<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright (c) 2017 osCommerce; https://www.oscommerce.com
 * @license BSD; https://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core;

use osCommerce\OM\Core\OSCOM;

class AuditLog
{
    public static function save($data)
    {
        if (!isset($data['site'])) {
            $data['site'] = OSCOM::getSite();
        }

        if (!isset($data['application'])) {
            $data['application'] = OSCOM::getSiteApplication();
        }

        if (!isset($data['action'])) {
            $data['action'] = null;
        }

        OSCOM::callDB('SaveAuditLog', $data, 'Core');
    }

    public static function getAll($req, $id, $limit = 10)
    {
        $sig = explode('\\', $req, 3);

        $data = [
            'site' => $sig[0],
            'application' => $sig[1],
            'action' => $sig[2],
            'id' => $id,
            'limit' => $limit
        ];

        return OSCOM::callDB('GetAuditLog', $data, 'Core');
    }

    public static function getDiff(array $array1, array $array2)
    {
        $difference = [];

        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!array_key_exists($key, $array2) || !is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = static::getDiff($value, $array2[$key]);

                    if (!empty($new_diff)) {
                        $difference[$key] = $new_diff;
                    }
                }
            } else if (!array_key_exists($key, $array2) || ($array2[$key] !== $value)) {
                $difference[$key] = $value;
            }
        }

        return $difference;
    }
}

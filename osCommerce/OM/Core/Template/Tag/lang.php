<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright (c) 2016 osCommerce; http://www.oscommerce.com
 * @license BSD; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Template\Tag;

use osCommerce\OM\Core\OSCOM;

class lang extends \osCommerce\OM\Core\Template\TagAbstract
{
    public static function execute($string)
    {
        $args = func_get_args();

        $key = trim($string);
        $values = [];

        if (strpos($key, ' ') !== false) {
            $x = new \SimpleXMLElement('<' . $key . ' />');

            if (count($x->attributes()) > 0) {
                $key = $x->getName();

                foreach ($x->attributes() as $k => $v) {
                    $values[':' . $k] = (string)$v;
                }
            }
        }

        $result = OSCOM::getDef($key, $values);

        if (isset($args[1]) && !empty($args[1])) {
            $result = call_user_func(trim($args[1]), $result);
        }

        return $result;
    }
}

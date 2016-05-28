<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright (c) 2016 osCommerce; http://www.oscommerce.com
 * @license BSD; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Template\Tag;

use osCommerce\OM\Core\OSCOM;
use osCommerce\OM\Core\Registry;

class raw extends \osCommerce\OM\Core\Template\TagAbstract
{
    static protected $_parse_result = false;

    static public function execute($string)
    {
        $args = func_get_args();

        $OSCOM_Template = Registry::get('Template');

        if (strpos($string, ' ') === false) {
            $value = $OSCOM_Template->getValue($string);
        } else {
            list($array, $key) = explode(' ', $string, 2);

            $value = $OSCOM_Template->getValue($array)[$key];
        }

        if (isset($args[1]) && !empty($args[1])) {
            return call_user_func(trim($args[1]), $value);
        } else {
            return $value;
        }
    }
}

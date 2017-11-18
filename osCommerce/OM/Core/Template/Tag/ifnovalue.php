<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright (c) 2017 osCommerce; https://www.oscommerce.com
 * @license BSD; https://www.oscommerce.com/license/bsd.txt
 */

namespace osCommerce\OM\Core\Template\Tag;

use osCommerce\OM\Core\{
    OSCOM,
    Registry
};

class ifnovalue extends \osCommerce\OM\Core\Template\TagAbstract
{
    protected static $_parse_result = false;

    public static function execute($string)
    {
        $args = func_get_args();

        $OSCOM_Template = Registry::get('Template');

        $key = trim($args[1]);

        if (strpos($key, ' ') !== false) {
            list($key, $entry) = explode(' ', $key, 2);
        }

        if (!$OSCOM_Template->valueExists($key)) {
            if (class_exists('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Application\\' . OSCOM::getSiteApplication() . '\\Module\\Template\\Value\\' . $key . '\\Controller') && is_subclass_of('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Application\\' . OSCOM::getSiteApplication() . '\\Module\\Template\\Value\\' . $key . '\\Controller', 'osCommerce\\OM\\Core\\Template\\ValueAbstract')) {
                call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Application\\' . OSCOM::getSiteApplication() . '\\Module\\Template\\Value\\' . $key . '\\Controller', 'initialize'));
            } elseif (class_exists('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Module\\Template\\Value\\' . $key . '\\Controller') && is_subclass_of('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Module\\Template\\Value\\' . $key . '\\Controller', 'osCommerce\\OM\\Core\\Template\\ValueAbstract')) {
                call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Module\\Template\\Value\\' . $key . '\\Controller', 'initialize'));
            }
        }

        $has_value = false;
        $has_else = strpos($string, '{else}');

        $result = '';

        if ($OSCOM_Template->valueExists($key)) {
            $value = $OSCOM_Template->getValue($key);

            if (isset($entry) && is_array($value)) {
                if (isset($value[$entry]) && ((is_string($value[$entry]) && (strlen($value[$entry]) > 0)) || (is_array($value[$entry]) && (count($value[$entry]) > 0)))) {
                    $has_value = true;
                }
            } elseif ((is_string($value) && (strlen($value) > 0)) || (is_array($value) && (count($value) > 0))) {
                $has_value = true;
            }
        }

        if ($has_else !== false) {
            if ($has_value === false) {
                $result = substr($string, 0, $has_else);
            } else {
                $result = substr($string, $has_else + 6); // strlen('{else}')==6
            }
        } elseif ($has_value === false) {
            $result = $string;
        }

        return $result;
    }
}

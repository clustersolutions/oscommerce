<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright (c) 2016 osCommerce; http://www.oscommerce.com
 * @license BSD; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Template\Tag;

use osCommerce\OM\Core\Registry;

class import extends \osCommerce\OM\Core\Template\TagAbstract
{
    public static function execute($file): string
    {
        $result = '';

        if (!empty($file)) {
            if (file_exists($file)) {
// use only file_get_contents() when content pages no longer contain PHP; HPDL
                if (substr($file, strrpos($file, '.')+1) == 'html') {
                    $result = file_get_contents($file);
                } else {
                    $result = Registry::get('Template')->getContent($file);
                }
            } else {
                trigger_error('Template Tag {import}: File does not exist: ' . $file);
            }
        }

        return $result;
    }
}

<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright (c) 2017 osCommerce; https://www.oscommerce.com
 * @license BSD; https://www.oscommerce.com/license/bsd.txt
 */

namespace osCommerce\OM\Core\Template\Tag;

class json extends \osCommerce\OM\Core\Template\TagAbstract
{
    public static function execute($string)
    {
        return json_encode($string);
    }
}

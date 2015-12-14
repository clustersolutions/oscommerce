<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2015 osCommerce; http://www.oscommerce.com
 * @license BSD; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Template\Tag;

use osCommerce\OM\Core\HTML;

class formtoken extends \osCommerce\OM\Core\Template\TagAbstract
{
    static protected $_parse_result = false;

    static public function execute($string): string
    {
        $result = '';

        $value = raw::execute($string);

        if (!empty($value)) {
            $result = HTML::hiddenField($string, md5($value));
        }

        return $result;
    }
}

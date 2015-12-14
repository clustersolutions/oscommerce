<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright (c) 2015 osCommerce; http://www.oscommerce.com
 * @license BSD; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Site\Admin;

class Session extends \osCommerce\OM\Core\Session
{
    public static function load($name = null)
    {
        ini_set('session.use_cookies', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 0);
        ini_set('session.cookie_httponly', 1);

        return parent::load($name);
    }
}

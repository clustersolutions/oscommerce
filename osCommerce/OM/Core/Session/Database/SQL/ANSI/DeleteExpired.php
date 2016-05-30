<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright (c) 2016 osCommerce; https://www.oscommerce.com
 * @license BSD; https://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Session\Database\SQL\ANSI;

use osCommerce\OM\Core\Registry;

class DeleteExpired
{
    public static function execute(array $data): bool
    {
        $OSCOM_PDO = Registry::get('PDO');

        $Qsession = $OSCOM_PDO->prepare('delete from :table_sessions where expiry < :expiry');
        $Qsession->bindInt(':expiry', time() - $data['expiry']);
        $Qsession->execute();

        return $Qsession->isError() === false;
    }
}

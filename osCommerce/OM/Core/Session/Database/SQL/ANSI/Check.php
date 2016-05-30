<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright (c) 2016 osCommerce; https://www.oscommerce.com
 * @license BSD; https://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Session\Database\SQL\ANSI;

use osCommerce\OM\Core\Registry;

class Check
{
    public static function execute(array $data): bool
    {
        $OSCOM_PDO = Registry::get('PDO');

        $Qsession = $OSCOM_PDO->prepare('select 1 from :table_sessions where id = :id');
        $Qsession->bindValue(':id', $data['id']);
        $Qsession->execute();

        return $Qsession->fetch() !== false;
    }
}

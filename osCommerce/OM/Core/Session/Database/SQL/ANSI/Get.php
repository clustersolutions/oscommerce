<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2015 osCommerce; http://www.oscommerce.com
 * @license BSD; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Session\Database\SQL\ANSI;

use osCommerce\OM\Core\Registry;

class Get
{
    public static function execute(array $data)
    {
        $OSCOM_PDO = Registry::get('PDO');

        $Qsession = $OSCOM_PDO->prepare('select value from :table_sessions where id = :id limit 1');
        $Qsession->bindValue(':id', $data['id']);
        $Qsession->execute();

        return $Qsession->fetch();
    }
}

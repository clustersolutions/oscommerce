<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright (c) 2016 osCommerce; https://www.oscommerce.com
 * @license BSD; https://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Session\Database\SQL\MySQL\Standard;

use osCommerce\OM\Core\{
    OSCOM,
    Registry
};

class Save
{
    public static function execute(array $data): bool
    {
        $OSCOM_PDO = Registry::get('PDO');

        $Qsession = $OSCOM_PDO->prepare('insert into :table_sessions (id, expiry, value) values (:id, :expiry, :value) on duplicate key update expiry = values(expiry), value = values(value)');
        $Qsession->bindValue(':id', $data['id']);
        $Qsession->bindInt(':expiry', $data['expiry']);
        $Qsession->bindValue(':value', $data['value']);
        $Qsession->execute();

        return $Qsession->isError() === false;
    }
}

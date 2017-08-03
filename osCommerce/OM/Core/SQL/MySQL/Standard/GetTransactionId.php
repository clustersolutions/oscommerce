<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright (c) 2017 osCommerce; https://www.oscommerce.com
 * @license BSD; https://www.oscommerce.com/license/bsd.txt
 */

namespace osCommerce\OM\Core\SQL\MySQL\Standard;

use osCommerce\OM\Core\Registry;

class GetTransactionId
{
    public static function execute($data): int
    {
        $OSCOM_PDO = Registry::get('PDO');

        $OSCOM_PDO->beginTransaction();

        $Qv = $OSCOM_PDO->prepare('select tx_value from :table_transaction_ids where tx_key = :tx_key for update');
        $Qv->bindValue(':tx_key', $data['key']);
        $Qv->execute();

        $value = $Qv->valueInt('tx_value');

        $Qv = $OSCOM_PDO->prepare('update :table_transaction_ids set tx_value = tx_value+1 where tx_key = :tx_key');
        $Qv->bindValue(':tx_key', $data['key']);
        $Qv->execute();

        $OSCOM_PDO->commit();

        return $value;
    }
}

<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2014 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\SQL\ANSI;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.3
 */

  class GetAuditLog {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qaudit = $OSCOM_PDO->prepare('select * from :table_audit_log where site = :site and application = :application and action = :action and row_id = :row_id order by date_added desc limit :limit');
      $Qaudit->bindValue(':site', $data['site']);
      $Qaudit->bindValue(':application', $data['application']);
      $Qaudit->bindValue(':action', $data['action']);
      $Qaudit->bindInt(':row_id', $data['id']);
      $Qaudit->bindInt(':limit', $data['limit']);
      $Qaudit->execute();

      $result = $Qaudit->fetchAll();

      foreach ( $result as $key => $value ) {
        $Qrecords = $OSCOM_PDO->prepare('select row_key, old_value, new_value from :table_audit_log_rows where audit_log_id = :audit_log_id order by row_key');
        $Qrecords->bindInt(':audit_log_id', $value['id']);
        $Qrecords->execute();

        $result[$key]['rows'] = $Qrecords->fetchAll();
      }

      return $result;
    }
  }
?>

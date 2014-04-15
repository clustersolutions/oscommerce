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

  class SaveAuditLog {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      try {
        $OSCOM_PDO->beginTransaction();

        $audit = [ 'site' => $data['site'],
                   'application' => $data['application'],
                   'action' => $data['action'],
                   'row_id' => $data['id'],
                   'user_id' => $data['user_id'],
                   'ip_address' => $data['ip_address'],
                   'action_type' => $data['action_type'],
                   'date_added' => 'now()' ];

        $OSCOM_PDO->save('audit_log', $audit);

        $audit_id = $OSCOM_PDO->lastInsertId();

        foreach ( $data['rows'] as $row ) {
          $record = [ 'audit_log_id' => $audit_id,
                      'row_key' => $row['key'],
                      'old_value' => $row['old'],
                      'new_value' => $row['new'] ];

          $OSCOM_PDO->save('audit_log_rows', $record);
        }

        $OSCOM_PDO->commit();

        return true;
      } catch ( \Exception $e ) {
        $OSCOM_PDO->rollBack();

        trigger_error($e->getMessage());
      }

      return false;
    }
  }
?>

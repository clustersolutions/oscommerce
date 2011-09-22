<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Dashboard\SQL\ANSI;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.3
 */

  class UpdateAppLastOpened {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qreset = $OSCOM_PDO->prepare('update :table_administrator_shortcuts set last_viewed = now() where administrators_id = :administrators_id and module = :module');
      $Qreset->bindInt(':administrators_id', $data['admin_id']);
      $Qreset->bindValue(':module', $data['application']);
      $Qreset->execute();

      return ( ($Qreset->rowCount() === 1) || !$Qreset->isError() );
    }
  }
?>

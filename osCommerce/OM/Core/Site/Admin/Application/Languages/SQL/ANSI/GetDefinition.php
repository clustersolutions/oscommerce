<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Languages\SQL\ANSI;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.3
 */

  class GetDefinition {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qdef = $OSCOM_PDO->prepare('select * from :table_languages_definitions where id = :id');
      $Qdef->bindInt(':id', $data['id']);
      $Qdef->execute();

      return $Qdef->fetch();
    }
  }
?>

<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\SQL\ANSI;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.3
 */

  class GetLanguages {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qlanguages = $OSCOM_PDO->query('select * from :table_languages order by sort_order, name');
      $Qlanguages->setCache('languages');
      $Qlanguages->execute();

      return $Qlanguages->fetchAll();
    }
  }
?>

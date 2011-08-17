<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\ProductAttributes\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.3
 */

  class GetAll {
    public static function execute() {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $Qpa = $OSCOM_PDO->prepare('select code from :table_templates_boxes where modules_group = :modules_group order by code');
      $Qpa->bindValue(':modules_group', 'ProductAttribute');
      $Qpa->execute();

      $result['entries'] = $Qpa->fetchAll();

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>

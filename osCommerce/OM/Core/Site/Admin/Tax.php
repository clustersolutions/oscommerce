<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.3
 */

  class Tax extends \osCommerce\OM\Core\Site\Shop\Tax {
    public function getTaxRate($class_id, $country_id = null, $zone_id = null) {
      if ( !isset($country_id) && !isset($zone_id)) {
        $country_id = STORE_COUNTRY;
        $zone_id = STORE_ZONE;
      }

      return parent::getTaxRate($class_id, $country_id, $zone_id);
    }

    public static function getClasses() {
      $OSCOM_PDO = Registry::get('PDO');

      $Qtc = $OSCOM_PDO->query('select tax_class_id, tax_class_title from :table_tax_class order by tax_class_title');
      $Qtc->execute();

      return $Qtc->fetchAll();
    }
  }
?>

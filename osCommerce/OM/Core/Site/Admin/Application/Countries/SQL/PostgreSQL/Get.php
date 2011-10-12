<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Countries\SQL\PostgreSQL;

  use osCommerce\OM\Core\Registry;

  class Get {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qcountry = $OSCOM_PDO->prepare('select c.*, (select count(*) from :table_zones z where z.zone_country_id = c.countries_id) as total_zones from :table_countries c where c.countries_id = :countries_id');
      $Qcountry->bindInt(':countries_id', $data['id']);
      $Qcountry->execute();

      return $Qcountry->fetch();
    }
  }
?>

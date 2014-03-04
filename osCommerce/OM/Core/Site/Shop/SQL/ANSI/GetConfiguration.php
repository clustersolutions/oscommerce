<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2014 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop\SQL\ANSI;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.3
 */

  class GetConfiguration {
    public static function execute() {
      $OSCOM_PDO = Registry::get('PDO');

      $Qcfg = $OSCOM_PDO->prepare('select configuration_key as cfgkey, configuration_value as cfgvalue from :table_configuration');
      $Qcfg->setCache('configuration');
      $Qcfg->execute();

      return $Qcfg->fetchAll();
    }
  }
?>

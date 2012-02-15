<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Configuration\SQL\PostgreSQL;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.3
 */

  class EntryFind {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array('entries' => array());

      $Qcfg = $OSCOM_PDO->prepare('select * from :table_configuration where configuration_group_id = :configuration_group_id and (configuration_key ilike :configuration_key or configuration_value ilike :configuration_value) order by sort_order, configuration_title');
      $Qcfg->bindInt(':configuration_group_id', $data['group_id']);
      $Qcfg->bindValue(':configuration_key', '%' . $data['search'] . '%');
      $Qcfg->bindValue(':configuration_value', '%' . $data['search'] . '%');
      $Qcfg->execute();

      $result['entries'] = $Qcfg->fetchAll();

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>

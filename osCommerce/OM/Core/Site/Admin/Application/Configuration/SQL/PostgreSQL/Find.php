<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Configuration\SQL\PostgreSQL;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.3
 */

  class Find {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      $Qgroups = $OSCOM_PDO->prepare('select cg.configuration_group_id, cg.configuration_group_title, count(c.configuration_id) as total_entries from :table_configuration_group cg, :table_configuration c where (c.configuration_key ilike :configuration_key or c.configuration_value ilike :configuration_value) and cg.visible = 1 and cg.configuration_group_id = c.configuration_group_id group by cg.configuration_group_id, cg.configuration_group_title, cg.sort_order order by cg.sort_order, cg.configuration_group_title');
      $Qgroups->bindValue(':configuration_key', '%' . $data['search'] . '%');
      $Qgroups->bindValue(':configuration_value', '%' . $data['search'] . '%');
      $Qgroups->execute();

      $result['entries'] = $Qgroups->fetchAll();

      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>

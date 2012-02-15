<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Configuration\Model;

  use osCommerce\OM\Core\OSCOM;

  class getAllEntries {
    public static function execute($group_id) {
      $data = array('group_id' => $group_id);

      $result = OSCOM::callDB('Admin\Configuration\EntryGetAll', $data);

      foreach ( $result['entries'] as &$row ) {
        if ( !empty($row['use_function']) ) {
          $row['configuration_value'] = callUserFunc::execute($row['use_function'], $row['configuration_value']);
        }
      }

      return $result;
    }
  }
?>

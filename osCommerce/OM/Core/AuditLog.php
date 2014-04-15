<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2014 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core;

  use osCommerce\OM\Core\OSCOM;

/**
 * @since v3.0.3
 */

  class AuditLog {
    public static function save($data) {
      if ( !isset($data['site']) ) {
        $data['site'] = OSCOM::getSite();
      }

      if ( !isset($data['application']) ) {
        $data['application'] = OSCOM::getSiteApplication();
      }

      if ( !isset($data['action']) ) {
        $data['action'] = null;
      }

      OSCOM::callDB('SaveAuditLog', $data, 'Core');
    }

    public static function getAll($req, $id, $limit = 10) {
      $sig = explode('\\', $req, 3);

      $data = [ 'site' => $sig[0],
                'application' => $sig[1],
                'action' => $sig[2],
                'id' => $id,
                'limit' => $limit ];

      return OSCOM::callDB('GetAuditLog', $data, 'Core');
    }
  }
?>

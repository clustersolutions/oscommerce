<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\ProductAttributes\RPC;

  use osCommerce\OM\Core\Site\Admin\Application\ProductAttributes\ProductAttributes;
  use osCommerce\OM\Core\Site\RPC\Controller as RPC;

/**
 * @since v3.0.3
 */

  class GetInstalled {
    public static function execute() {
      if ( !isset($_GET['search']) ) {
        $_GET['search'] = '';
      }

      if ( !empty($_GET['search']) ) {
        $result = ProductAttributes::findInstalled($_GET['search']);
      } else {
        $result = ProductAttributes::getInstalled();
      }

      $result['rpcStatus'] = RPC::STATUS_SUCCESS;

      echo json_encode($result);
    }
  }
?>

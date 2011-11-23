<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Products\RPC;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\RPC\Controller as RPC;
  use osCommerce\OM\Core\Site\Shop\Currencies;

/**
 * @since v3.0.3
 */

  class FormatCurrency {
    public static function execute() {
      if ( !Registry::exists('Currencies') ) {
        Registry::set('Currencies', new Currencies());
      }

      $OSCOM_Currencies = Registry::get('Currencies');

      $result = array('value' => $OSCOM_Currencies->format($_GET['value']),
                      'rpcStatus' => RPC::STATUS_SUCCESS);

      echo json_encode($result);
    }
  }
?>

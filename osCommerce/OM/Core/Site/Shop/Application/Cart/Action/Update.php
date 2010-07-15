<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Application\Cart\Action;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  class Update {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_ShoppingCart = Registry::get('ShoppingCart');

      if ( isset($_POST['products']) && is_array($_POST['products']) && !empty($_POST['products']) ) {
        foreach ( $_POST['products'] as $item_id => $quantity ) {
          if ( !is_numeric($item_id) || !is_numeric($quantity) ) {
            return false;
          }

          $OSCOM_ShoppingCart->update($item_id, $quantity);
        }
      }

      osc_redirect(OSCOM::getLink(null, 'Cart'));
    }
  }
?>

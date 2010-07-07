<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\OSCOM;
  use osCommerce\OM\Site\Shop\Address;
  use osCommerce\OM\Site\Shop\Tax;

  if ( $OSCOM_Customer->isLoggedOn() ) {
    $Qglobal = $OSCOM_Database->query('select global_product_notifications from :table_customers where customers_id =:customers_id');
    $Qglobal->bindInt(':customers_id', $OSCOM_Customer->getID());
    $Qglobal->execute();

    if ( $Qglobal->valueInt('global_product_notifications') !== 1 ) {
      $Qorder = $OSCOM_Database->query('select orders_id from :table_orders where customers_id = :customers_id order by date_purchased desc limit 1');
      $Qorder->bindInt(':customers_id', $OSCOM_Customer->getID());
      $Qorder->execute();

      $Qproducts = $OSCOM_Database->query('select products_id, products_name from :table_orders_products where orders_id = :orders_id order by products_name');
      $Qproducts->bindInt(':orders_id', $Qorder->valueInt('orders_id'));
      $Qproducts->execute();

      $products_array = array();
      while ( $Qproducts->next() ) {
        $products_array[] = array('id' => $Qproducts->valueInt('products_id'),
                                  'text' => $Qproducts->value('products_name'));
      }
    }
  }
?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<form name="order" action="<?php echo OSCOM::getLink(null, null, 'Success&UpdateNotifications', 'SSL'); ?>" method="post">

<div>
  <div style="float: left;"><?php echo osc_image(DIR_WS_IMAGES . 'table_background_man_on_board.gif', $OSCOM_Template->getPageTitle()); ?></div>

  <div style="padding-top: 30px;">
    <p><?php echo OSCOM::getDef('order_processed_successfully'); ?></p>

    <p>

<?php
  if ( $OSCOM_Customer->isLoggedOn() ) {
    if ( $Qglobal->valueInt('global_product_notifications') != 1 ) {
      echo OSCOM::getDef('add_selection_to_product_notifications') . '<br /><p class="productsNotifications">';

      $products_displayed = array();

      foreach ( $products_array as $product ) {
        if ( !in_array($product['id'], $products_displayed) ) {
          echo osc_draw_checkbox_field('notify[]', $product['id']) . ' ' . $product['text'] . '<br />';

          $products_displayed[] = $product['id'];
        }
      }
    } else {
      echo sprintf(OSCOM::getDef('view_order_history'), OSCOM::getLink(null, 'Account', null, 'SSL'), OSCOM::getLink(null, 'Account', 'Orders', 'SSL')) . '<br /><br />' . sprintf(OSCOM::getDef('contact_store_owner'), OSCOM::getLink(null, 'Info', 'Contact'));
    }
  }
?>

    </p>

    <h2 style="text-align: center;"><?php echo OSCOM::getDef('thanks_for_shopping_with_us'); ?></h2>
  </div>
</div>

<div class="submitFormButtons" style="text-align: right;">
  <?php echo osc_draw_image_submit_button('button_continue.gif', OSCOM::getDef('button_continue')); ?>
</div>

<?php
// HPDL
/*  if (DOWNLOAD_ENABLED == '1') {
?>

<table border="0" width="100%" cellspacing="0" cellpadding="0">

<?php
  include('includes/modules/downloads.php');
?>

</table>

<?php
  }
*/
?>

</form>

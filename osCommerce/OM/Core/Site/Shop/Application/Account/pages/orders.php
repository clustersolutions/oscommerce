<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\Order;
  use osCommerce\OM\Core\DateTime;
?>

<?php echo osc_image(DIR_WS_IMAGES . $OSCOM_Template->getPageImage(), $OSCOM_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists('Account') ) {
    echo $OSCOM_MessageStack->get('Account');
  }

  if ( Order::numberOfEntries() > 0 ) {
    $Qhistory = Order::getListing(MAX_DISPLAY_ORDER_HISTORY);

    while ( $Qhistory->next() ) {
      if ( !osc_empty($Qhistory->value('delivery_name')) ) {
        $order_type = OSCOM::getDef('order_shipped_to');
        $order_name = $Qhistory->value('delivery_name');
      } else {
        $order_type = OSCOM::getDef('order_billed_to');
        $order_name = $Qhistory->value('billing_name');
      }
?>

<div class="moduleBox">
  <span style="float: right;"><h6><?php echo OSCOM::getDef('order_status') . ' ' . $Qhistory->value('orders_status_name'); ?></h6></span>

  <h6><?php echo OSCOM::getDef('order_number') . ' ' . $Qhistory->valueInt('orders_id'); ?></h6>

  <div class="content">
    <table border="0" width="100%" cellspacing="2" cellpadding="4">
      <tr>
        <td width="50%" valign="top"><?php echo '<b>' . OSCOM::getDef('order_date') . '</b> ' . DateTime::getLong($Qhistory->value('date_purchased')) . '<br /><b>' . $order_type . '</b> ' . osc_output_string_protected($order_name); ?></td>
        <td width="30%" valign="top"><?php echo '<b>' . OSCOM::getDef('order_products') . '</b> ' . Order::numberOfProducts($Qhistory->valueInt('orders_id')) . '<br /><b>' . OSCOM::getDef('order_cost') . '</b> ' . strip_tags($Qhistory->value('order_total')); ?></td>
        <td width="20%"><?php echo osc_link_object(OSCOM::getLink(null, null, 'Orders=' . $Qhistory->valueInt('orders_id') . (isset($_GET['page']) ? '&page=' . $_GET['page'] : ''), 'SSL'), osc_draw_image_button('small_view.gif', OSCOM::getDef('button_view'))); ?></td>
      </tr>
    </table>
  </div>
</div>

<?php
    }
?>

<div class="listingPageLinks">
  <span style="float: right;"><?php echo $Qhistory->getBatchPageLinks(); ?></span>

  <?php echo $Qhistory->getBatchTotalPages(OSCOM::getDef('result_set_number_of_orders')); ?>
</div>

<?php
  } else {
?>

<div class="moduleBox">
  <div class="content">
    <?php echo OSCOM::getDef('no_orders_made_yet'); ?>
  </div>
</div>

<?php
  }
?>

<div class="submitFormButtons">
  <?php echo osc_link_object(OSCOM::getLink(null, null, null, 'SSL'), osc_draw_image_button('button_back.gif', OSCOM::getDef('button_back'))); ?>
</div>

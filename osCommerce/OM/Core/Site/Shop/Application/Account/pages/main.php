<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\OSCOM;
?>

<?php echo osc_image(DIR_WS_IMAGES . $OSCOM_Template->getPageImage(), $OSCOM_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists('Account') ) {
    echo $OSCOM_MessageStack->get('Account');
  }
?>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('my_account_title'); ?></h6>

  <div class="content">
    <?php echo osc_image(DIR_WS_IMAGES . 'account_personal.gif', OSCOM::getDef('my_account_title'), null, null, 'style="float: left;"'); ?>

    <ul style="padding-left: 100px; list-style-image: url(<?php echo osc_href_link(DIR_WS_IMAGES . 'arrow_green.gif', null, 'SSL'); ?>);">
      <li><?php echo osc_link_object(OSCOM::getLink(null, null, 'Edit', 'SSL'), OSCOM::getDef('my_account_information')); ?></li>
      <li><?php echo osc_link_object(OSCOM::getLink(null, null, 'AddressBook', 'SSL'), OSCOM::getDef('my_account_address_book')); ?></li>
      <li><?php echo osc_link_object(OSCOM::getLink(null, null, 'Password', 'SSL'), OSCOM::getDef('my_account_password')); ?></li>
    </ul>

    <div style="clear: both;"></div>
  </div>
</div>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('my_orders_title'); ?></h6>

  <div class="content">
    <?php echo osc_image(DIR_WS_IMAGES . 'account_orders.gif', OSCOM::getDef('my_orders_title'), null, null, 'style="float: left;"'); ?>

    <ul style="padding-left: 100px; list-style-image: url(<?php echo osc_href_link(DIR_WS_IMAGES . 'arrow_green.gif', null, 'SSL'); ?>);">
      <li><?php echo osc_link_object(OSCOM::getLink(null, null, 'Orders', 'SSL'), OSCOM::getDef('my_orders_view')); ?></li>
    </ul>

    <div style="clear: both;"></div>
  </div>
</div>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('my_notifications_title'); ?></h6>

  <div class="content">
    <?php echo osc_image(DIR_WS_IMAGES . 'account_notifications.gif', OSCOM::getDef('my_notifications_title'), null, null, 'style="float: left;"'); ?>

    <ul style="padding-left: 100px; list-style-image: url(<?php echo osc_href_link(DIR_WS_IMAGES . 'arrow_green.gif', null, 'SSL'); ?>);">
      <li><?php echo osc_link_object(OSCOM::getLink(null, null, 'Newsletters', 'SSL'), OSCOM::getDef('my_notifications_newsletters')); ?></li>
      <li><?php echo osc_link_object(OSCOM::getLink(null, null, 'Notifications', 'SSL'), OSCOM::getDef('my_notifications_products')); ?></li>
    </ul>

    <div style="clear: both;"></div>
  </div>
</div>

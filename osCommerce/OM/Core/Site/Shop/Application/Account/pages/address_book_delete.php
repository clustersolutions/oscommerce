<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Site\Shop\AddressBook;
  use osCommerce\OM\OSCOM;
  use osCommerce\OM\Site\Shop\Address;

  $Qentry = AddressBook::getEntry($_GET['Delete']);
?>

<?php echo osc_image(DIR_WS_IMAGES . $OSCOM_Template->getPageImage(), $OSCOM_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists('AddressBook') ) {
    echo $OSCOM_MessageStack->get('AddressBook');
  }
?>

<form name="address_book" action="<?php echo OSCOM::getLink(null, null, 'AddressBook&Delete=' . $_GET['Delete'] . '&Process', 'SSL'); ?>" method="post">

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('address_book_delete_address_title'); ?></h6>

  <div class="content">
    <div style="float: right; padding: 0px 0px 10px 20px;">
      <?php echo Address::format($_GET['Delete'], '<br />'); ?>
    </div>

    <div style="float: right; padding: 0px 0px 10px 20px; text-align: center;">
      <?php echo '<b>' . OSCOM::getDef('selected_address_title') . '</b><br />' . osc_image(DIR_WS_IMAGES . 'arrow_south_east.gif'); ?>
    </div>

    <?php echo OSCOM::getDef('address_book_delete_address_description'); ?>

    <div style="clear: both;"></div>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo osc_draw_image_submit_button('button_delete.gif', OSCOM::getDef('button_delete')); ?></span>

  <?php echo osc_link_object(OSCOM::getLink(null, null, 'AddressBook', 'SSL'), osc_draw_image_button('button_back.gif', OSCOM::getDef('button_back'))); ?>
</div>

</form>

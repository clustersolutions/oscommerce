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

  if ( isset($_GET['Edit']) ) {
    $Qentry = AddressBook::getEntry($_GET['Edit']); // HPDL conflict with $osC_oiAddress
  } else {
    if ( AddressBook::numberOfEntries() >= MAX_ADDRESS_BOOK_ENTRIES ) {
      $OSCOM_MessageStack->add('AddressBook', OSCOM::getDef('error_address_book_full'));
    }
  }
?>

<?php echo osc_image(DIR_WS_IMAGES . $OSCOM_Template->getPageImage(), $OSCOM_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists('AddressBook') ) {
    echo $OSCOM_MessageStack->get('AddressBook');
  }

  if ( ($OSCOM_Customer->hasDefaultAddress() === false) || (isset($_GET['Create']) && (AddressBook::numberOfEntries() < MAX_ADDRESS_BOOK_ENTRIES)) || (isset($Qentry) && ($Qentry->numberOfRows() === 1)) ) {
?>

<form name="address_book" action="<?php echo OSCOM::getLink(null, null, 'AddressBook&' . (isset($_GET['Edit']) ? 'Edit=' . $_GET['Edit'] : 'Create') . '&Process', 'SSL'); ?>" method="post" onsubmit="return check_form(address_book);">

<div class="moduleBox">
  <em style="float: right; margin-top: 10px;"><?php echo OSCOM::getDef('form_required_information'); ?></em>

  <h6><?php echo OSCOM::getDef('address_book_new_address_title'); ?></h6>

  <div class="content">

<?php
    include('includes/modules/address_book_details.php');
?>

  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo osc_draw_image_submit_button('button_continue.gif', OSCOM::getDef('button_continue')); ?></span>

<?php
    if ( $OSCOM_NavigationHistory->hasSnapshot() ) {
      $back_link = $OSCOM_NavigationHistory->getSnapshotURL();
    } elseif ( $OSCOM_Customer->hasDefaultAddress() === false ) {
      $back_link = OSCOM::getLink(null, null, null, 'SSL');
    } else {
      $back_link = OSCOM::getLink(null, null, 'AddressBook', 'SSL');
    }

    echo osc_link_object($back_link, osc_draw_image_button('button_back.gif', OSCOM::getDef('button_back')));
?>

</div>

</form>

<?php
  } else {
?>

<div class="submitFormButtons">
  <?php osc_link_object(OSCOM::getLink(null, null, 'AddressBook', 'SSL'), osc_draw_image_button('button_back.gif', OSCOM::getDef('button_back'))); ?>
</div>

<?php
  }
?>

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
  use osCommerce\OM\Site\Shop\AddressBook;
?>

<?php echo osc_image(DIR_WS_IMAGES . $OSCOM_Template->getPageImage(), $OSCOM_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists('AddressBook') ) {
    echo $OSCOM_MessageStack->get('AddressBook');
  }
?>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('primary_address_title'); ?></h6>

  <div class="content">
    <div style="float: right; padding: 0px 0px 10px 20px;">
      <?php echo Address::format($OSCOM_Customer->getDefaultAddressID(), '<br />'); ?>
    </div>

    <div style="float: right; padding: 0px 0px 10px 20px; text-align: center;">
      <?php echo '<b>' . OSCOM::getDef('primary_address_title') . '</b><br />' . osc_image(DIR_WS_IMAGES . 'arrow_south_east.gif'); ?>
    </div>

    <?php echo OSCOM::getDef('primary_address_description'); ?>

    <div style="clear: both;"></div>
  </div>
</div>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('address_book_title'); ?></h6>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  $Qaddresses = AddressBook::getListing();

  while ( $Qaddresses->next() ) {
?>

      <tr class="moduleRow" onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
        <td>
          <b><?php echo $Qaddresses->valueProtected('firstname') . ' ' . $Qaddresses->valueProtected('lastname'); ?></b>

<?php
    if ( $Qaddresses->valueInt('address_book_id') == $OSCOM_Customer->getDefaultAddressID() ) {
      echo '&nbsp;<small><i>' . OSCOM::getDef('primary_address_marker') . '</i></small>';
    }
?>

        </td>
        <td align="right"><?php echo osc_link_object(OSCOM::getLink(null, null, 'AddressBook=' . $Qaddresses->valueInt('address_book_id') . '&Edit', 'SSL'), osc_draw_image_button('small_edit.gif', OSCOM::getDef('button_edit'))) . '&nbsp;' . osc_link_object(OSCOM::getLink(null, null, 'AddressBook=' . $Qaddresses->valueInt('address_book_id') . '&Delete', 'SSL'), osc_draw_image_button('small_delete.gif', OSCOM::getDef('button_delete'))); ?></td>
      </tr>
      <tr>
        <td colspan="2" style="padding: 0px 0px 10px 10px;"><?php echo Address::format($Qaddresses->toArray(), '<br />'); ?></td>
      </tr>

<?php
  }
?>

    </table>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;">

<?php
  if ( $Qaddresses->numberOfRows() < MAX_ADDRESS_BOOK_ENTRIES ) {
    echo osc_link_object(OSCOM::getLink(null, null, 'AddressBook&New', 'SSL'), osc_draw_image_button('button_add_address.gif', OSCOM::getDef('button_add_address')));
  } else {
    echo sprintf(OSCOM::getDef('address_book_maximum_entries'), MAX_ADDRESS_BOOK_ENTRIES);
  }
?>

  </span>

  <?php echo osc_link_object(OSCOM::getLink(null, null, null, 'SSL'), osc_draw_image_button('button_back.gif', OSCOM::getDef('button_back'))); ?>
</div>

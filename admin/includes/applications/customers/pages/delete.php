<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Customers_Admin::getData($_GET['cID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . osc_output_string_protected($osC_ObjectInfo->get('customers_full_name')); ?></div>
<div class="infoBoxContent">
  <form name="cDelete" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $osC_ObjectInfo->get('customers_id') . '&search=' . $_GET['search'] . '&page=' . $_GET['page'] . '&action=delete'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_delete_customer'); ?></p>

  <p>

<?php
  $customer_name = osc_output_string_protected($osC_ObjectInfo->get('customers_full_name'));

  if ( $osC_ObjectInfo->get('total_reviews') > 0 ) {
    $customer_name .= ' (' . sprintf($osC_Language->get('total_reviews'), $osC_ObjectInfo->get('total_reviews')) . ')';
  }

  echo '<b>' . $customer_name . '</b>';
?>

  </p>

<?php
  if ( $osC_ObjectInfo->get('total_reviews') > 0 ) {
    echo '  <p>' . osc_draw_checkbox_field('delete_reviews', null, true) . ' ' . $osC_Language->get('field_delete_reviews') . '</p>';
  }
?>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&search=' . $_GET['search'] . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

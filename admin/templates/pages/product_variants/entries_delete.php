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

  $osC_ObjectInfo = new osC_ObjectInfo(osC_ProductVariants_Admin::getEntryData($_GET['paeID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_ObjectInfo->get('products_options_values_name'); ?></div>
<div class="infoBoxContent">
  <form name="paeDelete" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&paeID=' . $osC_ObjectInfo->get('products_options_values_id') . '&action=deleteEntry'); ?>" method="post">

<?php
  if ( $osC_ObjectInfo->get('total_products') > 0 ) {
?>

  <p><?php echo '<b>' . sprintf($osC_Language->get('delete_error_group_entry_in_use'), $osC_ObjectInfo->get('total_products')) . '</b>'; ?></p>

  <p align="center"><?php echo '<input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

<?php
  } else {
?>

  <p><?php echo $osC_Language->get('introduction_delete_group_entry'); ?></p>

  <p><?php echo '<b>' . $osC_ObjectInfo->get('products_options_values_name') . '</b>'; ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

<?php
  }
?>

  </form>
</div>

<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Categories_Admin::get($_GET['cID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_ObjectInfo->getProtected('categories_name'); ?></div>
<div class="infoBoxContent">
  <form name="cDelete" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&cID=' . $osC_ObjectInfo->getInt('categories_id') . '&action=delete'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_delete_category'); ?></p>

  <p><?php echo '<b>' . $osC_ObjectInfo->getProtected('categories_name') . '</b>'; ?></p>

<?php
  if ( $osC_ObjectInfo->get('childs_count') > 0 ) {
    echo '<p>' . sprintf($osC_Language->get('delete_warning_category_in_use_children'), $osC_ObjectInfo->get('childs_count')) . '</p>';
  }

  if ( $osC_ObjectInfo->get('products_count') > 0 ) {
    echo '<p>' . sprintf($osC_Language->get('delete_warning_category_in_use_products'), $osC_ObjectInfo->get('products_count')) . '</p>';
  }
?>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()]) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

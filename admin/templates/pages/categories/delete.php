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

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Categories_Admin::getData($_GET['cID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_ObjectInfo->get('categories_name'); ?></div>
<div class="infoBoxContent">
  <form name="cDelete" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search'] . '&cID=' . $osC_ObjectInfo->get('categories_id') . '&action=delete'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_delete_category'); ?></p>

  <p><?php echo '<b>' . $osC_ObjectInfo->get('categories_name') . '</b>'; ?></p>

<?php
  if ($osC_ObjectInfo->get('childs_count') > 0) {
    echo '  <p>' . sprintf($osC_Language->get('delete_warning_category_in_use_children'), $osC_ObjectInfo->get('childs_count')) . '</p>';
  }

  if ($osC_ObjectInfo->get('products_count') > 0) {
    echo '  <p>' . sprintf($osC_Language->get('delete_warning_category_in_use_products'), $osC_ObjectInfo->get('products_count')) . '</p>';
  }
?>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

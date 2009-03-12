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

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Products_Admin::get($_GET[$osC_Template->getModule()]));

  $in_categories = array();

  $Qcategories = $osC_Database->query('select categories_id from :table_products_to_categories where products_id = :products_id');
  $Qcategories->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
  $Qcategories->bindInt(':products_id', $osC_ObjectInfo->get('products_id'));
  $Qcategories->execute();

  while ( $Qcategories->next() ) {
    $in_categories[] = $Qcategories->valueInt('categories_id');
  }

  $categories_array = array();

  foreach ( $in_categories as $category_id ) {
    $categories_array[] = array('id' => $category_id,
                                'text' => $osC_CategoryTree->getPath($category_id, 0, ' &raquo; '));
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_ObjectInfo->getProtected('products_name'); ?></div>
<div class="infoBoxContent">
  <form name="pDelete" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $osC_ObjectInfo->getInt('products_id') . '&cID=' . $_GET['cID'] . '&action=delete'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_delete_product'); ?></p>

  <p><?php echo '<b>' . $osC_ObjectInfo->getProtected('products_name') . '</b>'; ?></p>

  <p><?php echo osc_draw_checkbox_field('product_categories[]', $categories_array, true, null, '<br />'); ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $_GET['cID']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

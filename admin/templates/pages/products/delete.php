<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Products_Admin::getData($_GET['pID']));

  $in_categories = array();

  $Qcategories = $osC_Database->query('select categories_id from :table_products_to_categories where products_id = :products_id');
  $Qcategories->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
  $Qcategories->bindInt(':products_id', $osC_ObjectInfo->get('products_id'));
  $Qcategories->execute();

  while ($Qcategories->next()) {
    $in_categories[] = $Qcategories->valueInt('categories_id');
  }

  $categories_array = array();

  foreach ($in_categories as $category_id) {
    $categories_array[] = array('id' => $category_id,
                                'text' => $osC_CategoryTree->getPath($category_id, 0, ' &raquo; '));
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_ObjectInfo->get('products_name'); ?></div>
<div class="infoBoxContent">
  <form name="pDelete" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search'] . '&pID=' . $osC_ObjectInfo->get('products_id') . '&action=delete'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_delete_product'); ?></p>

  <p><?php echo '<b>' . $osC_ObjectInfo->get('products_name') . '</b>'; ?></p>

  <p><?php echo osc_draw_checkbox_field('product_categories[]', $categories_array, true, null, '<br />'); ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

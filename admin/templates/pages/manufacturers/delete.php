<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Manufacturers_Admin::getData($_GET['mID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $osC_ObjectInfo->get('manufacturers_name'); ?></div>
<div class="infoBoxContent">
  <form name="mDelete" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&mID=' . $osC_ObjectInfo->get('manufacturers_id') . '&action=delete'); ?>" method="post">

  <p><?php echo TEXT_DELETE_INTRO; ?></p>

  <p><?php echo '<b>' . $osC_ObjectInfo->get('manufacturers_name') . '</b>'; ?></p>

<?php
  if ( !osc_empty($osC_ObjectInfo->get('manufacturers_image')) ) {
    echo '  <p>' . osc_draw_checkbox_field('delete_image', null, true) . ' ' . TEXT_DELETE_IMAGE . '</p>';
  }

  if ( $osC_ObjectInfo->get('products_count') > 0 ) {
    echo '  <p>' . osc_draw_checkbox_field('delete_products') . ' ' . TEXT_DELETE_PRODUCTS . '</p>' .
         '  <p>' . sprintf(TEXT_DELETE_WARNING_PRODUCTS, $osC_ObjectInfo->get('products_count')) . '</p>';
  }
?>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

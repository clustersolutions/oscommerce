<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_ProductAttributes_Admin::getData($_GET['paID']));
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $osC_ObjectInfo->get('products_options_name'); ?></div>
<div class="infoBoxContent">
  <form name="paDelete" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&paID=' . $osC_ObjectInfo->get('products_options_id') . '&action=delete'); ?>" method="post">

<?php
  if ( $osC_ObjectInfo->get('total_products') > 0 ) {
?>

  <p><?php echo '<b>' . sprintf(TEXT_INFO_DELETE_ATTRIBUTE_GROUP_PROHIBITED, $osC_ObjectInfo->get('total_products')) . '</b>'; ?></p>

  <p align="center"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

<?php
  } else {
?>

  <p><?php echo TEXT_INFO_DELETE_ATTRIBUTE_INTRO; ?></p>

  <p><?php echo '<b>' . $osC_ObjectInfo->get('products_options_name') . '</b>'; ?></p>

<?php
    if ( $osC_ObjectInfo->get('total_entries') > 0 ) {
      echo '  <p><b>' . sprintf(TEXT_INFO_DELETE_ATTRIBUTE_GROUP_WARNING, $osC_ObjectInfo->get('total_entries')) . '</b></p>';
    }
?>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

<?php
  }
?>

  </form>
</div>

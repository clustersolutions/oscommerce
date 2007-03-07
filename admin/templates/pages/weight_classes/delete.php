<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_WeightClasses_Admin::getData($_GET['wcID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $osC_ObjectInfo->get('weight_class_title'); ?></div>
<div class="infoBoxContent">
  <form name="wcDelete" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&wcID=' . $osC_ObjectInfo->get('weight_class_id') . '&action=delete'); ?>" method="post">

<?php
  $Qcheck = $osC_Database->query('select count(*) as total from :table_products where products_weight_class = :products_weight_class');
  $Qcheck->bindTable(':table_products', TABLE_PRODUCTS);
  $Qcheck->bindInt(':products_weight_class', $osC_ObjectInfo->get('weight_class_id'));
  $Qcheck->execute();

  if ( ( $osC_ObjectInfo->get('weight_class_id') == SHIPPING_WEIGHT_UNIT ) || ( $Qcheck->valueInt('total') > 0 ) ) {
    if ( $osC_ObjectInfo->get('weight_class_id') == SHIPPING_WEIGHT_UNIT ) {
      echo '  <p><b>' . TEXT_INFO_DELETE_PROHIBITED . '</b></p>';
    }

    if ( $Qcheck->valueInt('total') > 0 ) {
      echo '  <p><b>' . sprintf(TEXT_INFO_DELETE_PROHIBITED_PRODUCTS, $Qcheck->valueInt('total')) . '</b></p>';
    }

    echo '  <p align="center"><input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" /></p>';
  } else {
?>

  <p><?php echo TEXT_INFO_DELETE_INTRO; ?></p>

  <p><?php echo '<b>' . $osC_ObjectInfo->get('weight_class_title') . '</b>'; ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

<?php
  }
?>

  </form>
</div>

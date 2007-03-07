<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Tax_Admin::getData($_GET['tcID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $osC_ObjectInfo->get('tax_class_title'); ?></div>
<div class="infoBoxContent">
  <form name="tcDelete" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&tcID=' . $osC_ObjectInfo->get('tax_class_id') . '&action=delete'); ?>" method="post">

<?php
  $Qcheck = $osC_Database->query('select products_id from :table_products where products_tax_class_id = :products_tax_class_id limit 1');
  $Qcheck->bindTable(':table_products', TABLE_PRODUCTS);
  $Qcheck->bindInt(':products_tax_class_id', $osC_ObjectInfo->get('tax_class_id'));
  $Qcheck->execute();

  if ( $Qcheck->numberOfRows() > 0 ) {
?>

  <p><?php echo '<b>' . TEXT_INFO_DELETE_PROHIBITED . '</b>'; ?></p>
  <p align="center"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

<?php
  } else {
?>

  <p><?php echo TEXT_INFO_DELETE_INTRO; ?></p>
  <p><?php echo '<b>' . $osC_ObjectInfo->get('tax_class_title'); ?></p>

<?php
    if ($osC_ObjectInfo->get('total_tax_rates') > 0) {
      echo '    <p><b>' . sprintf(TEXT_INFO_DELETE_TAX_RATES_WARNING, $osC_ObjectInfo->get('total_tax_rates')) . '</b></p>' . "\n";
    }
?>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

<?php
  }
?>

</div>

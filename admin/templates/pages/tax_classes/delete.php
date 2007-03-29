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

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Tax_Admin::getData($_GET['tcID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_ObjectInfo->get('tax_class_title'); ?></div>
<div class="infoBoxContent">
  <form name="tcDelete" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&tcID=' . $osC_ObjectInfo->get('tax_class_id') . '&action=delete'); ?>" method="post">

<?php
  $Qcheck = $osC_Database->query('select products_id from :table_products where products_tax_class_id = :products_tax_class_id limit 1');
  $Qcheck->bindTable(':table_products', TABLE_PRODUCTS);
  $Qcheck->bindInt(':products_tax_class_id', $osC_ObjectInfo->get('tax_class_id'));
  $Qcheck->execute();

  if ( $Qcheck->numberOfRows() > 0 ) {
?>

  <p><?php echo '<b>' . sprintf($osC_Language->get('delete_warning_tax_class_in_use'), $Qcheck->numberOfRows()) . '</b>'; ?></p>

  <p align="center"><?php echo '<input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

<?php
  } else {
?>

  <p><?php echo $osC_Language->get('introduction_delete_tax_class'); ?></p>

<?php
    $tax_class_name = $osC_ObjectInfo->get('tax_class_title');

    if ($osC_ObjectInfo->get('total_tax_rates') > 0) {
      $tax_class_name .= ' (' . sprintf($osC_Language->get('total_entries'), $osC_ObjectInfo->get('total_tax_rates')) . ')';
    }
?>

  <p><?php echo '<b>' . $tax_class_name . '</b>'; ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

<?php
  }
?>

</div>

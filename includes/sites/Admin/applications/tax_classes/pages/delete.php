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

  $osC_ObjectInfo = new osC_ObjectInfo(osC_TaxClasses_Admin::get($_GET['tcID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_ObjectInfo->getProtected('tax_class_title'); ?></div>
<div class="infoBoxContent">
  <form name="tcDelete" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&tcID=' . $osC_ObjectInfo->getInt('tax_class_id') . '&action=delete'); ?>" method="post">

<?php
  if ( osC_TaxClasses_Admin::hasProducts($osC_ObjectInfo->getInt('tax_class_id')) ) {
?>

  <p><?php echo '<b>' . sprintf($osC_Language->get('delete_warning_tax_class_in_use'), osC_TaxClasses_Admin::getNumberOfProducts($osC_ObjectInfo->getInt('tax_class_id'))) . '</b>'; ?></p>

  <p align="center"><?php echo '<input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>

<?php
  } else {
?>

  <p><?php echo $osC_Language->get('introduction_delete_tax_class'); ?></p>

  <p><?php echo '<b>' . $osC_ObjectInfo->get('tax_class_title') . ' (' . sprintf($osC_Language->get('total_entries'), $osC_ObjectInfo->getInt('total_tax_rates')) . ')</b>'; ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>

<?php
  }
?>

</div>

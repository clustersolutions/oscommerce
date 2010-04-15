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
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_Language->get('action_heading_batch_delete_tax_classes'); ?></div>
<div class="infoBoxContent">
  <form name="tcDeleteBatch" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=batch_delete'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_batch_delete_tax_classes'); ?></p>

<?php
  $check_tax_classes_flag = array();

  $Qclasses = $osC_Database->query('select tax_class_id, tax_class_title from :table_tax_class where tax_class_id in (":tax_class_id") order by tax_class_title');
  $Qclasses->bindTable(':table_tax_class', TABLE_TAX_CLASS);
  $Qclasses->bindRaw(':tax_class_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qclasses->execute();

  $names_string = '';

  while ( $Qclasses->next() ) {
    if ( osC_TaxClasses_Admin::hasProducts($Qclasses->valueInt('tax_class_id')) ) {
      $check_tax_classes_flag[] = $Qclasses->value('tax_class_title');
    }

    $names_string .= osc_draw_hidden_field('batch[]', $Qclasses->valueInt('tax_class_id')) . '<b>' . $Qclasses->value('tax_class_title') . ' (' . sprintf($osC_Language->get('total_entries'), osC_TaxClasses_Admin::getNumberOfTaxRates($Qclasses->valueInt('tax_class_id'))) . ')</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2) . osc_draw_hidden_field('subaction', 'confirm');
  }

  echo '<p>' . $names_string . '</p>';

  if ( empty($check_tax_classes_flag) ) {
    echo '<p align="center"><input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" /></p>';
  } else {
    echo '<p><b>' . $osC_Language->get('batch_delete_warning_tax_class_in_use') . '</b></p>' .
         '<p>' . implode(', ', $check_tax_classes_flag) . '</p>';

    echo '<p align="center"><input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" /></p>';
  }
?>

  </form>
</div>

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
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_Language->get('action_heading_batch_delete_group_entries'); ?></div>
<div class="infoBoxContent">
  <form name="paeDeleteBatch" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&action=batchDeleteEntries'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_batch_delete_group_entries'); ?></p>

<?php
  $check_products_array = array();

  $Qentries = $osC_Database->query('select products_options_values_id, products_options_values_name from :table_products_options_values where products_options_values_id in (":products_options_values_id") and language_id = :language_id order by products_options_values_name');
  $Qentries->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
  $Qentries->bindRaw(':products_options_values_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qentries->bindInt(':language_id', $osC_Language->getID());
  $Qentries->execute();

  $names_string = '';

  while ( $Qentries->next() ) {
    $Qproducts = $osC_Database->query('select count(*) as total_products from :table_products_attributes where options_values_id = :options_values_id');
    $Qproducts->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
    $Qproducts->bindInt(':options_values_id', $Qentries->valueInt('products_options_values_id'));
    $Qproducts->execute();

    if ( $Qproducts->valueInt('total_products') > 0 ) {
      $check_products_array[] = $Qentries->value('products_options_values_name');
    }

    $names_string .= osc_draw_hidden_field('batch[]', $Qentries->valueInt('products_options_values_id')) . '<b>' . $Qentries->value('products_options_values_name') . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2);
  }

  echo '<p>' . $names_string . '</p>';

  if ( empty($check_products_array) ) {
?>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

<?php
  } else {
?>

  <p><b><?php echo $osC_Language->get('batch_delete_error_group_entries_in_use'); ?></b></p>

  <p><?php echo implode(', ', $check_products_array); ?></p>

  <p align="center"><?php echo '<input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

<?php
  }
?>

  </form>
</div>

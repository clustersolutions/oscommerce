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
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_Language->get('action_heading_batch_delete_attribute_groups'); ?></div>
<div class="infoBoxContent">
  <form name="paDeleteBatch" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchDelete'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_batch_delete_attribute_groups'); ?></p>

<?php
  $check_products_flag = array();

  $Qgroups = $osC_Database->query('select products_options_id, products_options_name from :table_products_options where language_id = :language_id and products_options_id in (":products_options_id") order by products_options_name');
  $Qgroups->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
  $Qgroups->bindInt(':language_id', $osC_Language->getID());
  $Qgroups->bindRaw(':products_options_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qgroups->execute();

  $names_string = '';

  while ( $Qgroups->next() ) {
    $Qproducts = $osC_Database->query('select count(*) as total_products from :table_products_attributes where options_id = :options_id');
    $Qproducts->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
    $Qproducts->bindInt(':options_id', $Qgroups->valueInt('products_options_id'));
    $Qproducts->execute();

    if ( $Qproducts->valueInt('total_products') > 0 ) {
      $check_products_flag[] = $Qgroups->value('products_options_name');
    }

    $Qentries = $osC_Database->query('select count(*) as total_entries from :table_products_options_values_to_products_options where products_options_id = :products_options_id');
    $Qentries->bindTable(':table_products_options_values_to_products_options', TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS);
    $Qentries->bindInt(':products_options_id', $Qgroups->valueInt('products_options_id'));
    $Qentries->execute();

    $group_name = $Qgroups->value('products_options_name');

    if ( $Qentries->valueInt('total_entries') > 0 ) {
      $group_name .= ' (' . sprintf($osC_Language->get('total_entries'), $Qentries->valueInt('total_entries')) . ')';
    }

    $names_string .= osc_draw_hidden_field('batch[]', $Qgroups->valueInt('products_options_id')) . '<b>' . $group_name . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2);
  }

  echo '<p>' . $names_string . '</p>';

  if ( empty($check_products_flag) ) {
?>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

<?php
  } else {
?>

  <p><b><?php echo $osC_Language->get('batch_delete_error_attribute_groups_in_use'); ?></b></p>

  <p><?php echo implode(', ', $check_products_flag); ?></p>

  <p align="center"><?php echo '<input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

<?php
  }
?>

  </form>
</div>

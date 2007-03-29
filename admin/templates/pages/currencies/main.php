<?php
/*
  $Id$

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

<p align="right"><?php echo '<input type="button" value="' . $osC_Language->get('button_update_currency_exchange_rates') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=updateRates') . '\';" class="infoBoxButton" />&nbsp;<input type="button" value="' . $osC_Language->get('button_insert') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=save') . '\';" class="infoBoxButton" />'; ?></p>

<?php
  $Qcurrencies = $osC_Database->query('select * from :table_currencies order by title');
  $Qcurrencies->bindTable(':table_currencies', TABLE_CURRENCIES);
  $Qcurrencies->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qcurrencies->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php echo $Qcurrencies->getBatchTotalPages($osC_Language->get('batch_results_number_of_entries')); ?></td>
    <td align="right"><?php echo $Qcurrencies->getBatchPageLinks('page', $osC_Template->getModule(), false); ?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo $osC_Language->get('table_heading_currencies'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_code'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_value'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_example'); ?></th>
      <th width="150"><?php echo $osC_Language->get('table_heading_action'); ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="5"><?php echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . $osC_Language->get('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchDelete') . '\';" />'; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>

<?php
  while ($Qcurrencies->next()) {
    $currency_name = $Qcurrencies->value('title');

    if ( $Qcurrencies->value('code') == DEFAULT_CURRENCY ) {
      $currency_name .= ' (' . $osC_Language->get('default_entry') . ')';
    }
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td onclick="document.getElementById('batch<?php echo $Qcurrencies->valueInt('currencies_id'); ?>').checked = !document.getElementById('batch<?php echo $Qcurrencies->valueInt('currencies_id'); ?>').checked;"><?php echo $currency_name; ?></td>
      <td><?php echo $Qcurrencies->value('code'); ?></td>
      <td><?php echo number_format($Qcurrencies->valueDecimal('value'), 8); ?></td>
      <td><?php echo $osC_Currencies->format(1499.99, $Qcurrencies->value('code'), 1); ?></td>
      <td align="right">

<?php
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cID=' . $Qcurrencies->valueInt('currencies_id') . '&action=save'), osc_icon('edit.png')) . '&nbsp;' .
         osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cID=' . $Qcurrencies->valueInt('currencies_id') . '&action=delete'), osc_icon('trash.png'));
?>

      </td>
      <td align="center"><?php echo osc_draw_checkbox_field('batch[]', $Qcurrencies->valueInt('currencies_id'), null, 'id="batch' . $Qcurrencies->valueInt('currencies_id') . '"'); ?></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

</form>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . $osC_Language->get('table_action_legend') . '</b> ' . osc_icon('edit.png') . '&nbsp;' . $osC_Language->get('icon_edit') . '&nbsp;&nbsp;' . osc_icon('trash.png') . '&nbsp;' . $osC_Language->get('icon_trash'); ?></td>
    <td align="right"><?php echo $Qcurrencies->getBatchPagesPullDownMenu('page', $osC_Template->getModule()); ?></td>
  </tr>
</table>

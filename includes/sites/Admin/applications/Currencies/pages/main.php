<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<h1><?php echo osc_link_object(OSCOM::getLink(), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<form id="liveSearchForm">
  <input type="text" id="liveSearchField" name="search" class="searchField fieldTitleAsDefault" title="Search.." /><?php echo osc_draw_button(array('type' => 'button', 'params' => 'onclick="osC_DataTable.reset();"', 'title' => 'Reset')); ?>

  <span style="float: right;"><?php echo osc_draw_button(array('href' => OSCOM::getLink(null, null, 'action=UpdateRates'), 'icon' => 'refresh', 'title' => OSCOM::getDef('button_update_currency_exchange_rates'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(null, null, 'action=Save'), 'icon' => 'plus', 'title' => OSCOM::getDef('button_insert'))); ?></span>
</form>

<div style="padding: 20px 5px 5px 5px; height: 16px;">
  <span id="batchTotalPages"></span>
  <span id="batchPageLinks"></span>
</div>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="currenciesDataTable">
  <thead>
    <tr>
      <th><?php echo OSCOM::getDef('table_heading_currencies'); ?></th>
      <th><?php echo OSCOM::getDef('table_heading_code'); ?></th>
      <th><?php echo OSCOM::getDef('table_heading_value'); ?></th>
      <th width="150"><?php echo OSCOM::getDef('table_heading_action'); ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="4"><?php echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . OSCOM::getDef('icon_trash') . '" onclick="document.batch.action=\'' . OSCOM::getLink(null, null, 'action=BatchDelete') . '\';" />'; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>
  </tbody>
</table>

</form>

<div style="padding: 2px;">
  <span id="dataTableLegend"><?php echo '<b>' . OSCOM::getDef('table_action_legend') . '</b> ' . osc_icon('edit.png') . '&nbsp;' . OSCOM::getDef('icon_edit') . '&nbsp;&nbsp;' . osc_icon('trash.png') . '&nbsp;' . OSCOM::getDef('icon_trash'); ?></span>
  <span id="batchPullDownMenu"></span>
</div>

<script type="text/javascript">
  var moduleParamsCookieName = 'oscadmin_module_' + pageModule;

  var moduleParams = new Object();
  moduleParams.page = 1;
  moduleParams.search = '';

  if ( $.cookie(moduleParamsCookieName) != null ) {
    var p = $.secureEvalJSON($.cookie(moduleParamsCookieName));
    moduleParams.page = parseInt(p.page);
    moduleParams.search = String(p.search);
  }

  var dataTableName = 'currenciesDataTable';
  var dataTableDataURL = '<?php echo OSCOM::getRPCLink(null, null, 'action=getAll'); ?>';

  var cEditLink = '<?php echo OSCOM::getLink(null, null, 'id=CID&action=Save'); ?>';
  var cEditLinkIcon = '<?php echo osc_icon('edit.png'); ?>';

  var cDeleteLink = '<?php echo OSCOM::getLink(null, null, 'id=CID&action=Delete'); ?>';
  var cDeleteLinkIcon = '<?php echo osc_icon('trash.png'); ?>';

  var defaultCurrency = '<?php echo DEFAULT_CURRENCY; ?>';
  var defaultText = '<?php echo addslashes(OSCOM::getDef('default_entry')); ?>';

  var osC_DataTable = new osC_DataTable();
  osC_DataTable.load();

  function feedDataTable(data) {
    var rowCounter = 0;

    for ( var r in data.entries ) {
      var record = data.entries[r];

      var currencyName = record.title;

      if ( record.code == defaultCurrency ) {
        currencyName += ' (' + defaultText + ')';
      }

      var newRow = $('#' + dataTableName)[0].tBodies[0].insertRow(rowCounter);
      newRow.id = 'row' + parseInt(record.currencies_id);

      $('#row' + parseInt(record.currencies_id)).hover( function() { rowOverEffect(this); }, function() { rowOutEffect(this); }).click(function(event) {
        if (event.target.type !== 'checkbox') {
          $(':checkbox', this).trigger('click');
        }
      }).css('cursor', 'pointer');

      var newCell = newRow.insertCell(0);
      newCell.innerHTML = htmlSpecialChars(currencyName);

      newCell = newRow.insertCell(1);
      newCell.innerHTML = htmlSpecialChars(record.code);

      newCell = newRow.insertCell(2);
      newCell.innerHTML = htmlSpecialChars(record.value);

      newCell = newRow.insertCell(3);
      newCell.innerHTML = '<a href="' + cEditLink.replace('CID', parseInt(record.currencies_id)) + '">' + cEditLinkIcon + '</a>&nbsp;<a href="' + cDeleteLink.replace('CID', parseInt(record.currencies_id)) + '">' + cDeleteLinkIcon + '</a>';
      newCell.align = 'right';

      newCell = newRow.insertCell(4);
      newCell.innerHTML = '<input type="checkbox" name="batch[]" value="' + parseInt(record.currencies_id) + '" id="batch' + parseInt(record.currencies_id) + '" />';
      newCell.align = 'center';

      rowCounter++;
    }
  }
</script>

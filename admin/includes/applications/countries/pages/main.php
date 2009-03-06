<?php
/*
  $Id: main.php 1845 2009-02-27 00:19:37Z hpdl $

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

<div style="padding-bottom: 10px;">
  <span><form id="liveSearchForm"><input type="text" id="liveSearchField" name="search" class="searchField fieldTitleAsDefault" title="Search.." /><input type="button" value="Reset" class="operationButton" onclick="osC_DataTable.reset();" /></form></span>
  <span style="float: right;"><?php echo '<input type="button" value="' . $osC_Language->get('button_insert') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=save') . '\';" class="infoBoxButton" />'; ?></span>
</div>

<div style="clear: both; padding: 2px; height: 16px;">
  <span id="batchTotalPages"></span>
  <span id="batchPageLinks"></span>
</div>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="countryDataTable">
  <thead>
    <tr>
      <th><?php echo $osC_Language->get('table_heading_countries'); ?></th>
      <th width="20">&nbsp;</th>
      <th><?php echo $osC_Language->get('table_heading_code'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_total_zones'); ?></th>
      <th width="150"><?php echo $osC_Language->get('table_heading_action'); ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="5"><?php echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . $osC_Language->get('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=batch_delete') . '\';" />'; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>
  </tbody>
</table>

</form>

<div style="padding: 2px;">
  <span id="dataTableLegend"><?php echo '<b>' . $osC_Language->get('table_action_legend') . '</b> ' . osc_icon('edit.png') . '&nbsp;' . $osC_Language->get('icon_edit') . '&nbsp;&nbsp;' . osc_icon('trash.png') . '&nbsp;' . $osC_Language->get('icon_trash'); ?></span>
  <span id="batchPullDownMenu"></span>
</div>

<script type="text/javascript"><!--
  var moduleParamsCookieName = 'oscadmin_module_' + pageModule;

  var moduleParams = new Object();
  moduleParams.page = 1;
  moduleParams.search = '';

  if ( $.cookie(moduleParamsCookieName) != null ) {
    var p = $.secureEvalJSON($.cookie(moduleParamsCookieName));
    moduleParams.page = parseInt(p.page);
    moduleParams.search = String(p.search);
  }

  var dataTableName = 'countryDataTable';
  var dataTableDataURL = '<?php echo osc_href_link_admin('rpc.php', $osC_Template->getModule() . '&action=getAll'); ?>';

  var countryLink = '<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=COUNTRYID'); ?>';
  var countryLinkIcon = '<?php echo osc_icon('folder.png'); ?>';

  var countryFlag = '<?php echo osc_image('../images/worldflags/COUNTRYISOCODE2.png', 'COUNTRYNAME'); ?>';

  var countryEditLink = '<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=COUNTRYID&action=save'); ?>';
  var countryEditLinkIcon = '<?php echo osc_icon('edit.png'); ?>';

  var countryDeleteLink = '<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=COUNTRYID&action=delete'); ?>';
  var countryDeleteLinkIcon = '<?php echo osc_icon('trash.png'); ?>';

  var osC_DataTable = new osC_DataTable();
  osC_DataTable.load();

  function feedDataTable(data) {
    var rowCounter = 0;

    for ( var r in data.entries ) {
      var record = data.entries[r];

      var newRow = $('#' + dataTableName)[0].tBodies[0].insertRow(rowCounter);
      newRow.id = 'row' + parseInt(record.countries_id);

      $('#row' + parseInt(record.countries_id)).mouseover( function() { rowOverEffect(this); }).mouseout( function() { rowOutEffect(this); }).click(function(event) {
        if (event.target.type !== 'checkbox') {
          $(':checkbox', this).trigger('click');
        }
      }).css('cursor', 'pointer');

      var newCell = newRow.insertCell(0);
      newCell.innerHTML = '<a href="' + countryLink.replace('COUNTRYID', parseInt(record.countries_id)) + '">' + countryLinkIcon + '&nbsp;' + htmlSpecialChars(record.countries_name) + '</a>';

      newCell = newRow.insertCell(1);
      newCell.innerHTML = countryFlag.replace('COUNTRYISOCODE2', htmlSpecialChars(record.countries_iso_code_2)).replace('COUNTRYNAME', htmlSpecialChars(record.countries_name)).replace('COUNTRYNAME', htmlSpecialChars(record.countries_name));

      newCell = newRow.insertCell(2);
      newCell.innerHTML = htmlSpecialChars(record.countries_iso_code_2) + '&nbsp;&nbsp;&nbsp;&nbsp;' + htmlSpecialChars(record.countries_iso_code_3);

      newCell = newRow.insertCell(3);
      newCell.innerHTML = parseInt(record.total_zones);

      newCell = newRow.insertCell(4);
      newCell.innerHTML = '<a href="' + countryEditLink.replace('COUNTRYID', parseInt(record.countries_id)) + '">' + countryEditLinkIcon + '</a>&nbsp;<a href="' + countryDeleteLink.replace('COUNTRYID', parseInt(record.countries_id)) + '">' + countryDeleteLinkIcon + '</a>';
      newCell.align = 'right';

      newCell = newRow.insertCell(5);
      newCell.innerHTML = '<input type="checkbox" name="batch[]" value="' + parseInt(record.countries_id) + '" id="batch' + parseInt(record.countries_id) + '" />';
      newCell.align = 'center';

      rowCounter++;
    }
  }
//--></script>

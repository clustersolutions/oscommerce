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

<div style="padding-bottom: 10px;">
  <span><form id="liveSearchForm"><input type="text" id="liveSearchField" name="search" class="searchField fieldTitleAsDefault" title="Search.." /><input type="button" value="Reset" class="operationButton" onclick="osC_DataTable.reset();" /></form></span>
  <span style="float: right;"><?php echo '<input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="infoBoxButton" /> <input type="button" value="' . $osC_Language->get('button_insert') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&action=insert_definition') . '\';" class="infoBoxButton" />'; ?></span>
</div>

<div style="clear: both; padding: 2px; height: 16px;">
  <span id="batchTotalPages"></span>
  <span id="batchPageLinks"></span>
</div>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="langDefGroupDataTable">
  <thead>
    <tr>
      <th><?php echo $osC_Language->get('table_heading_definition_groups'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_total_definitions'); ?></th>
      <th width="150"><?php echo $osC_Language->get('table_heading_action'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th colspan="3">&nbsp;</th>
    </tr>
  </tfoot>
  <tbody>
  </tbody>
</table>

<div style="padding: 2px;">
  <span id="dataTableLegend"><?php echo '<b>' . $osC_Language->get('table_action_legend') . '</b> ' . osc_icon('trash.png') . '&nbsp;' . $osC_Language->get('icon_trash'); ?></span>
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

  var dataTableName = 'langDefGroupDataTable';
  var dataTableDataURL = '<?php echo osc_href_link_admin('rpc.php', $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&action=getDefinitionGroups'); ?>';

  var groupLink = '<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&group=GROUPCODE'); ?>';
  var groupLinkIcon = '<?php echo osc_icon('folder.png'); ?>';

  var groupDeleteLink = '<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&group=GROUPCODE&action=delete_group'); ?>';
  var groupDeleteLinkIcon = '<?php echo osc_icon('trash.png'); ?>';

  var osC_DataTable = new osC_DataTable();
  osC_DataTable.load();

  function feedDataTable(data) {
    var rowCounter = 0;

    for ( var r in data.entries ) {
      var record = data.entries[r];

      var newRow = $('#' + dataTableName)[0].tBodies[0].insertRow(rowCounter);
      newRow.id = 'row' + record.content_group;

      $('#row' + record.content_group).mouseover( function() { rowOverEffect(this); }).mouseout( function() { rowOutEffect(this); });

      var newCell = newRow.insertCell(0);
      newCell.innerHTML = '<a href="' + groupLink.replace('GROUPCODE', htmlSpecialChars(record.content_group)) + '">' + groupLinkIcon + '&nbsp;' + htmlSpecialChars(record.content_group) + '</a>';

      newCell = newRow.insertCell(1);
      newCell.innerHTML = parseInt(record.total_entries);

      newCell = newRow.insertCell(2);
      newCell.innerHTML = '<a href="' + groupDeleteLink.replace('GROUPCODE', htmlSpecialChars(record.content_group)) + '">' + groupDeleteLinkIcon + '</a>';
      newCell.align = 'right';

      rowCounter++;
    }
  }
//--></script>

<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\OSCOM;
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<form id="liveSearchForm">
  <input type="text" id="liveSearchField" name="search" class="searchField fieldTitleAsDefault" title="Search.." /><?php echo osc_draw_button(array('type' => 'button', 'params' => 'onclick="osC_DataTable.reset();"', 'title' => 'Reset')); ?>

  <span style="float: right;"><?php echo osc_draw_button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(null, null, 'InsertDefinition&id=' . $_GET['id']), 'icon' => 'plus', 'title' => OSCOM::getDef('button_insert'))); ?></span>
</form>

<div style="padding: 20px 5px 5px 5px; height: 16px;">
  <span id="batchTotalPages"></span>
  <span id="batchPageLinks"></span>
</div>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="langDefGroupDataTable">
  <thead>
    <tr>
      <th><?php echo OSCOM::getDef('table_heading_definition_groups'); ?></th>
      <th width="150"><?php echo OSCOM::getDef('table_heading_action'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th colspan="2">&nbsp;</th>
    </tr>
  </tfoot>
  <tbody>
  </tbody>
</table>

<div style="padding: 2px;">
  <span id="dataTableLegend"><?php echo '<b>' . OSCOM::getDef('table_action_legend') . '</b> ' . osc_icon('trash.png') . '&nbsp;' . OSCOM::getDef('icon_trash'); ?></span>
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

  var dataTableName = 'langDefGroupDataTable';
  var dataTableDataURL = '<?php echo OSCOM::getRPCLink(null, null, 'id=' . $_GET['id'] . '&action=getDefinitionGroups'); ?>';

  var groupLink = '<?php echo OSCOM::getLink(null, null, 'id=' . $_GET['id'] . '&group=GROUPCODE'); ?>';
  var groupLinkIcon = '<?php echo osc_icon('folder.png'); ?>';

  var groupDeleteLink = '<?php echo OSCOM::getLink(null, null, 'DeleteGroup&id=' . $_GET['id'] . '&group=GROUPCODE'); ?>';
  var groupDeleteLinkIcon = '<?php echo osc_icon('trash.png'); ?>';

  var osC_DataTable = new osC_DataTable();
  osC_DataTable.load();

  function feedDataTable(data) {
    var rowCounter = 0;

    for ( var r in data.entries ) {
      var record = data.entries[r];

      var newRow = $('#' + dataTableName)[0].tBodies[0].insertRow(rowCounter);
      newRow.id = 'row' + record.content_group;

      $('#row' + record.content_group).hover( function() { $(this).addClass('mouseOver'); }, function() { $(this).removeClass('mouseOver'); }).click(function(event) {
      }).css('cursor', 'pointer');

      var newCell = newRow.insertCell(0);
      newCell.innerHTML = groupLinkIcon + '&nbsp;<a href="' + groupLink.replace('GROUPCODE', htmlSpecialChars(record.content_group)) + '" class="parent">' + htmlSpecialChars(record.content_group) + '</a><span style="float: right;">(' + parseInt(record.total_entries) + ')</span>';

      newCell = newRow.insertCell(1);
      newCell.innerHTML = '<a href="' + groupDeleteLink.replace('GROUPCODE', htmlSpecialChars(record.content_group)) + '">' + groupDeleteLinkIcon + '</a>';
      newCell.align = 'right';

      rowCounter++;
    }
  }
</script>

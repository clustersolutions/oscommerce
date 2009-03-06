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
</div>

<div id="infoPane" class="ui-corner-all" style="float: left; width: 150px;">

  <ul>

<?php
  foreach ( osc_toObjectInfo(osC_Configuration_Admin::getAllGroups())->get('entries') as $group ) {
    echo '<li id="cfgGroup' . (int)$group['configuration_group_id'] . '" style="list-style: circle;">' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&gID=' . (int)$group['configuration_group_id']), osc_output_string_protected($group['configuration_group_title'])) . '</li>';
  }
?>

  </ul>

</div>

<script type="text/javascript"><!--
  $('#cfgGroup<?php echo (int)$_GET['gID']; ?>').css('listStyle', 'disc').find('a').css({'fontWeight': 'bold', 'textDecoration': 'none'});
//--></script>

<div id="dataTableContainer" style="margin-left: 160px;">
  <div style="padding: 2px; min-height: 16px;">
    <span id="batchTotalPages"></span>
    <span id="batchPageLinks"></span>
  </div>

  <form name="batch" action="#" method="post">

  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="configurationDataTable">
    <thead>
      <tr>
        <th width="35%;"><?php echo $osC_Language->get('table_heading_title'); ?></th>
        <th><?php echo $osC_Language->get('table_heading_value'); ?></th>
        <th width="150"><?php echo $osC_Language->get('table_heading_action'); ?></th>
        <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <th align="right" colspan="3"><?php echo '<input type="image" src="' . osc_icon_raw('edit.png') . '" title="' . $osC_Language->get('icon_edit') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&gID=' . $_GET['gID'] . '&action=batch_save') . '\';" />'; ?></th>
        <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
      </tr>
    </tfoot>
    <tbody>
    </tbody>
  </table>

  </form>

  <div style="padding: 2px; min-height: 16px;">
    <span id="dataTableLegend"><?php echo '<b>' . $osC_Language->get('table_action_legend') . '</b> ' . osc_icon('edit.png') . '&nbsp;' . $osC_Language->get('icon_edit'); ?></span>
    <span id="batchPullDownMenu"></span>
  </div>
</div>

<div style="clear: both;"></div>

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

  var dataTableName = 'configurationDataTable';
  var dataTableDataURL = '<?php echo osc_href_link_admin('rpc.php', $osC_Template->getModule() . '&action=getAll&gID=' . (int)$_GET['gID']); ?>';

  var configEditLink = '<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&gID=' . (int)$_GET['gID'] . '&cID=CONFIGID&action=save'); ?>';
  var configEditLinkIcon = '<?php echo osc_icon('edit.png'); ?>';

  var osC_DataTable = new osC_DataTable();
  osC_DataTable.load();

  function feedDataTable(data) {
    var rowCounter = 0;

    for ( var r in data.entries ) {
      var record = data.entries[r];

      var newRow = $('#' + dataTableName)[0].tBodies[0].insertRow(rowCounter);
      newRow.id = 'row' + parseInt(record.configuration_id);

      $('#row' + parseInt(record.configuration_id)).mouseover( function() { rowOverEffect(this); }).mouseout( function() { rowOutEffect(this); }).click(function(event) {
        if (event.target.type !== 'checkbox') {
          $(':checkbox', this).trigger('click');
        }
      }).css('cursor', 'pointer');

      var newCell = newRow.insertCell(0);
      newCell.innerHTML = htmlSpecialChars(record.configuration_title);

      var newCell = newRow.insertCell(1);
      newCell.innerHTML = htmlSpecialChars(record.configuration_value).replace(/([^>]?)\n/g, '$1<br />\n'); // nl2br()

      newCell = newRow.insertCell(2);
      newCell.innerHTML = '<a href="' + configEditLink.replace('CONFIGID', parseInt(record.configuration_id)) + '">' + configEditLinkIcon + '</a>';
      newCell.align = 'right';

      newCell = newRow.insertCell(3);
      newCell.innerHTML = '<input type="checkbox" name="batch[]" value="' + parseInt(record.configuration_id) + '" id="batch' + parseInt(record.configuration_id) + '" />';
      newCell.align = 'center';

      rowCounter++;
    }
  }

/* HPDL
  var infoPaneWidth = $('#dataTableContainer').css('marginLeft');

  function toggleInfoPane() {
    if ( $('#dataTableContainer').css('marginLeft') == '0px' ) {
      $('#dataTableContainer').css('marginLeft', infoPaneWidth);
      $('#infoPane').show('fast');
    } else {
      $('#infoPane').hide('fast', function() { $('#dataTableContainer').css('marginLeft', '0px'); });
    }
  }
*/
//--></script>

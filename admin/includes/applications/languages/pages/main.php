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
  <span style="float: right;"><?php echo '<input type="button" value="' . $osC_Language->get('button_import') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=import') . '\';" class="infoBoxButton" />'; ?></span>
</div>

<div style="clear: both; padding: 2px; height: 16px;">
  <span id="batchTotalPages"></span>
  <span id="batchPageLinks"></span>
</div>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="langDataTable">
  <thead>
    <tr>
      <th><?php echo $osC_Language->get('table_heading_languages'); ?></th>
      <th width="20">&nbsp;</th>
      <th><?php echo $osC_Language->get('table_heading_code'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_total_definitions'); ?></th>
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
  <span id="dataTableLegend"><?php echo '<b>' . $osC_Language->get('table_action_legend') . '</b> ' . osc_icon('edit.png') . '&nbsp;' . $osC_Language->get('icon_edit') . '&nbsp;&nbsp;' . osc_icon('export.png') . '&nbsp;' . $osC_Language->get('icon_export') . '&nbsp;&nbsp;' . osc_icon('trash.png') . '&nbsp;' . $osC_Language->get('icon_trash'); ?></span>
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

  var dataTableName = 'langDataTable';
  var dataTableDataURL = '<?php echo osc_href_link_admin('rpc.php', $osC_Template->getModule() . '&action=getAll'); ?>';

  var languageLink = '<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=LANGUAGEID'); ?>';
  var languageLinkIcon = '<?php echo osc_icon('folder.png'); ?>';

  var languageIcon = '<?php echo osc_image('../images/worldflags/LANGUAGECODE.png', 'LANGUAGENAME'); ?>';

  var languageEditLink = '<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&lID=LANGUAGEID&action=save'); ?>';
  var languageEditLinkIcon = '<?php echo osc_icon('edit.png'); ?>';

  var languageExportLink = '<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&lID=LANGUAGEID&action=export'); ?>';
  var languageExportLinkIcon = '<?php echo osc_icon('export.png'); ?>';

  var languageDeleteLink = '<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&lID=LANGUAGEID&action=delete'); ?>';
  var languageDeleteLinkIcon = '<?php echo osc_icon('trash.png'); ?>';

  var defaultLanguage = '<?php echo DEFAULT_LANGUAGE; ?>';
  var defaultText = '<?php echo addslashes($osC_Language->get('default_entry')); ?>';

  var osC_DataTable = new osC_DataTable();
  osC_DataTable.load();

  function feedDataTable(data) {
    var rowCounter = 0;

    for ( var r in data.entries ) {
      var record = data.entries[r];

      var languageName = record.name;

      if ( record.code == defaultLanguage ) {
        languageName += ' (' + defaultText + ')';
      }

      var newRow = $('#' + dataTableName)[0].tBodies[0].insertRow(rowCounter);
      newRow.id = 'row' + parseInt(record.languages_id);

      $('#row' + parseInt(record.languages_id)).mouseover( function() { rowOverEffect(this); }).mouseout( function() { rowOutEffect(this); }).click(function(event) {
        if (event.target.type !== 'checkbox') {
          $(':checkbox', this).trigger('click');
        }
      }).css('cursor', 'pointer');

      var newCell = newRow.insertCell(0);
      newCell.innerHTML = '<a href="' + languageLink.replace('LANGUAGEID', parseInt(record.languages_id)) + '">' + languageLinkIcon + '&nbsp;' + htmlSpecialChars(languageName) + '</a>';

      newCell = newRow.insertCell(1);
      newCell.innerHTML = languageIcon.replace('LANGUAGECODE', htmlSpecialChars(record.code.toLowerCase().substring(3))).replace('LANGUAGENAME', htmlSpecialChars(record.name)).replace('LANGUAGENAME', htmlSpecialChars(record.name));

      newCell = newRow.insertCell(2);
      newCell.innerHTML = htmlSpecialChars(record.code);

      newCell = newRow.insertCell(3);
      newCell.innerHTML = parseInt(record.total_definitions);

      newCell = newRow.insertCell(4);
      newCell.innerHTML = '<a href="' + languageEditLink.replace('LANGUAGEID', parseInt(record.languages_id)) + '">' + languageEditLinkIcon + '</a>&nbsp;<a href="' + languageExportLink.replace('LANGUAGEID', parseInt(record.languages_id)) + '">' + languageExportLinkIcon + '</a>&nbsp;<a href="' + languageDeleteLink.replace('LANGUAGEID', parseInt(record.languages_id)) + '">' + languageDeleteLinkIcon + '</a>';
      newCell.align = 'right';

      newCell = newRow.insertCell(5);
      newCell.innerHTML = '<input type="checkbox" name="batch[]" value="' + parseInt(record.languages_id) + '" id="batch' + parseInt(record.languages_id) + '" />';
      newCell.align = 'center';

      rowCounter++;
    }
  }
//--></script>

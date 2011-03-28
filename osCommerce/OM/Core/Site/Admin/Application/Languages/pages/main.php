<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<form id="liveSearchForm">
  <input type="text" id="liveSearchField" name="search" class="searchField fieldTitleAsDefault" title="Search.." /><?php echo HTML::button(array('type' => 'button', 'params' => 'onclick="osC_DataTable.reset();"', 'title' => 'Reset')); ?>

  <span style="float: right;"><?php echo HTML::button(array('href' => OSCOM::getLink(null, null, 'Import'), 'icon' => 'triangle-1-se', 'title' => OSCOM::getDef('button_import'))); ?></span>
</form>

<div style="padding: 20px 5px 5px 5px; height: 16px;">
  <span id="batchTotalPages"></span>
  <span id="batchPageLinks"></span>
</div>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="langDataTable">
  <thead>
    <tr>
      <th><?php echo OSCOM::getDef('table_heading_languages'); ?></th>
      <th width="20">&nbsp;</th>
      <th><?php echo OSCOM::getDef('table_heading_code'); ?></th>
      <th width="150"><?php echo OSCOM::getDef('table_heading_action'); ?></th>
      <th align="center" width="20"><?php echo HTML::checkboxField('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="4"><?php echo HTML::imageSubmit(HTML::iconRaw('trash.png'), OSCOM::getDef('icon_trash'), 'onclick="document.batch.action=\'' . OSCOM::getLink(null, null, 'BatchDelete') . '\';"'); ?></th>
      <th align="center" width="20"><?php echo HTML::checkboxField('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>
  </tbody>
</table>

</form>

<div style="padding: 2px;">
  <span id="dataTableLegend"><?php echo '<b>' . OSCOM::getDef('table_action_legend') . '</b> ' . HTML::icon('edit.png') . '&nbsp;' . OSCOM::getDef('icon_edit') . '&nbsp;&nbsp;' . HTML::icon('export.png') . '&nbsp;' . OSCOM::getDef('icon_export') . '&nbsp;&nbsp;' . HTML::icon('trash.png') . '&nbsp;' . OSCOM::getDef('icon_trash'); ?></span>
  <span id="batchPullDownMenu"></span>
</div>

<script type="text/javascript">
  var moduleParamsCookieName = 'oscom_admin_' + pageModule;
  var dataTablePageSetName = 'page';

  var moduleParams = new Object();
  moduleParams[dataTablePageSetName] = 1;
  moduleParams['search'] = '';

  if ( $.cookie(moduleParamsCookieName) != null ) {
    moduleParams = $.secureEvalJSON($.cookie(moduleParamsCookieName));
  }

  var dataTableName = 'langDataTable';
  var dataTableDataURL = '<?php echo OSCOM::getRPCLink(null, null, 'GetAll'); ?>';

  var languageLink = '<?php echo OSCOM::getLink(null, null, 'id=LANGUAGEID'); ?>';
  var languageLinkIcon = '<?php echo HTML::icon('folder.png'); ?>';

  var languageIcon = '<?php echo HTML::image('images/worldflags/LANGUAGECODE.png', 'LANGUAGENAME'); ?>';

  var languageEditLink = '<?php echo OSCOM::getLink(null, null, 'Save&id=LANGUAGEID'); ?>';
  var languageEditLinkIcon = '<?php echo HTML::icon('edit.png'); ?>';

  var languageExportLink = '<?php echo OSCOM::getLink(null, null, 'Export&id=LANGUAGEID'); ?>';
  var languageExportLinkIcon = '<?php echo HTML::icon('export.png'); ?>';

  var languageDeleteLink = '<?php echo OSCOM::getLink(null, null, 'Delete&id=LANGUAGEID'); ?>';
  var languageDeleteLinkIcon = '<?php echo HTML::icon('trash.png'); ?>';

  var defaultLanguage = '<?php echo DEFAULT_LANGUAGE; ?>';
  var defaultText = '<?php echo addslashes(OSCOM::getDef('default_entry')); ?>';

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

      $('#row' + parseInt(record.languages_id)).hover( function() { $(this).addClass('mouseOver'); }, function() { $(this).removeClass('mouseOver'); }).click(function(event) {
        if (event.target.type !== 'checkbox') {
          $(':checkbox', this).trigger('click');
        }
      }).css('cursor', 'pointer');

      var newCell = newRow.insertCell(0);
      newCell.innerHTML = languageLinkIcon + '&nbsp;<a href="' + languageLink.replace('LANGUAGEID', parseInt(record.languages_id)) + '" class="parent">' + htmlSpecialChars(languageName) + '</a><span style="float: right;">(' + parseInt(record.total_definitions) + ')</span>';

      newCell = newRow.insertCell(1);
      newCell.innerHTML = languageIcon.replace('LANGUAGECODE', htmlSpecialChars(record.code.toLowerCase().substring(3))).replace('LANGUAGENAME', htmlSpecialChars(record.name)).replace('LANGUAGENAME', htmlSpecialChars(record.name));

      newCell = newRow.insertCell(2);
      newCell.innerHTML = htmlSpecialChars(record.code);

      newCell = newRow.insertCell(3);
      newCell.innerHTML = '<a href="' + languageEditLink.replace('LANGUAGEID', parseInt(record.languages_id)) + '">' + languageEditLinkIcon + '</a>&nbsp;<a href="' + languageExportLink.replace('LANGUAGEID', parseInt(record.languages_id)) + '">' + languageExportLinkIcon + '</a>&nbsp;<a href="' + languageDeleteLink.replace('LANGUAGEID', parseInt(record.languages_id)) + '">' + languageDeleteLinkIcon + '</a>';
      newCell.align = 'right';

      newCell = newRow.insertCell(4);
      newCell.innerHTML = '<input type="checkbox" name="batch[]" value="' + parseInt(record.languages_id) + '" id="batch' + parseInt(record.languages_id) + '" />';
      newCell.align = 'center';

      rowCounter++;
    }
  }
</script>

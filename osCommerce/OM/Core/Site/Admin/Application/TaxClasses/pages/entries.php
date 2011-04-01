<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
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

  <span style="float: right;"><?php echo HTML::button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(null, null, 'EntrySave&id=' . $_GET['id']), 'icon' => 'plus', 'title' => OSCOM::getDef('button_insert'))); ?></span>
</form>

<div style="padding: 20px 5px 5px 5px; height: 16px;">
  <span id="batchTotalPages"></span>
  <span id="batchPageLinks"></span>
</div>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="taxClassEntriesDataTable">
  <thead>
    <tr>
      <th width="100"><?php echo OSCOM::getDef('table_heading_tax_rate_priority'); ?></th>
      <th><?php echo OSCOM::getDef('table_heading_tax_rate_zone'); ?></th>
      <th width="100"><?php echo OSCOM::getDef('table_heading_tax_rate'); ?></th>
      <th width="150"><?php echo OSCOM::getDef('table_heading_action'); ?></th>
      <th align="center" width="20"><?php echo HTML::checkboxField('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="4"><?php echo HTML::submitImage(HTML::iconRaw('trash.png'), OSCOM::getDef('icon_trash'), 'onclick="document.batch.action=\'' . OSCOM::getLink(null, null, 'BatchDeleteEntries&id=' . $_GET['id']) . '\';"'); ?></th>
      <th align="center" width="20"><?php echo HTML::checkboxField('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>
  </tbody>
</table>

</form>

<div style="padding: 2px;">
  <span id="dataTableLegend"><?php echo '<b>' . OSCOM::getDef('table_action_legend') . '</b> ' . HTML::icon('edit.png') . '&nbsp;' . OSCOM::getDef('icon_edit') . '&nbsp;&nbsp;' . HTML::icon('trash.png') . '&nbsp;' . OSCOM::getDef('icon_trash'); ?></span>
  <span id="batchPullDownMenu"></span>
</div>

<script type="text/javascript">
  var moduleParamsCookieName = 'oscom_admin_' + pageModule;
  var dataTablePageSetName = 'entries_page';

  var moduleParams = new Object();
  moduleParams[dataTablePageSetName] = 1;
  moduleParams['search'] = '';

  if ( $.cookie(moduleParamsCookieName) != null ) {
    moduleParams = $.secureEvalJSON($.cookie(moduleParamsCookieName));
  }

  var dataTableName = 'taxClassEntriesDataTable';
  var dataTableDataURL = '<?php echo OSCOM::getRPCLink(null, null, 'GetAllEntries&id=' . $_GET['id']); ?>';

  var entryEditLink = '<?php echo OSCOM::getLink(null, null, 'EntrySave&id=' . $_GET['id'] . '&rID=ENTRYID'); ?>';
  var entryEditLinkIcon = '<?php echo HTML::icon('edit.png'); ?>';

  var entryDeleteLink = '<?php echo OSCOM::getLink(null, null, 'EntryDelete&id=' . $_GET['id'] . '&rID=ENTRYID'); ?>';
  var entryDeleteLinkIcon = '<?php echo HTML::icon('trash.png'); ?>';

  var osC_DataTable = new osC_DataTable();
  osC_DataTable.load();

  function feedDataTable(data) {
    var rowCounter = 0;

    for ( var r in data.entries ) {
      var record = data.entries[r];

      var newRow = $('#' + dataTableName)[0].tBodies[0].insertRow(rowCounter);
      newRow.id = 'row' + parseInt(record.tax_rates_id);

      $('#row' + parseInt(record.tax_rates_id)).hover( function() { $(this).addClass('mouseOver'); }, function() { $(this).removeClass('mouseOver'); }).click(function(event) {
        if (event.target.type !== 'checkbox') {
          $(':checkbox', this).trigger('click');
        }
      }).css('cursor', 'pointer');

      var newCell = newRow.insertCell(0);
      newCell.innerHTML = parseInt(record.tax_priority);

      newCell = newRow.insertCell(1);
      newCell.innerHTML = htmlSpecialChars(record.geo_zone_name);

      newCell = newRow.insertCell(2);
      newCell.innerHTML = displayTaxRateValue(record.tax_rate);

      newCell = newRow.insertCell(3);
      newCell.innerHTML = '<a href="' + entryEditLink.replace('ENTRYID', parseInt(record.tax_rates_id)) + '">' + entryEditLinkIcon + '</a>&nbsp;<a href="' + entryDeleteLink.replace('ENTRYID', parseInt(record.tax_rates_id)) + '">' + entryDeleteLinkIcon + '</a>';
      newCell.align = 'right';

      newCell = newRow.insertCell(4);
      newCell.innerHTML = '<input type="checkbox" name="batch[]" value="' + parseInt(record.tax_rates_id) + '" id="batch' + parseInt(record.tax_rates_id) + '" />';
      newCell.align = 'center';

      rowCounter++;
    }
  }
</script>

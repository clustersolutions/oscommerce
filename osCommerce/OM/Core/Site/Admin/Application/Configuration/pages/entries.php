<h1>{value}page_icon{value}<a href="{link}{link}">{value}page_title{value}</a></h1>

{widget}message_stack{widget}

<form id="liveSearchForm">
  <input type="text" name="search" id="liveSearchField" class="searchField" placeholder="{lang}placeholder_search{lang}" /><button type="button" id="buttonReset" onclick="osC_DataTable.reset();">{lang}button_reset{lang}</button>

  <span style="float: right;"><button type="button" id="buttonBack" class="ui-priority-secondary" onclick="window.location.href='{link}{link}';">{lang}button_back{lang}</button></span>
</form>

<script>
$('#buttonReset').button();
$('#buttonBack').button({
  icons: {
    primary: 'ui-icon-triangle-1-w'
  }
});
</script>

<div style="padding: 20px 5px 5px 5px; height: 16px;">
  <span id="batchTotalPages"></span>
  <span id="batchPageLinks"></span>
</div>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="configurationDataTable">
  <thead>
    <tr>
      <th width="35%;">{lang}table_heading_title{lang}</th>
      <th>{lang}table_heading_value{lang}</th>
      <th width="150">{lang}table_heading_action{lang}</th>
      <th align="center" width="20"><input type="checkbox" name="batchFlag" onclick="flagCheckboxes(this);" /></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="3"><input type="image" src="{iconraw}edit.png{iconraw}" title="{lang}icon_edit{lang}" onclick="document.batch.action='{link}||BatchSaveEntries&id={value}group_id{value}{link}';" /></th>
      <th align="center" width="20"><input type="checkbox" name="batchFlag" onclick="flagCheckboxes(this);" /></th>
    </tr>
  </tfoot>
  <tbody>
  </tbody>
</table>

</form>

<div style="padding: 2px; min-height: 16px;">
  <span id="dataTableLegend"><b>{lang}table_action_legend{lang}</b> {icon}edit.png{icon}&nbsp;{lang}icon_edit{lang}</span>
  <span id="batchPullDownMenu"></span>
</div>

<script>
  var moduleParamsCookieName = 'oscom_admin_' + pageModule;
  var dataTablePageSetName = 'entries_page';

  var moduleParams = new Object();
  moduleParams[dataTablePageSetName] = 1;
  moduleParams['search'] = '';

  if ( $.cookie(moduleParamsCookieName) != null ) {
    moduleParams = $.secureEvalJSON($.cookie(moduleParamsCookieName));
  }

  var dataTableName = 'configurationDataTable';
  var dataTableDataURL = '{rpclink}GetAllEntries&id={value}group_id{value}{rpclink}';

  var configEditLink = '{link}||EntrySave&id={value}group_id{value}&pID=CONFIGID{link}';
  var configEditLinkIcon = '{icon}edit.png{icon}';

  var osC_DataTable = new osC_DataTable();
  osC_DataTable.load();

  function feedDataTable(data) {
    var rowCounter = 0;

    for ( var r in data.entries ) {
      var record = data.entries[r];

      var newRow = $('#' + dataTableName)[0].tBodies[0].insertRow(rowCounter);
      newRow.id = 'row' + parseInt(record.configuration_id);

      $('#row' + parseInt(record.configuration_id)).hover( function() { $(this).addClass('mouseOver'); }, function() { $(this).removeClass('mouseOver'); }).click(function(event) {
        if (event.target.type !== 'checkbox') {
          $(':checkbox', this).trigger('click');
        }
      }).css('cursor', 'pointer');

      var newCell = newRow.insertCell(0);
      newCell.innerHTML = htmlSpecialChars(record.configuration_title);

      var newCell = newRow.insertCell(1);
      newCell.innerHTML = htmlSpecialChars(record.configuration_value).replace(/([^>]?)\n/g, '$1<br />\n'); // nl2br() in javascript

      newCell = newRow.insertCell(2);
      newCell.innerHTML = '<a href="' + configEditLink.replace('CONFIGID', parseInt(record.configuration_id)) + '">' + configEditLinkIcon + '</a>';
      newCell.align = 'right';

      newCell = newRow.insertCell(3);
      newCell.innerHTML = '<input type="checkbox" name="batch[]" value="' + parseInt(record.configuration_id) + '" id="batch' + parseInt(record.configuration_id) + '" />';
      newCell.align = 'center';

      rowCounter++;
    }
  }
</script>

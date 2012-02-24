<h1>{value}page_icon{value}<a href="{link}{link}">{value}page_title{value}</a></h1>

{widget}message_stack{widget}

<form id="liveSearchForm">
  <input type="text" name="search" id="liveSearchField" class="searchField" placeholder="{lang}placeholder_search{lang}" /><button type="button" id="buttonReset" onclick="osC_DataTable.reset();">{lang}button_reset{lang}</button>
</form>

<script>
$('#buttonReset').button();
</script>

<div style="padding: 20px 5px 5px 5px; height: 16px;">
  <span id="batchTotalPages"></span>
  <span id="batchPageLinks"></span>
</div>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="configurationDataTable">
  <thead>
    <tr>
      <th>{lang}table_heading_groups{lang}</th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th>&nbsp;</th>
    </tr>
  </tfoot>
  <tbody>
  </tbody>
</table>

</form>

<div style="padding: 2px; min-height: 16px;">
  <span id="dataTableLegend"></span>
  <span id="batchPullDownMenu"></span>
</div>

<script>
  var moduleParamsCookieName = 'oscom_admin_' + pageModule;
  var dataTablePageSetName = 'page';

  var moduleParams = new Object();
  moduleParams[dataTablePageSetName] = 1;
  moduleParams['search'] = '';

  if ( $.cookie(moduleParamsCookieName) != null ) {
    moduleParams = $.secureEvalJSON($.cookie(moduleParamsCookieName));
  }

  var dataTableName = 'configurationDataTable';
  var dataTableDataURL = '{rpclink}GetAll{rpclink}';

  var groupLink = '{link}||id=GROUPID{link}';
  var groupLinkIcon = '{icon}folder.png{icon}';

  var osC_DataTable = new osC_DataTable();
  osC_DataTable.load();

  function feedDataTable(data) {
    var rowCounter = 0;

    for ( var r in data.entries ) {
      var record = data.entries[r];

      var newRow = $('#' + dataTableName)[0].tBodies[0].insertRow(rowCounter);
      newRow.id = 'row' + parseInt(record.configuration_group_id);

      $('#row' + parseInt(record.configuration_group_id)).hover( function() { $(this).addClass('mouseOver'); }, function() { $(this).removeClass('mouseOver'); }).css('cursor', 'pointer');

      var newCell = newRow.insertCell(0);
      newCell.innerHTML = groupLinkIcon + '&nbsp;<a href="' + groupLink.replace('GROUPID', parseInt(record.configuration_group_id)) + '" class="parent">' + htmlSpecialChars(record.configuration_group_title) + '</a><span style="float: right;">(' + parseInt(record.total_entries) + ')</span>';

      rowCounter++;
    }
  }
</script>

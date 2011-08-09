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
  <?php echo HTML::inputField('search', null, 'id="liveSearchField" class="searchField" placeholder="' . OSCOM::getDef('placeholder_search') . '"') . HTML::button(array('type' => 'button', 'params' => 'onclick="osC_DataTable.reset();"', 'title' => OSCOM::getDef('button_reset'))) . '&nbsp;' . HTML::selectMenu('cid', array_merge(array(array('id' => '0', 'text' => OSCOM::getDef('top_category'))), $OSCOM_Application->getCategoryList())); ?>

  <span style="float: right;">

<?php
  if ( $OSCOM_Application->getCurrentCategoryID() > 0 ) {
    echo HTML::button(array('href' => OSCOM::getLink(null, null, 'cid=' . $OSCOM_CategoryTree->getParentID($OSCOM_Application->getCurrentCategoryID())), 'priority' => 'secondary', 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))) . ' ';
  }

  echo HTML::button(array('href' => OSCOM::getLink(null, null, 'Save&cid=' . $OSCOM_Application->getCurrentCategoryID()), 'icon' => 'plus', 'title' => OSCOM::getDef('button_insert')));
?>

  </span>
</form>

<script>
$(function() {
  $('#cid').change(function() {
    window.location.href='<?php echo OSCOM::getLink(null, null, 'cid=CATEGORYID'); ?>'.replace('CATEGORYID', $('#cid option:selected').val());
  });
});
</script>

<div style="padding: 20px 5px 5px 5px; height: 16px;">
  <span id="batchTotalPages"></span>
  <span id="batchPageLinks"></span>
</div>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="productsDataTable">
  <thead>
    <tr>
      <th><?php echo OSCOM::getDef('table_heading_products'); ?></th>
      <th><?php echo OSCOM::getDef('table_heading_price'); ?></th>
      <th><?php echo OSCOM::getDef('table_heading_quantity'); ?></th>
      <th width="150"><?php echo OSCOM::getDef('table_heading_action'); ?></th>
      <th align="center" width="20"><?php echo HTML::checkboxField('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="4"><?php echo HTML::submitImage(HTML::iconRaw('copy.png'), OSCOM::getDef('icon_copy'), 'onclick="document.batch.action=\'' . OSCOM::getLink(null, null, 'BatchCopy&cid=' . $OSCOM_Application->getCurrentCategoryID()) . '\';"') . '&nbsp;<a href="#" onclick="$(\'#dialogBatchDeleteConfirm\').dialog(\'open\'); return false;">' . HTML::icon('trash.png') . '</a>'; ?></th>
      <th align="center" width="20"><?php echo HTML::checkboxField('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>
  </tbody>
</table>

</form>

<div style="padding: 2px;">
  <span id="dataTableLegend"><?php echo '<b>' . OSCOM::getDef('table_action_legend') . '</b> ' . HTML::icon('edit.png') . '&nbsp;' . OSCOM::getDef('icon_edit') . '&nbsp;&nbsp;' . HTML::icon('copy.png') . '&nbsp;' . OSCOM::getDef('icon_copy') . '&nbsp;&nbsp;' . HTML::icon('trash.png') . '&nbsp;' . OSCOM::getDef('icon_trash'); ?></span>
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

  var dataTableName = 'productsDataTable';
  var dataTableDataURL = '<?php echo OSCOM::getRPCLink(null, null, 'GetAll&cid=' . $OSCOM_Application->getCurrentCategoryID()); ?>';

  var productLink = '<?php echo OSCOM::getLink(null, null, 'Preview&cid=' . $OSCOM_Application->getCurrentCategoryID() . '&id=PRODUCTID'); ?>';
  var productLinkIcon = '<?php echo HTML::icon('products.png'); ?>';
  var productVariantLinkIcon = '<?php echo HTML::icon('attach.png'); ?>';

  var productEditLink = '<?php echo OSCOM::getLink(null, null, 'Save&cid=' . $OSCOM_Application->getCurrentCategoryID() . '&id=PRODUCTID'); ?>';
  var productEditLinkIcon = '<?php echo HTML::icon('edit.png'); ?>';

  var productCopyLink = '<?php echo OSCOM::getLink(null, null, 'Copy&cid=' . $OSCOM_Application->getCurrentCategoryID() . '&id=PRODUCTID'); ?>';
  var productCopyLinkIcon = '<?php echo HTML::icon('copy.png'); ?>';

  var productDeleteLink = '<?php echo OSCOM::getLink(null, null, 'Delete&cid=' . $OSCOM_Application->getCurrentCategoryID() . '&id=PRODUCTID'); ?>';
  var productDeleteLinkIcon = '<?php echo HTML::icon('trash.png'); ?>';

  var osC_DataTable = new osC_DataTable();
  osC_DataTable.load();

  function feedDataTable(data) {
    var rowCounter = 0;

    for ( var r in data.entries ) {
      var record = data.entries[r];

      var newRow = $('#' + dataTableName)[0].tBodies[0].insertRow(rowCounter);
      newRow.id = 'row' + parseInt(record.products_id);

      $('#row' + parseInt(record.products_id)).hover( function() { $(this).addClass('mouseOver'); }, function() { $(this).removeClass('mouseOver'); }).click(function(event) {
        if (event.target.type !== 'checkbox') {
          $(':checkbox', this).trigger('click');
        }
      }).css('cursor', 'pointer');


      if ( parseInt(record.has_children) == 1 ) {
        var useProductLinkIcon = productVariantLinkIcon;
      } else {
        var useProductLinkIcon = productLinkIcon;
      }

      var newCell = newRow.insertCell(0);
      newCell.innerHTML = '<a href="' + productLink.replace('PRODUCTID', parseInt(record.products_id)) + '">' + useProductLinkIcon + '&nbsp;' + htmlSpecialChars(record.products_name) + '</a>';

      newCell = newRow.insertCell(1);
      newCell.innerHTML = htmlSpecialChars(record.products_price_formatted);

      newCell = newRow.insertCell(2);
      newCell.innerHTML = htmlSpecialChars(record.products_quantity);

      newCell = newRow.insertCell(3);
      newCell.innerHTML = '<a href="' + productEditLink.replace('PRODUCTID', parseInt(record.products_id)) + '">' + productEditLinkIcon + '</a>&nbsp;<a href="' + productCopyLink.replace('PRODUCTID', parseInt(record.products_id)) + '">' + productCopyLinkIcon + '</a>&nbsp;<a href="' + productDeleteLink.replace('PRODUCTID', parseInt(record.products_id)) + '">' + productDeleteLinkIcon + '</a>';
      newCell.align = 'right';

      newCell = newRow.insertCell(4);
      newCell.innerHTML = '<input type="checkbox" name="batch[]" value="' + parseInt(record.products_id) + '" id="batch' + parseInt(record.products_id) + '" />';
      newCell.align = 'center';

      rowCounter++;
    }
  }
</script>

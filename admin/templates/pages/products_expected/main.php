<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }

  $Qproducts = $osC_Database->query('select p.products_id, p.products_date_available, pd.products_name from :table_products p, :table_products_description pd where p.products_date_available is not null and p.products_id = pd.products_id and pd.language_id = :language_id order by p.products_date_available');
  $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
  $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
  $Qproducts->bindInt(':language_id', $osC_Language->getID());
  $Qproducts->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qproducts->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php echo $Qproducts->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_ENTRIES); ?></td>
    <td align="right"><?php echo $Qproducts->displayBatchLinksPullDown('page', $osC_Template->getModule()); ?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo TABLE_HEADING_PRODUCTS; ?></th>
      <th><?php echo TABLE_HEADING_DATE_EXPECTED; ?></th>
      <th width="150"><?php echo TABLE_HEADING_ACTION; ?></th>
      <th align="center" width="20"><?php // echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="3"><?php // echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . IMAGE_DELETE . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchDelete') . '\';" />'; ?></th>
      <th align="center" width="20"><?php // echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>

<?php
  while ( $Qproducts->next() ) {
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td onclick="document.getElementById('batch<?php echo $Qproducts->valueInt('products_id'); ?>').checked = !document.getElementById('batch<?php echo $Qproducts->valueInt('products_id'); ?>').checked;"><?php echo $Qproducts->value('products_name'); ?></td>
      <td><?php echo osC_DateTime::getShort($Qproducts->value('products_date_available')); ?></td>
      <td align="right">

<?php
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&pID=' . $Qproducts->valueInt('products_id') . '&action=save'), osc_icon('configure.png', IMAGE_EDIT));
?>

      </td>
      <td align="center"><?php // echo osc_draw_checkbox_field('batch[]', $Qproducts->valueInt('products_id'), null, 'id="batch' . $Qproducts->valueInt('products_id') . '"'); ?></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

</form>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . TEXT_LEGEND . '</b> ' . osc_icon('configure.png', IMAGE_EDIT) . '&nbsp;' . IMAGE_EDIT; ?></td>
    <td align="right"><?php echo $Qproducts->displayBatchLinksPullDown('page', $osC_Template->getModule()); ?></td>
  </tr>
</table>

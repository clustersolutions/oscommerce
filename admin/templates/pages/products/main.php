<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $categories_array = array();

  foreach ($osC_CategoryTree->getTree() as $value) {
    $categories_array[] = array('id' => $value['id'],
                                'text' => $value['title']);
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<form name="search" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT); ?>" method="get"><?php echo osc_draw_hidden_field($osC_Template->getModule()); ?>

<p align="right">

<?php
  echo HEADING_TITLE_SEARCH . ' ' . osc_draw_input_field('search') . osc_draw_pull_down_menu('cPath', array_merge(array(array('id' => '', 'text' => '-- ' . TEXT_TOP . ' --')), $categories_array)) . '<input type="submit" value="GO" class="operationButton" />' .
       '<input type="button" value="' . IMAGE_INSERT . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search'] . '&action=save') . '\';" class="infoBoxButton" />';
?>

</p>

</form>

<?php
  if ( $current_category_id > 0 ) {
    $osC_CategoryTree->setBreadcrumbUsage(false);

    $in_categories = array($current_category_id);

    foreach($osC_CategoryTree->getTree($current_category_id) as $category) {
      $in_categories[] = $category['id'];
    }

    $Qproducts = $osC_Database->query('select distinct p.products_id, pd.products_name, p.products_quantity, p.products_price, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status from :table_products p, :table_products_description pd, :table_products_to_categories p2c where p.products_id = pd.products_id and pd.language_id = :language_id and p.products_id = p2c.products_id and p2c.categories_id in (:categories_id)');
    $Qproducts->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
    $Qproducts->bindRaw(':categories_id', implode(',', $in_categories));
  } else {
    $Qproducts = $osC_Database->query('select p.products_id, pd.products_name, p.products_quantity, p.products_price, p.products_date_added, p.products_last_modified, p.products_date_available, p.products_status from :table_products p, :table_products_description pd where p.products_id = pd.products_id and pd.language_id = :language_id');
  }

  if ( !empty($_GET['search']) ) {
    $Qproducts->appendQuery('and pd.products_name like :products_name');
    $Qproducts->bindValue(':products_name', '%' . $_GET['search'] . '%');
  }

  $Qproducts->appendQuery('order by pd.products_name');
  $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
  $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
  $Qproducts->bindInt(':language_id', $osC_Language->getID());
  $Qproducts->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, (!empty($_GET['search']) ? 'distinct p.products_id' : ''));
  $Qproducts->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php echo $Qproducts->getBatchTotalPages(TEXT_DISPLAY_NUMBER_OF_ENTRIES); ?></td>
    <td align="right"><?php echo $Qproducts->getBatchPageLinks('page', $osC_Template->getModule() . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search'], false); ?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo TABLE_HEADING_PRODUCTS; ?></th>
      <th><?php echo TABLE_HEADING_PRICE; ?></th>
      <th><?php echo TABLE_HEADING_QUANTITY; ?></th>
      <th width="150"><?php echo TABLE_HEADING_ACTION; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="4"><?php echo '<input type="image" src="' . osc_icon_raw('copy.png') . '" title="' . IMAGE_COPY . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search'] . '&action=batchCopy') . '\';" />&nbsp;<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . IMAGE_DELETE . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search'] . '&action=batchDelete') . '\';" />'; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>

<?php
  while ($Qproducts->next()) {
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" <?php echo (($Qproducts->valueInt('products_status') !== 1) ? 'class="deactivatedRow"' : '') ?>>
      <td><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search'] . '&pID=' . $Qproducts->valueInt('products_id') . '&action=preview'), osc_image('images/icons/preview.gif', ICON_PREVIEW) . '&nbsp;' . $Qproducts->value('products_name')); ?></td>
      <td align="right"><?php echo $osC_Currencies->format($Qproducts->value('products_price')); ?></td>
      <td align="right"><?php echo $Qproducts->valueInt('products_quantity'); ?></td>
      <td align="right">

<?php
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search'] . '&pID=' . $Qproducts->valueInt('products_id') . '&action=save'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;' .
         osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search'] . '&pID=' . $Qproducts->valueInt('products_id') . '&action=copy'), osc_icon('copy.png', IMAGE_COPY)) . '&nbsp;' .
         osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search'] . '&pID=' . $Qproducts->valueInt('products_id') . '&action=delete'), osc_icon('trash.png', IMAGE_DELETE));
?>

      </td>
      <td align="center"><?php echo osc_draw_checkbox_field('batch[]', $Qproducts->valueInt('products_id'), null, 'id="batch' . $Qproducts->valueInt('products_id') . '"'); ?></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

</form>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . TEXT_LEGEND . '</b> ' . osc_icon('configure.png', IMAGE_EDIT) . '&nbsp;' . IMAGE_EDIT . '&nbsp;&nbsp;' . osc_icon('copy.png', IMAGE_COPY) . '&nbsp;' . IMAGE_COPY . '&nbsp;&nbsp;' . osc_icon('trash.png', IMAGE_DELETE) . '&nbsp;' . IMAGE_DELETE; ?></td>
    <td align="right"><?php echo $Qproducts->getBatchPagesPullDownMenu('page', $osC_Template->getModule() . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search']); ?></td>
  </tr>
</table>

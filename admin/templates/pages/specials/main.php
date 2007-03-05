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
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<p align="right"><?php echo '<input type="button" value="' . IMAGE_INSERT . '" onclick="document.location.href=\'' . osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=save') . '\';" class="infoBoxButton" />'; ?></p>

<?php
  $Qspecials = $osC_Database->query('select p.products_id, pd.products_name, p.products_price, s.specials_id, s.specials_new_products_price, s.specials_date_added, s.specials_last_modified, s.expires_date, s.date_status_change, s.status from :table_products p, :table_specials s, :table_products_description pd where p.products_id = pd.products_id and pd.language_id = :language_id and p.products_id = s.products_id order by pd.products_name');
  $Qspecials->bindTable(':table_specials', TABLE_SPECIALS);
  $Qspecials->bindTable(':table_products', TABLE_PRODUCTS);
  $Qspecials->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
  $Qspecials->bindInt(':language_id', $osC_Language->getID());
  $Qspecials->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qspecials->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php echo $Qspecials->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_ENTRIES); ?></td>
    <td align="right"><?php echo $Qspecials->displayBatchLinksPullDown('page', $osC_Template->getModule()); ?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo TABLE_HEADING_PRODUCTS; ?></th>
      <th><?php echo TABLE_HEADING_PRODUCTS_PRICE; ?></th>
      <th width="150"><?php echo TABLE_HEADING_ACTION; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="3"><?php echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . IMAGE_DELETE . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchDelete') . '\';" />'; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>

<?php
  while ( $Qspecials->next() ) {
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" <?php echo (($Qspecials->valueInt('status') !== 1) ? 'class="deactivatedRow"' : '') ?>>
      <td onclick="document.getElementById('batch<?php echo $Qspecials->valueInt('specials_id'); ?>').checked = !document.getElementById('batch<?php echo $Qspecials->valueInt('specials_id'); ?>').checked;"><?php echo $Qspecials->value('products_name'); ?></td>
      <td><span class="oldPrice"><?php echo $osC_Currencies->format($Qspecials->value('products_price')); ?></span> <span class="specialPrice"><?php echo $osC_Currencies->format($Qspecials->value('specials_new_products_price')); ?></span></td>
      <td align="right">

<?php
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&sID=' . $Qspecials->valueInt('specials_id') . '&action=save'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;' .
         osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&sID=' . $Qspecials->valueInt('specials_id') . '&action=delete'), osc_icon('trash.png', IMAGE_DELETE));
?>

      </td>
      <td align="center"><?php echo osc_draw_checkbox_field('batch[]', $Qspecials->valueInt('specials_id'), null, 'id="batch' . $Qspecials->valueInt('specials_id') . '"'); ?></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

</form>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . TEXT_LEGEND . '</b> ' . osc_icon('configure.png', IMAGE_EDIT) . '&nbsp;' . IMAGE_EDIT . '&nbsp;&nbsp;' . osc_icon('trash.png', IMAGE_DELETE) . '&nbsp;' . IMAGE_DELETE; ?></td>
    <td align="right"><?php echo $Qspecials->displayBatchLinksPullDown('page', $osC_Template->getModule()); ?></td>
  </tr>
</table>

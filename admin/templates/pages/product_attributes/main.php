<?php
/*
  $Id: products_attributes.php 1027 2006-10-20 09:41:26Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

/*
      if (DOWNLOAD_ENABLED == '1') {
        $download_query_raw ="select products_attributes_filename, products_attributes_maxdays, products_attributes_maxcount
                              from " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . "
                              where products_attributes_id='" . $attributes_values['products_attributes_id'] . "'";
        $download_query = tep_db_query($download_query_raw);
        if (tep_db_num_rows($download_query) > 0) {
          $download = tep_db_fetch_array($download_query);
          $products_attributes_filename = $download['products_attributes_filename'];
          $products_attributes_maxdays  = $download['products_attributes_maxdays'];
          $products_attributes_maxcount = $download['products_attributes_maxcount'];
        }
?>
          <tr class="<?php echo (!($rows % 2)? 'attributes-even' : 'attributes-odd');?>">
            <td>&nbsp;</td>
            <td colspan="5">
              <table>
                <tr class="<?php echo (!($rows % 2)? 'attributes-even' : 'attributes-odd');?>">
                  <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_DOWNLOAD; ?>&nbsp;</td>
                  <td class="smallText"><?php echo TABLE_TEXT_FILENAME; ?></td>
                  <td class="smallText"><?php echo tep_draw_input_field('products_attributes_filename', $products_attributes_filename, 'size="15"'); ?>&nbsp;</td>
                  <td class="smallText"><?php echo TABLE_TEXT_MAX_DAYS; ?></td>
                  <td class="smallText"><?php echo tep_draw_input_field('products_attributes_maxdays', $products_attributes_maxdays, 'size="5"'); ?>&nbsp;</td>
                  <td class="smallText"><?php echo TABLE_TEXT_MAX_COUNT; ?></td>
                  <td class="smallText"><?php echo tep_draw_input_field('products_attributes_maxcount', $products_attributes_maxcount, 'size="5"'); ?>&nbsp;</td>
                </tr>
              </table>
            </td>
            <td>&nbsp;</td>
          </tr>
<?php
      }
*/
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<p align="right"><?php echo '<input type="button" value="' . $osC_Language->get('button_insert') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=save') . '\';" class="infoBoxButton" />'; ?></p>

<?php
  $Qgroups = $osC_Database->query('select products_options_id, products_options_name from :table_products_options where language_id = :language_id order by products_options_name');
  $Qgroups->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
  $Qgroups->bindInt(':language_id', $osC_Language->getID());
  $Qgroups->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qgroups->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php echo $Qgroups->getBatchTotalPages($osC_Language->get('batch_results_number_of_entries')); ?></td>
    <td align="right"><?php echo $Qgroups->getBatchPageLinks('page', $osC_Template->getModule(), false); ?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo $osC_Language->get('table_heading_attribute_groups'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_total_entries'); ?></th>
      <th width="150"><?php echo $osC_Language->get('table_heading_action'); ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="3"><?php echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . $osC_Language->get('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchDelete') . '\';" />'; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>

<?php
  while ( $Qgroups->next() ) {
    $Qentries = $osC_Database->query('select count(*) as total_entries from :table_products_options_values_to_products_options where products_options_id = :products_options_id');
    $Qentries->bindTable(':table_products_options_values_to_products_options', TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS);
    $Qentries->bindInt(':products_options_id', $Qgroups->valueInt('products_options_id'));
    $Qentries->execute();
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td onclick="document.getElementById('batch<?php echo $Qgroups->valueInt('products_options_id'); ?>').checked = !document.getElementById('batch<?php echo $Qgroups->valueInt('products_options_id'); ?>').checked;"><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $Qgroups->valueInt('products_options_id') . '&page=' . $_GET['page']), osc_icon('folder.png') . '&nbsp;' . $Qgroups->value('products_options_name')); ?></td>
      <td><?php echo $Qentries->valueInt('total_entries'); ?></td>
      <td align="right">

<?php
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&paID=' . $Qgroups->valueInt('products_options_id') . '&action=save'), osc_icon('edit.png')) . '&nbsp;' .
         osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&paID=' . $Qgroups->valueInt('products_options_id') . '&action=delete'), osc_icon('trash.png'));
?>

      </td>
      <td align="center"><?php echo osc_draw_checkbox_field('batch[]', $Qgroups->valueInt('products_options_id'), null, 'id="batch' . $Qgroups->valueInt('products_options_id') . '"'); ?></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

</form>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . $osC_Language->get('table_action_legend') . '</b> ' . osc_icon('edit.png') . '&nbsp;' . $osC_Language->get('icon_edit') . '&nbsp;&nbsp;' . osc_icon('trash.png') . '&nbsp;' . $osC_Language->get('icon_trash'); ?></td>
    <td align="right"><?php echo $Qgroups->getBatchPagesPullDownMenu('page', $osC_Template->getModule()); ?></td>
  </tr>
</table>

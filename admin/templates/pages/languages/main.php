<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<p align="right"><?php echo '<input type="button" value="' . $osC_Language->get('button_import') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=import') . '\';" class="infoBoxButton" />'; ?></p>

<?php
  $Qlanguages = $osC_Database->query('select * from :table_languages order by sort_order, name');
  $Qlanguages->bindTable(':table_languages', TABLE_LANGUAGES);
  $Qlanguages->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qlanguages->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php echo $Qlanguages->getBatchTotalPages($osC_Language->get('batch_results_number_of_entries')); ?></td>
    <td align="right"><?php echo $Qlanguages->getBatchPageLinks('page', $osC_Template->getModule(), false); ?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo $osC_Language->get('table_heading_languages'); ?></th>
      <th width="20">&nbsp;</th>
      <th><?php echo $osC_Language->get('table_heading_total_definitions'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_code'); ?></th>
      <th width="150"><?php echo $osC_Language->get('table_heading_action'); ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="5"><?php echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . $osC_Language->get('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchDelete') . '\';" />'; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>

<?php
  while ($Qlanguages->next()) {
    $Qdef = $osC_Database->query('select count(*) as total_definitions from :table_languages_definitions where languages_id = :languages_id');
    $Qdef->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
    $Qdef->bindInt(':languages_id', $Qlanguages->valueInt('languages_id'));
    $Qdef->execute();

    $languages_name = $Qlanguages->value('name');

    if ($Qlanguages->value('code') == DEFAULT_LANGUAGE) {
      $languages_name .= ' (' . $osC_Language->get('default_entry') . ')';
    }
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td onclick="document.getElementById('batch<?php echo $Qlanguages->valueInt('languages_id'); ?>').checked = !document.getElementById('batch<?php echo $Qlanguages->valueInt('languages_id'); ?>').checked;"><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $Qlanguages->valueInt('languages_id') . '&page=' . $_GET['page']), osc_icon('folder.png') . '&nbsp;' . $languages_name); ?></td>
      <td align="center"><?php echo $osC_Language->showImage($Qlanguages->value('code')); ?></td>
      <td><?php echo $Qdef->value('total_definitions'); ?></td>
      <td><?php echo $Qlanguages->value('code'); ?></td>
      <td align="right">

<?php
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&lID=' . $Qlanguages->valueInt('languages_id') . '&action=save'), osc_icon('edit.png')) . '&nbsp;' .
         osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&lID=' . $Qlanguages->valueInt('languages_id') . '&action=export'), osc_icon('export.png')) . '&nbsp;' .
         osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&lID=' . $Qlanguages->valueInt('languages_id') . '&action=delete'), osc_icon('trash.png'));
?>

      </td>
      <td align="center"><?php echo osc_draw_checkbox_field('batch[]', $Qlanguages->valueInt('languages_id'), null, 'id="batch' . $Qlanguages->valueInt('languages_id') . '"'); ?></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

</form>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . $osC_Language->get('table_action_legend') . '</b> ' . osc_icon('edit.png') . '&nbsp;' . $osC_Language->get('icon_edit') . '&nbsp;&nbsp;' . osc_icon('export.png') . '&nbsp;' . $osC_Language->get('icon_export') . '&nbsp;&nbsp;' . osc_icon('trash.png') . '&nbsp;' . $osC_Language->get('icon_trash'); ?></td>
    <td align="right"><?php echo $Qlanguages->getBatchPagesPullDownMenu('page', $osC_Template->getModule()); ?></td>
  </tr>
</table>

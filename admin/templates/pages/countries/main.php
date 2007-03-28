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

<p align="right"><?php echo '<input type="button" value="' . $osC_Language->get('button_insert') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=save') . '\';" class="infoBoxButton" />'; ?></p>

<?php
  $Qcountries = $osC_Database->query('select SQL_CALC_FOUND_ROWS * from :table_countries order by countries_name');
  $Qcountries->bindTable(':table_countries', TABLE_COUNTRIES);
  $Qcountries->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qcountries->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php echo $Qcountries->getBatchTotalPages($osC_Language->get('batch_results_number_of_entries')); ?></td>
    <td align="right"><?php echo $Qcountries->getBatchPageLinks('page', $osC_Template->getModule(), false); ?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo $osC_Language->get('table_heading_countries'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_code'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_total_zones'); ?></th>
      <th width="150"><?php echo $osC_Language->get('table_heading_action'); ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="4"><?php echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . $osC_Language->get('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchDelete') . '\';" />'; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>

<?php
  while ($Qcountries->next()) {
    $Qzones = $osC_Database->query('select count(*) as total_zones from :table_zones where zone_country_id = :zone_country_id');
    $Qzones->bindTable(':table_zones', TABLE_ZONES);
    $Qzones->bindInt(':zone_country_id', $Qcountries->valueInt('countries_id'));
    $Qzones->execute();
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td onclick="document.getElementById('batch<?php echo $Qcountries->valueInt('countries_id'); ?>').checked = !document.getElementById('batch<?php echo $Qcountries->valueInt('countries_id'); ?>').checked;"><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $Qcountries->valueInt('countries_id') . '&page=' . $_GET['page']), osc_icon('folder.png') . '&nbsp;' . $Qcountries->value('countries_name')); ?></td>
      <td><?php echo $Qcountries->value('countries_iso_code_2') . '&nbsp;&nbsp;&nbsp;&nbsp;' . $Qcountries->value('countries_iso_code_3'); ?></td>
      <td><?php echo $Qzones->valueInt('total_zones'); ?></td>
      <td align="right">

<?php
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cID=' . $Qcountries->valueInt('countries_id') . '&action=save'), osc_icon('edit.png')) . '&nbsp;' .
         osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cID=' . $Qcountries->valueInt('countries_id') . '&action=delete'), osc_icon('trash.png'));
?>

      </td>
      <td align="center"><?php echo osc_draw_checkbox_field('batch[]', $Qcountries->valueInt('countries_id'), null, 'id="batch' . $Qcountries->valueInt('countries_id') . '"'); ?></td>
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
    <td align="right"><?php echo $Qcountries->getBatchPagesPullDownMenu('page', $osC_Template->getModule()); ?></td>
  </tr>
</table>

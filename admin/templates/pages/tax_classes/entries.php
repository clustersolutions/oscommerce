<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $osC_Tax = new osC_Tax_Admin();
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<p align="right"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="infoBoxButton" /> <input type="button" value="' . IMAGE_INSERT . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&action=entrySave') . '\';" class="infoBoxButton" />'; ?></p>

<?php
  $Qrates = $osC_Database->query('select r.tax_rates_id, r.tax_priority, r.tax_rate, r.tax_description, r.date_added, r.last_modified, z.geo_zone_id, z.geo_zone_name from :table_tax_rates r, :table_geo_zones z where r.tax_class_id = :tax_class_id and r.tax_zone_id = z.geo_zone_id order by r.tax_priority, z.geo_zone_name');
  $Qrates->bindTable(':table_tax_rates', TABLE_TAX_RATES);
  $Qrates->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
  $Qrates->bindInt(':tax_class_id', $_GET[$osC_Template->getModule()]);
  $Qrates->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php echo sprintf(TEXT_DISPLAY_NUMBER_OF_ENTRIES, ($Qrates->numberOfRows() > 0 ? 1 : 0), $Qrates->numberOfRows(), $Qrates->numberOfRows()); ?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th width="100"><?php echo $osC_Language->get('table_heading_tax_rate_priority'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_tax_rate_zone'); ?></th>
      <th width="100"><?php echo $osC_Language->get('table_heading_tax_rate'); ?></th>
      <th width="150"><?php echo $osC_Language->get('table_heading_action'); ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="4"><?php echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . IMAGE_DELETE . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&action=batchDeleteEntries') . '\';" />'; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>

<?php
  while ($Qrates->next()) {
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" title="<?php echo $Qrates->valueProtected('tax_description'); ?>">
      <td><?php echo $Qrates->valueInt('tax_priority'); ?></td>
      <td><?php echo $Qrates->value('geo_zone_name'); ?></td>
      <td><?php echo $osC_Tax->displayTaxRateValue($Qrates->valueDecimal('tax_rate')); ?></td>
      <td align="right">

<?php
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&trID=' . $Qrates->valueInt('tax_rates_id') . '&action=entrySave'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;' .
         osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&trID=' . $Qrates->valueInt('tax_rates_id') . '&action=entryDelete'), osc_icon('trash.png', IMAGE_DELETE));
?>

      </td>
      <td align="center"><?php echo osc_draw_checkbox_field('batch[]', $Qrates->valueInt('tax_rates_id'), null, 'id="batch' . $Qrates->valueInt('tax_rates_id') . '"'); ?></td>
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
  </tr>
</table>

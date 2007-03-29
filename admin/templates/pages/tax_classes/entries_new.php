<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $zones_array = array();

  $Qzones = $osC_Database->query('select geo_zone_id, geo_zone_name from :table_geo_zones order by geo_zone_name');
  $Qzones->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
  $Qzones->execute();

  while ($Qzones->next()) {
    $zones_array[] = array('id' => $Qzones->valueInt('geo_zone_id'),
                           'text' => $Qzones->value('geo_zone_name'));
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png') . ' ' . $osC_Language->get('action_heading_new_tax_rate'); ?></div>
<div class="infoBoxContent">
  <form name="trNew" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&action=entrySave'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_new_tax_rate'); ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_tax_rate_zone_group') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_pull_down_menu('tax_zone_id', $zones_array); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_tax_rate') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('tax_rate', null, 'style="width: 100%;"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_tax_rate_description') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('tax_description', null, 'style="width: 100%;"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_tax_rate_priority') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('tax_priority', null, 'style="width: 100%;"'); ?></td>
    </tr>
  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

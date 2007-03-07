<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
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

<div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_NEW_TAX_RATE; ?></div>
<div class="infoBoxContent">
  <form name="trNew" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&action=entrySave'); ?>" method="post">

  <p><?php echo TEXT_INFO_INSERT_INTRO; ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_INFO_ZONE_NAME . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_pull_down_menu('tax_zone_id', $zones_array); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_INFO_TAX_RATE . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('tax_rate', null, 'style="width: 100%;"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_INFO_TAX_RATE_DESCRIPTION . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('tax_description', null, 'style="width: 100%;"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_INFO_TAX_RATE_PRIORITY . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('tax_priority', null, 'style="width: 100%;"'); ?></td>
    </tr>
  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_INSERT . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_ZoneGroups_Admin::getData($_GET['zID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_NEW_ZONE; ?></div>
<div class="infoBoxContent">
  <form name="zEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&zID=' . $osC_ObjectInfo->get('geo_zone_id') . '&action=save'); ?>" method="post">

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_ZONE_NAME . '</b>'; ?></td>
      <td class="smallText" width="60%"><?php echo osc_draw_input_field('zone_name', $osC_ObjectInfo->get('geo_zone_name'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_ZONE_DESCRIPTION . '</b>'; ?></td>
      <td class="smallText" width="60%"><?php echo osc_draw_input_field('zone_description', $osC_ObjectInfo->get('geo_zone_description'), 'style="width: 100%"'); ?></td>
    </tr>
  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

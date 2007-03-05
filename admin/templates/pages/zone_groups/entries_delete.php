<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_ZoneGroups_Admin::getEntryData($_GET['zeID']));

  if ( $osC_ObjectInfo->get('zone_country_id') < 1 ) {
    $osC_ObjectInfo->set('countries_name', TEXT_ALL_COUNTRIES);
  }

  if ( $osC_ObjectInfo->get('zone_id') < 1 ) {
    $osC_ObjectInfo->set('zone_name', PLEASE_SELECT);
  }
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $osC_ObjectInfo->get('countries_name') . ': ' . $osC_ObjectInfo->get('zone_name'); ?></div>
<div class="infoBoxContent">
  <form name="zeDelete" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&zeID=' . $osC_ObjectInfo->get('association_id') . '&action=entryDelete'); ?>" method="post">

  <p><?php echo TEXT_INFO_DELETE_SUB_ZONE_INTRO; ?></p>

  <p><?php echo '<b>' . $osC_ObjectInfo->get('countries_name') . ': ' . $osC_ObjectInfo->get('zone_name') . '</b>'; ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

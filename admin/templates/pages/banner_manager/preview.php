<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_BannerManager_Admin::getData($_GET['bID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('windows.png', IMAGE_PREVIEW) . ' ' . $osC_ObjectInfo->get('banners_title'); ?></div>
<div class="infoBoxContent">

<?php
  if ( !osc_empty($osC_ObjectInfo->get('banners_html_text')) ) {
    echo $osC_ObjectInfo->get('banners_html_text');
  } else {
    echo osc_image('../images/' . $osC_ObjectInfo->get('banners_image'), $osC_ObjectInfo->get('banners_title'));
  }
?>

  <p align="center"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>
</div>

<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_AdministratorsLog::getData($_GET['lID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $osC_ObjectInfo->get('user_name') . ' &raquo; ' . $osC_ObjectInfo->get('module_action') . ' &raquo; ' . $osC_ObjectInfo->get('module') . ' &raquo; ' . $osC_ObjectInfo->get('module_id'); ?></div>
<div class="infoBoxContent">
  <form name="lDelete" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu'] . '&lID=' . $osC_ObjectInfo->get('id') . '&action=delete'); ?>" method="post">

  <p><?php echo TEXT_DELETE_INTRO; ?></p>

  <p><?php echo '<b>' . $osC_ObjectInfo->get('user_name') . ' &raquo; ' . $osC_ObjectInfo->get('module_action') . ' &raquo; ' . $osC_ObjectInfo->get('module') . ' &raquo; ' . $osC_ObjectInfo->get('module_id') . '</b>'; ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

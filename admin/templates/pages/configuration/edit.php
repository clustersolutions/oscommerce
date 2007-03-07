<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Configuration_Admin::getData($_GET['cID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&gID=' . $_GET['gID']), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }

  if ( !osc_empty($osC_ObjectInfo->get('set_function')) ) {
    $value_field = osc_call_user_func($osC_ObjectInfo->get('set_function'), $osC_ObjectInfo->get('configuration_value'));
  } else {
    $value_field = osc_draw_input_field('configuration_value', $osC_ObjectInfo->get('configuration_value'), 'style="width: 100%;"');
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $osC_ObjectInfo->get('configuration_title'); ?></div>
<div class="infoBoxContent">
  <form name="cEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&gID=' . $_GET['gID'] . '&cID=' . $osC_ObjectInfo->get('configuration_id') . '&action=save'); ?>" method="post">

  <p><?php echo $osC_ObjectInfo->get('configuration_description'); ?></p>

  <p><?php echo '<b>' . $osC_ObjectInfo->get('configuration_title') . ':</b><br />' . $value_field; ?></p>

  <p><?php echo TEXT_INFO_LAST_MODIFIED . ' ' . (!osc_empty($osC_ObjectInfo->get('last_modified')) ? osC_DateTime::getShort($osC_ObjectInfo->get('last_modified')) : osC_DateTime::getShort($osC_ObjectInfo->get('date_added'))); ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&gID=' . $_GET['gID']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Language_Admin::getData($_GET['lID']));
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('export.png', IMAGE_EXPORT) . ' ' . $osC_ObjectInfo->get('name'); ?></div>
<div class="infoBoxContent">
  <form name="lExport" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&lID=' . $osC_ObjectInfo->get('languages_id') . '&action=export'); ?>" method="post">

  <p><?php echo TEXT_INFO_EXPORT_INTRO; ?></p>

<?php
  $Qgroups = $osC_Database->query('select distinct content_group from :table_languages_definitions where languages_id = :languages_id order by content_group');
  $Qgroups->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
  $Qgroups->bindInt(':languages_id', $osC_ObjectInfo->get('languages_id'));
  $Qgroups->execute();

  $groups_array = array();

  while ($Qgroups->next()) {
    $groups_array[] = array('id' => $Qgroups->value('content_group'),
                            'text' => $Qgroups->value('content_group'));
  }
?>

  <p>(<a href="javascript:selectAllFromPullDownMenu('groups');"><u>select all</u></a> | <a href="javascript:resetPullDownMenuSelection('groups');"><u>select none</u></a>)<br /><?php echo osc_draw_pull_down_menu('groups[]', $groups_array, array('account', 'checkout', 'general', 'index', 'info', 'order', 'products', 'search'), 'id="groups" size="10" multiple="multiple" style="width: 100%;"'); ?></p>

  <p><?php echo osc_draw_checkbox_field('include_data', array(array('id' => '', 'text' => TEXT_INFO_EXPORT_WITH_DATA)), true); ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_EXPORT . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

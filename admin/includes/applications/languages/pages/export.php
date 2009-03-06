<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Languages_Admin::get($_GET['lID']));

  $groups_array = array();

  foreach ( osc_toObjectInfo(osC_Languages_Admin::getDefinitionGroups($osC_ObjectInfo->getInt('languages_id')))->get('entries') as $group ) {
    $groups_array[] = array('id' => $group['content_group'],
                            'text' => $group['content_group']);
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('export.png') . ' ' . $osC_ObjectInfo->getProtected('name'); ?></div>
<div class="infoBoxContent">
  <form name="lExport" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&lID=' . $osC_ObjectInfo->getInt('languages_id') . '&action=export'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_export_language'); ?></p>

  <fieldset>
    <div><p>(<a href="javascript:selectAllFromPullDownMenu('groups');"><u><?php echo $osC_Language->get('select_all'); ?></u></a> | <a href="javascript:resetPullDownMenuSelection('groups');"><u><?php echo $osC_Language->get('select_none'); ?></u></a>)<br /><?php echo osc_draw_pull_down_menu('groups[]', $groups_array, array('account', 'checkout', 'general', 'index', 'info', 'order', 'products', 'search'), 'id="groups" size="10" multiple="multiple"'); ?></p></div>

    <div><?php echo osc_draw_checkbox_field('include_data', array(array('id' => '', 'text' => $osC_Language->get('field_export_with_data'))), true); ?></div>
  </fieldset>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_export') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

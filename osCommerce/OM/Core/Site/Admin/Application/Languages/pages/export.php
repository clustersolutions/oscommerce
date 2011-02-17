<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\ObjectInfo;
  use osCommerce\OM\Core\Site\Admin\Application\Languages\Languages;
  use osCommerce\OM\Core\OSCOM;

  $OSCOM_ObjectInfo = new ObjectInfo(Languages::get($_GET['id']));

  $groups_array = array();

  foreach ( osc_toObjectInfo(Languages::getGroups($OSCOM_ObjectInfo->getInt('languages_id')))->get('entries') as $group ) {
    $groups_array[] = array('id' => $group['content_group'],
                            'text' => $group['content_group']);
  }
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('export.png') . ' ' . $OSCOM_ObjectInfo->getProtected('name'); ?></h3>

  <form name="lExport" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'Export&Process&id=' . $_GET['id']); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_export_language'); ?></p>

  <fieldset>
    <p>(<a href="javascript:selectAllFromPullDownMenu('groups');"><u><?php echo OSCOM::getDef('select_all'); ?></u></a> | <a href="javascript:resetPullDownMenuSelection('groups');"><u><?php echo OSCOM::getDef('select_none'); ?></u></a>)<br /><?php echo osc_draw_pull_down_menu('groups[]', $groups_array, array('account', 'checkout', 'general', 'index', 'info', 'order', 'products', 'search'), 'id="groups" size="10" multiple="multiple"'); ?></p>

    <p><?php echo osc_draw_checkbox_field('include_data', array(array('id' => '', 'text' => OSCOM::getDef('field_export_with_data'))), true); ?></p>
  </fieldset>

  <p><?php echo osc_draw_button(array('priority' => 'primary', 'icon' => 'triangle-1-nw', 'title' => OSCOM::getDef('button_export'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>

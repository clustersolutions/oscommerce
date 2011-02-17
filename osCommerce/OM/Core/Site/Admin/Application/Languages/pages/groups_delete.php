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

  $OSCOM_ObjectInfo = new ObjectInfo(Languages::getGroup($_GET['group']));
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('trash.png') . ' ' . osc_output_string_protected($_GET['group']); ?></h3>

  <form name="gDelete" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'DeleteGroup&Process&id=' . $_GET['id'] . '&group=' . $_GET['group']); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_delete_definition_group'); ?></p>

  <p><?php echo '<b>' . osc_output_string_protected($_GET['group']) . '</b>'; ?></p>

  <p>

<?php
  foreach ( $OSCOM_ObjectInfo->get('entries') as $l ) {
    echo Languages::get($l['languages_id'], 'name') . ': ' . (int)$l['total_entries'] . '<br />';
  }
?>

  </p>

  <p><?php echo osc_draw_button(array('priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::getDef('button_delete'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(null, null, 'id=' . $_GET['id']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>

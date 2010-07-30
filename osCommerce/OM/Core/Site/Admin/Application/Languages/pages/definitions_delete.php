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

  $OSCOM_ObjectInfo = new ObjectInfo(Languages::getDefinition($_GET['dID']));
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('trash.png') . ' ' . $OSCOM_ObjectInfo->getProtected('definition_key'); ?></h3>

  <form name="lDelete" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'DeleteDefinition&Process&id=' . $_GET['id'] . '&group=' . $_GET['group'] . '&dID=' . $_GET['dID']); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_delete_language_definition'); ?></p>

  <p><?php echo '<b>' . $OSCOM_ObjectInfo->getProtected('definition_key') . '</b>'; ?></p>

  <p><?php echo osc_draw_button(array('priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::getDef('button_delete'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(null, null, 'id=' . $_GET['id'] . '&group=' . $_GET['group']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>

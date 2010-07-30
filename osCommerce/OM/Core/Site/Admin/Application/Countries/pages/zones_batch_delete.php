<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\OSCOM;

  $Qzones = $OSCOM_Database->query('select zone_id, zone_name from :table_zones where zone_id in (":zone_id") order by zone_name');
  $Qzones->bindRaw(':zone_id', implode('", "', array_unique(array_filter($_POST['batch'], 'is_numeric'))));
  $Qzones->execute();

  $names_string = '';

  while ( $Qzones->next() ) {
    $names_string .= osc_draw_hidden_field('batch[]', $Qzones->valueInt('zone_id')) . '<b>' . $Qzones->valueProtected('zone_name') . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2);
  }
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('trash.png') . ' ' . OSCOM::getDef('action_heading_batch_delete_zones'); ?></h3>

  <form name="cDeleteBatch" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'BatchDeleteZones&Process&id=' . $_GET['id']); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_batch_delete_zones'); ?></p>

  <p><?php echo $names_string; ?></p>

  <p><?php echo osc_draw_button(array('priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::getDef('button_delete'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(null, null, 'id=' . $_GET['id']), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>

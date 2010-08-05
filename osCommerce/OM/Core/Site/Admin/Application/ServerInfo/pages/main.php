<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\ObjectInfo;
  use osCommerce\OM\Core\Site\Admin\Application\ServerInfo\ServerInfo;
  use osCommerce\OM\Core\OSCOM;
  
  $OSCOM_ObjectInfo = new ObjectInfo(ServerInfo::getServerInfo());
?>
  
<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }  
?>

<table border="0" width="100%" cellspacing="0" cellpadding="3">
  <tr>
    <td><b><?php echo OSCOM::getDef('field_server_host'); ?></b></td>
    <td><?php echo $OSCOM_ObjectInfo->get('host') . ' (' . $OSCOM_ObjectInfo->get('ip') . ')'; ?></td>
    <td><b><?php echo OSCOM::getDef('field_database_host'); ?></b></td>
    <td><?php echo $OSCOM_ObjectInfo->get('db_server') . ' (' . $OSCOM_ObjectInfo->get('db_ip') . ')'; ?></td>
  </tr>
  <tr>
    <td><b><?php echo OSCOM::getDef('field_server_operating_system'); ?></b></td>
    <td><?php echo $OSCOM_ObjectInfo->get('system') . ' ' . $OSCOM_ObjectInfo->get('kernel'); ?></td>
    <td><b><?php echo OSCOM::getDef('field_database_version'); ?></b></td>
    <td><?php echo $OSCOM_ObjectInfo->get('db_version'); ?></td>
  </tr>
  <tr>
    <td><b><?php echo OSCOM::getDef('field_server_date'); ?></b></td>
    <td><?php echo $OSCOM_ObjectInfo->get('date'); ?></td>
    <td><b><?php echo OSCOM::getDef('field_database_date'); ?></b></td>
    <td><?php echo $OSCOM_ObjectInfo->get('db_date'); ?></td>
  </tr>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  <tr>
    <td><b><?php echo OSCOM::getDef('field_server_up_time'); ?></b></td>
    <td colspan="3"><?php echo $OSCOM_ObjectInfo->get('uptime'); ?></td>
  </tr>
  <tr>
    <td><b><?php echo OSCOM::getDef('field_database_up_time'); ?></b></td>
    <td colspan="3"><?php echo $OSCOM_ObjectInfo->get('db_uptime'); ?></td>
  </tr>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  <tr>
    <td><b><?php echo OSCOM::getDef('field_http_server'); ?></b></td>
    <td><?php echo $OSCOM_ObjectInfo->get('http_server'); ?></td>
    <td><b><?php echo OSCOM::getDef('field_php_version'); ?></b></td>
    <td><?php echo 'PHP: ' . $OSCOM_ObjectInfo->get('php') . ' / Zend: ' . $OSCOM_ObjectInfo->get('zend') . ' (' . osc_link_object(OSCOM::getLink( $OSCOM_Template->getModule() . '&action=phpInfo'), OSCOM::getDef('more_information'), 'target="_blank"') . ')'; ?></td>
  </tr>
</table>

<br />

<table border="0" width="100%" cellspacing="0" cellpadding="3" style="border: 1px #000000 solid;">
  <tr>
    <td><b><?php echo PROJECT_VERSION; ?></b></td>
    <td align="right"><a href="http://www.oscommerce.com" target="_blank"><?php echo osc_image(OSCOM::getLink( $OSCOM_Template->getModule() . '&action=image'), 'osCommerce'); ?></a></td>
  </tr>
</table>
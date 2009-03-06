<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osc_get_system_information());
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<table border="0" width="100%" cellspacing="0" cellpadding="3">
  <tr>
    <td><b><?php echo $osC_Language->get('field_server_host'); ?></b></td>
    <td><?php echo $osC_ObjectInfo->get('host') . ' (' . $osC_ObjectInfo->get('ip') . ')'; ?></td>
    <td><b><?php echo $osC_Language->get('field_database_host'); ?></b></td>
    <td><?php echo $osC_ObjectInfo->get('db_server') . ' (' . $osC_ObjectInfo->get('db_ip') . ')'; ?></td>
  </tr>
  <tr>
    <td><b><?php echo $osC_Language->get('field_server_operating_system'); ?></b></td>
    <td><?php echo $osC_ObjectInfo->get('system') . ' ' . $osC_ObjectInfo->get('kernel'); ?></td>
    <td><b><?php echo $osC_Language->get('field_database_version'); ?></b></td>
    <td><?php echo $osC_ObjectInfo->get('db_version'); ?></td>
  </tr>
  <tr>
    <td><b><?php echo $osC_Language->get('field_server_date'); ?></b></td>
    <td><?php echo $osC_ObjectInfo->get('date'); ?></td>
    <td><b><?php echo $osC_Language->get('field_database_date'); ?></b></td>
    <td><?php echo $osC_ObjectInfo->get('db_date'); ?></td>
  </tr>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  <tr>
    <td><b><?php echo $osC_Language->get('field_server_up_time'); ?></b></td>
    <td colspan="3"><?php echo $osC_ObjectInfo->get('uptime'); ?></td>
  </tr>
  <tr>
    <td><b><?php echo $osC_Language->get('field_database_up_time'); ?></b></td>
    <td colspan="3"><?php echo $osC_ObjectInfo->get('db_uptime'); ?></td>
  </tr>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  <tr>
    <td><b><?php echo $osC_Language->get('field_http_server'); ?></b></td>
    <td><?php echo $osC_ObjectInfo->get('http_server'); ?></td>
    <td><b><?php echo $osC_Language->get('field_php_version'); ?></b></td>
    <td><?php echo 'PHP: ' . $osC_ObjectInfo->get('php') . ' / Zend: ' . $osC_ObjectInfo->get('zend') . ' (' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=phpInfo'), $osC_Language->get('more_information'), 'target="_blank"') . ')'; ?></td>
  </tr>
</table>

<br />

<table border="0" width="100%" cellspacing="0" cellpadding="3" style="border: 1px #000000 solid;">
  <tr>
    <td><b><?php echo PROJECT_VERSION; ?></b></td>
    <td align="right"><a href="http://www.oscommerce.com" target="_blank"><?php echo osc_image(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=image'), 'osCommerce'); ?></a></td>
  </tr>
</table>

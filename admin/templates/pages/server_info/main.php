<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osc_get_system_information());
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<table border="0" width="100%" cellspacing="0" cellpadding="3">
  <tr>
    <td><b><?php echo TITLE_SERVER_HOST; ?></b></td>
    <td><?php echo $osC_ObjectInfo->get('host') . ' (' . $osC_ObjectInfo->get('ip') . ')'; ?></td>
    <td><b><?php echo TITLE_DATABASE_HOST; ?></b></td>
    <td><?php echo $osC_ObjectInfo->get('db_server') . ' (' . $osC_ObjectInfo->get('db_ip') . ')'; ?></td>
  </tr>
  <tr>
    <td><b><?php echo TITLE_SERVER_OS; ?></b></td>
    <td><?php echo $osC_ObjectInfo->get('system') . ' ' . $osC_ObjectInfo->get('kernel'); ?></td>
    <td><b><?php echo TITLE_DATABASE; ?></b></td>
    <td><?php echo $osC_ObjectInfo->get('db_version'); ?></td>
  </tr>
  <tr>
    <td><b><?php echo TITLE_SERVER_DATE; ?></b></td>
    <td><?php echo $osC_ObjectInfo->get('date'); ?></td>
    <td><b><?php echo TITLE_DATABASE_DATE; ?></b></td>
    <td><?php echo $osC_ObjectInfo->get('db_date'); ?></td>
  </tr>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  <tr>
    <td><b><?php echo TITLE_SERVER_UP_TIME; ?></b></td>
    <td colspan="3"><?php echo $osC_ObjectInfo->get('uptime'); ?></td>
  </tr>
  <tr>
    <td><b><?php echo TITLE_DATABASE_UP_TIME; ?></b></td>
    <td colspan="3"><?php echo $osC_ObjectInfo->get('db_uptime'); ?></td>
  </tr>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  <tr>
    <td><b><?php echo TITLE_HTTP_SERVER; ?></b></td>
    <td><?php echo $osC_ObjectInfo->get('http_server'); ?></td>
    <td><b><?php echo TITLE_PHP_VERSION; ?></b></td>
    <td><?php echo 'PHP: ' . $osC_ObjectInfo->get('php') . ' / ' . TITLE_ZEND_VERSION . ' ' . $osC_ObjectInfo->get('zend') . ' (' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=phpInfo'), TEXT_MORE_INFORMATION, 'target="_blank"') . ')'; ?></td>
  </tr>
</table>

<br />

<table border="0" width="100%" cellspacing="0" cellpadding="3" style="border: 1px #000000 solid;">
  <tr>
    <td><b><?php echo PROJECT_VERSION; ?></b></td>
    <td align="right"><a href="http://www.oscommerce.com" target="_blank"><?php echo osc_image(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=image'), 'osCommerce'); ?></a></td>
  </tr>
</table>

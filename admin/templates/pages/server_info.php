<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  $system = osc_get_system_information();
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<table border="0" width="100%" cellspacing="0" cellpadding="3">
  <tr>
    <td class="smallText"><b><?php echo TITLE_SERVER_HOST; ?></b></td>
    <td class="smallText"><?php echo $system['host'] . ' (' . $system['ip'] . ')'; ?></td>
    <td class="smallText"><b><?php echo TITLE_DATABASE_HOST; ?></b></td>
    <td class="smallText"><?php echo $system['db_server'] . ' (' . $system['db_ip'] . ')'; ?></td>
  </tr>
  <tr>
    <td class="smallText"><b><?php echo TITLE_SERVER_OS; ?></b></td>
    <td class="smallText"><?php echo $system['system'] . ' ' . $system['kernel']; ?></td>
    <td class="smallText"><b><?php echo TITLE_DATABASE; ?></b></td>
    <td class="smallText"><?php echo $system['db_version']; ?></td>
  </tr>
  <tr>
    <td class="smallText"><b><?php echo TITLE_SERVER_DATE; ?></b></td>
    <td class="smallText"><?php echo $system['date']; ?></td>
    <td class="smallText"><b><?php echo TITLE_DATABASE_DATE; ?></b></td>
    <td class="smallText"><?php echo $system['db_date']; ?></td>
  </tr>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  <tr>
    <td class="smallText"><b><?php echo TITLE_SERVER_UP_TIME; ?></b></td>
    <td colspan="3" class="smallText"><?php echo $system['uptime']; ?></td>
  </tr>
  <tr>
    <td class="smallText"><b><?php echo TITLE_DATABASE_UP_TIME; ?></b></td>
    <td colspan="3" class="smallText"><?php echo $system['db_uptime']; ?></td>
  </tr>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  <tr>
    <td class="smallText"><b><?php echo TITLE_HTTP_SERVER; ?></b></td>
    <td class="smallText"><?php echo $system['http_server']; ?></td>
    <td class="smallText"><b><?php echo TITLE_PHP_VERSION; ?></b></td>
    <td class="smallText"><?php echo 'PHP: ' . $system['php'] . ' / ' . TITLE_ZEND_VERSION . ' ' . $system['zend'] . ' (' . osc_link_object(osc_href_link_admin(FILENAME_SERVER_INFO, 'action=phpInfo'), TEXT_MORE_INFORMATION, 'target="_blank"') . ')'; ?></td>
  </tr>
</table>

<br />

<table border="0" width="100%" cellspacing="0" cellpadding="3" style="border: 1px #000000 solid;">
  <tr>
    <td class="smallText"><b><?php echo PROJECT_VERSION; ?></b></td>
    <td class="smallText" align="right"><a href="http://www.oscommerce.com" target="_blank"><?php echo osc_image(osc_href_link_admin(FILENAME_SERVER_INFO, 'action=imageOsCommerce'), 'osCommerce'); ?></a></td>
  </tr>
</table>

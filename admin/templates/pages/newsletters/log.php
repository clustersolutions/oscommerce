<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  if ( !isset($_GET['lpage']) || ( isset($_GET['lpage']) && !is_numeric($_GET['lpage']) ) ) {
    $_GET['lpage'] = 1;
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<p align="right"><?php echo '<input type="button" value="' . BUTTON_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

<?php
  $Qlog = $osC_Database->query('select email_address, date_sent from :table_newsletters_log where newsletters_id = :newsletters_id order by date_sent desc');
  $Qlog->bindTable(':table_newsletters_log', TABLE_NEWSLETTERS_LOG);
  $Qlog->bindInt(':newsletters_id', $_GET['nID']);
  $Qlog->setBatchLimit($_GET['lpage'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qlog->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php echo $Qlog->getBatchTotalPages(TEXT_DISPLAY_NUMBER_OF_ENTRIES); ?></td>
    <td align="right"><?php echo $Qlog->getBatchPageLinks('lpage', $osC_Template->getModule() . '&page=' . $_GET['page'] . '&nID=' . $_GET['nID'], false); ?></td>
  </tr>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo TABLE_HEADING_EMAIL_ADDRESS; ?></th>
      <th><?php echo TABLE_HEADING_SENT; ?></th>
      <th><?php echo TABLE_HEADING_DATE_SENT; ?></th>
    </tr>
  </thead>
  <tbody>

<?php
  while ( $Qlog->next() ) {
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td><?php echo $Qlog->valueProtected('email_address'); ?></td>
      <td align="center"><?php echo osc_icon(!osc_empty($Qlog->value('date_sent')) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif', null, null); ?></td>
      <td align="right"><?php echo $Qlog->value('date_sent'); ?></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td align="right"><?php echo $Qlog->getBatchPagesPullDownMenu('lpage', $osC_Template->getModule() . '&page=' . $_GET['page'] . '&nID=' . $_GET['nID']); ?></td>
  </tr>
</table>

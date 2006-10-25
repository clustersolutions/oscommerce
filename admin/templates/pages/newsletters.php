<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div id="infoBox_nmDefault" <?php if (!empty($_GET['action'])) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_NEWSLETTERS; ?></th>
        <th><?php echo TABLE_HEADING_SIZE; ?></th>
        <th><?php echo TABLE_HEADING_MODULE; ?></th>
        <th><?php echo TABLE_HEADING_SENT; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>

<?php
  $Qnewsletters = $osC_Database->query('select newsletters_id, title, length(content) as content_length, module, date_added, date_sent, status, locked from :table_newsletters order by date_added desc');
  $Qnewsletters->bindTable(':table_newsletters', TABLE_NEWSLETTERS);
  $Qnewsletters->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qnewsletters->execute();

  while ($Qnewsletters->next()) {
    $newsletter_module_class = 'osC_Newsletter_' . $Qnewsletters->value('module');

    if (!class_exists($newsletter_module_class)) {
      $osC_Language->loadConstants('modules/newsletters/' . $Qnewsletters->value('module') . '.php');
      include('includes/modules/newsletters/' . $Qnewsletters->value('module') . '.php');

      $$newsletter_module_class = new $newsletter_module_class();
    }

    if (!isset($nmInfo) && (!isset($_GET['nmID']) || (isset($_GET['nmID']) && ($_GET['nmID'] == $Qnewsletters->valueInt('newsletters_id'))))) {
      $nmInfo = new objectInfo(array_merge($Qnewsletters->toArray(), array('module_title' => $$newsletter_module_class->getTitle())));
    }
?>

      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
        <td><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&nmID=' . $Qnewsletters->valueInt('newsletters_id') . '&action=nmPreview'), osc_icon('file.png', ICON_PREVIEW) . '&nbsp;' . $Qnewsletters->value('title')); ?></td>
        <td align="right"><?php echo number_format($Qnewsletters->valueInt('content_length')); ?></td>
        <td align="right"><?php echo $$newsletter_module_class->getTitle(); ?></td>
        <td align="center"><?php echo osc_icon(($Qnewsletters->valueInt('status') === 1) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif', null, null); ?></td>
        <td align="right">

<?php
    if ($Qnewsletters->valueInt('status') === 1) {
      echo osc_image('images/pixel_trans.gif', '', '16', '16') . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&nmID=' . $Qnewsletters->valueInt('newsletters_id') . '&action=nmLog'), osc_icon('log.png', IMAGE_LOG)) . '&nbsp;';
    } else {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&nmID=' . $Qnewsletters->valueInt('newsletters_id') . '&action=nmEdit'), osc_icon('edit.png', IMAGE_EDIT)) . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&nmID=' . $Qnewsletters->valueInt('newsletters_id') . '&action=nmSend'), osc_icon('email_send.png', IMAGE_SEND)) . '&nbsp;';
    }

    if (isset($nmInfo) && ($Qnewsletters->valueInt('newsletters_id') == $nmInfo->newsletters_id)) {
      echo osc_link_object('#', osc_icon('trash.png', IMAGE_DELETE), 'onclick="toggleInfoBox(\'nmDelete\');"');
    } else {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&nmID=' . $Qnewsletters->valueInt('newsletters_id') . '&action=nmDelete') , osc_icon('trash.png', IMAGE_DELETE));
    }
?>

        </td>
      </tr>

<?php
  }
?>

    </tbody>
  </table>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td class="smallText"><?php echo $Qnewsletters->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_NEWSLETTERS); ?></td>
      <td class="smallText" align="right"><?php echo $Qnewsletters->displayBatchLinksPullDown('page', $osC_Template->getModule()); ?></td>
    </tr>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . BUTTON_INSERT . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=nmEdit') . '\';" class="infoBoxButton">'; ?></p>
</div>

<?php
  if (isset($nmInfo)) {
?>

<div id="infoBox_nmDelete" <?php if ($_GET['action'] != 'nmDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $nmInfo->title; ?></div>
  <div class="infoBoxContent">
    <p><?php echo TEXT_INFO_DELETE_INTRO; ?></p>

    <p><?php echo '<b>' . $nmInfo->title . '</b>'; ?></p>

    <p align="center"><?php echo '<input type="button" value="' . BUTTON_DELETE . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&nmID=' . $nmInfo->newsletters_id . '&action=deleteconfirm') . '\';" class="operationButton">&nbsp;<input type="button" value="' . BUTTON_CANCEL . '" onclick="toggleInfoBox(\'nmDefault\');" class="operationButton">'; ?></p>
  </div>
</div>

<?php
  }
?>

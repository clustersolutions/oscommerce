<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  $Qnewsletter = $osC_Database->query('select newsletters_id, title, content, module from :table_newsletters where newsletters_id = :newsletters_id');
  $Qnewsletter->bindTable(':table_newsletters', TABLE_NEWSLETTERS);
  $Qnewsletter->bindInt(':newsletters_id', $_GET['nmID']);
  $Qnewsletter->execute();

  $osC_Language->loadConstants('modules/newsletters/' . $Qnewsletter->value('module') . '.php');
  include('includes/modules/newsletters/' . $Qnewsletter->value('module') . '.php');

  $module_name = 'osC_Newsletter_' . $Qnewsletter->value('module');
  $module = new $module_name($Qnewsletter->value('title'), $Qnewsletter->value('content'), $Qnewsletter->valueInt('newsletters_id'));
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<?php
  if ($action == 'nmSend') {
    if ($module->hasAudienceSelection()) {
      echo $module->showAudienceSelectionForm();
    } else {
      echo $module->showConfirmation();
    }
  } elseif ($action == 'nmConfirm') {
    echo $module->showConfirmation();
  } elseif ($action == 'nmSendConfirm') {
?>

<p><?php echo osc_image('images/ani_send_email.gif', IMAGE_ANI_SEND_EMAIL); ?></p>
<p><?php echo '<b>' . TEXT_PLEASE_WAIT . '</b>'; ?></p>

<?php
    flush();
    $module->sendEmail();
?>

<p><font color="#ff0000"><b><?php echo TEXT_FINISHED_SENDING_NEWSLETTERS; ?></b></font></p>

<p align="right"><?php echo '<input type="button" value="' . BUTTON_OK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nmID=' . $_GET['nmID']) . '\';" class="operationButton">'; ?></p>

<?php
  }
?>

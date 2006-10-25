<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

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

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<?php
  if ($_GET['action'] == 'nmSend') {
    if ($module->hasAudienceSelection()) {
      echo $module->showAudienceSelectionForm();
    } else {
      echo $module->showConfirmation();
    }
  } elseif ($_GET['action'] == 'nmConfirm') {
    echo $module->showConfirmation();
  } elseif ($_GET['action'] == 'nmSendConfirm') {
?>

<p><?php echo osc_image('images/ani_send_email.gif', IMAGE_ANI_SEND_EMAIL); ?></p>
<p><?php echo '<b>' . TEXT_PLEASE_WAIT . '</b>'; ?></p>

<?php
    flush();
    $module->sendEmail();
?>

<p><font color="#ff0000"><b><?php echo TEXT_FINISHED_SENDING_NEWSLETTERS; ?></b></font></p>

<p align="right"><?php echo '<input type="button" value="' . BUTTON_OK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&nmID=' . $_GET['nmID']) . '\';" class="operationButton">'; ?></p>

<?php
  }
?>

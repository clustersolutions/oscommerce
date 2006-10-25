<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $Qemail = $osC_Database->query('select title, content from :table_newsletters where newsletters_id = :newsletters_id');
  $Qemail->bindTable(':table_newsletters', TABLE_NEWSLETTERS);
  $Qemail->bindInt(':newsletters_id', $_GET['nmID']);
  $Qemail->execute();
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<p><?php echo '<b>' . $Qemail->value('title') . '</b>'; ?></p>

<p><?php echo nl2br($Qemail->valueProtected('content')); ?></p>

<p align="right"><?php echo '<input type="button" value="' . BUTTON_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&nmID=' . $_GET['nmID']) . '\';" class="operationButton">'; ?></p>

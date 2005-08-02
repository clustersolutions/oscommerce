<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  $Qemail = $osC_Database->query('select title, content from :table_newsletters where newsletters_id = :newsletters_id');
  $Qemail->bindTable(':table_newsletters', TABLE_NEWSLETTERS);
  $Qemail->bindInt(':newsletters_id', $_GET['nmID']);
  $Qemail->execute();
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<p><?php echo '<b>' . $Qemail->value('title') . '</b>'; ?></p>

<p><?php echo nl2br($Qemail->valueProtected('content')); ?></p>

<p align="right"><?php echo '<input type="button" value="' . BUTTON_BACK . '" onClick="document.location.href=\'' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nmID=' . $_GET['nmID']) . '\';" class="operationButton">'; ?></p>

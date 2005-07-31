<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if ($osC_Customer->isLoggedOn() == false) {
    if (empty($_GET) || (!empty($_GET) && !in_array(tep_sanitize_string(basename(key(array_slice($_GET, 0, 1)))), array('login', 'create')))) {
      $navigation->set_snapshot();

      tep_redirect(tep_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
    }
  }

  require(DIR_WS_LANGUAGES . $osC_Session->value('language') . '/' . FILENAME_ACCOUNT);

  $osC_Template->setPageTitle(HEADING_TITLE_ACCOUNT);
  $osC_Template->setPageContentsFilename('account.php');

  if ($osC_Services->isStarted('breadcrumb')) {
    $breadcrumb->add(NAVBAR_TITLE_MY_ACCOUNT, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
  }

  if (empty($_GET) === false) {
    $_account_module = tep_sanitize_string(basename(key(array_slice($_GET, 0, 1))));

    if (file_exists('includes/modules/account/' . $_account_module . '.php')) {
      include('includes/modules/account/' . $_account_module . '.php');

      $_account_module_name = 'osC_Account_' . ucfirst($_account_module);
      $osC_Account_Module = new $_account_module_name();

      $osC_Template->setPageTitle($osC_Account_Module->getPageTitle());
      $osC_Template->setPageContentsFilename($osC_Account_Module->getPageContentsFilename());

      unset($osC_Account_Module);
    }
  }

  require('templates/' . $osC_Template->getTemplate() . '.php');

  require('includes/application_bottom.php');
?>

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
    $navigation->set_snapshot();

    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  require(DIR_WS_LANGUAGES . $osC_Session->value('language') . '/' . FILENAME_ACCOUNT);

  if ($osC_Services->isStarted('breadcrumb')) {
    $breadcrumb->add(NAVBAR_TITLE_MY_ACCOUNT, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
  }

  $page_contents = 'account.php';

  if (empty($_GET) === false) {
    $_account_module = tep_sanitize_string(basename(key(array_slice($_GET, 0, 1))));

    if (file_exists('includes/modules/account/' . $_account_module . '.php')) {
      include('includes/modules/account/' . $_account_module . '.php');

      $_account_module_name = 'osC_Account_' . ucfirst($_account_module);
      $osC_Account_Module = new $_account_module_name();

      $page_contents = $osC_Account_Module->getPageContentsFile();
    }
  }

  require('templates/default.php');

  require('includes/application_bottom.php');
?>

<?php
/*
  $Id: languages.php,v 1.39 2004/11/29 14:33:42 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $selected_box = 'localization';

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
    $_GET['page'] = 1;
  }

  if (!empty($action)) {
    switch ($action) {
      case 'save':
        $default = (isset($_POST['default']) && ($_POST['default'] == 'on')) ? true : false;

        if (isset($_GET['lID']) && is_numeric($_GET['lID'])) {
          $result = $osC_Language->update($_GET['lID'], $_POST, $default);
        } else {
          $result = $osC_Language->insert($_POST, $default);
        }

        if ($result === true) {
          $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        tep_redirect(tep_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page']));
        break;
      case 'deleteconfirm':
        if (isset($_GET['lID']) && is_numeric($_GET['lID'])) {
          if ($osC_Language->remove($_GET['lID'])) {
            $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
          } else {
            $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
          }
        }

        tep_redirect(tep_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page']));
        break;
    }
  }

  $page_contents = 'languages.php';

  require('templates/default.php');

  require('includes/application_bottom.php');
?>

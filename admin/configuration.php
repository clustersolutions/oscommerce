<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (!isset($_GET['gID']) || (isset($_GET['gID']) && !is_numeric($_GET['gID']))) {
    $_GET['gID'] = 1;
  }

  if (!empty($action)) {
    switch ($action) {
      case 'save':
        if (isset($_GET['cID']) && is_numeric($_GET['cID'])) {
          $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value, last_modified = now() where configuration_id = :configuration_id');
          $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
          $Qupdate->bindValue(':configuration_value', $_POST['configuration_value']);
          $Qupdate->bindInt(':configuration_id', $_GET['cID']);
          $Qupdate->execute();

          if ($Qupdate->affectedRows()) {
            osC_Cache::clear('configuration');

            $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
          } else {
            $osC_MessageStack->add_session('header', WARNING_DB_ROWS_NOT_UPDATED, 'warning');
          }
        }

        osc_redirect(osc_href_link_admin(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $_GET['cID']));
        break;
    }
  }

  $page_contents = 'configuration.php';

  require('templates/default.php');

  require('includes/application_bottom.php');
?>

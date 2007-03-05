<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Configuration extends osC_Template {

/* Private variables */

    var $_module = 'configuration',
        $_page_title,
        $_page_contents = 'main.php';

/* Class constructor */

    function osC_Content_Configuration() {
      global $osC_Database;

      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if (!isset($_GET['gID']) || (isset($_GET['gID']) && !is_numeric($_GET['gID']))) {
        $_GET['gID'] = 1;
      }

      $Qcg = $osC_Database->query('select configuration_group_title from :table_configuration_group where configuration_group_id = :configuration_group_id');
      $Qcg->bindTable(':table_configuration_group', TABLE_CONFIGURATION_GROUP);
      $Qcg->bindInt(':configuration_group_id', $_GET['gID']);
      $Qcg->execute();

      $this->_page_title = $Qcg->value('configuration_group_title');

      $Qcg->freeResult();

      if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
          case 'save':
            $this->_process();
            break;
        }
      }
    }

/* Private methods */

    function _process() {
      global $osC_Database, $osC_MessageStack;

      if (isset($_GET['cID']) && is_numeric($_GET['cID'])) {
        $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value, last_modified = now() where configuration_id = :configuration_id');
        $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
        $Qupdate->bindValue(':configuration_value', $_POST['configuration_value']);
        $Qupdate->bindInt(':configuration_id', $_GET['cID']);
        $Qupdate->execute();

        if ($Qupdate->affectedRows()) {
          osC_Cache::clear('configuration');

          $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_MessageStack->add_session($this->_module, WARNING_DB_ROWS_NOT_UPDATED, 'warning');
        }
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&gID=' . $_GET['gID'] . '&cID=' . $_GET['cID']));
    }
  }
?>

<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_debug {
    var $title = 'Debug',
        $description = 'Display collected debug information.',
        $uninstallable = true,
        $depends = 'language',
        $preceeds;

    function start() {
      if (PHP_VERSION < 4.1) {
        global $_SERVER;
      }

      global $messageStack, $osC_Session;

      if (SERVICE_DEBUG_SHOW_DEVELOPMENT_WARNING == 'True') {
        $messageStack->add('debug', 'This is a development version of osCommerce - please use it for testing purposes only! [' . PROJECT_VERSION . ']');
      }

      if (SERVICE_DEBUG_CHECK_LOCALE == 'True') {
        $setlocale = setlocale(LC_TIME, LANGUAGE_LOCALE);

        if (($setlocale === false) || ($setlocale === null)) {
          $messageStack->add('debug', 'Error: Locale does not exist: ' . LANGUAGE_LOCALE . ' [' . __CLASS__ . ']', 'error');
        }
      }

      if ((SERVICE_DEBUG_CHECK_INSTALLATION_MODULE == 'True') && file_exists(dirname($_SERVER['SCRIPT_FILENAME']) . '/install')) {
        $messageStack->add('debug', WARNING_INSTALL_DIRECTORY_EXISTS . ' [' . __CLASS__ . ']', 'warning');
      }

      if ((SERVICE_DEBUG_CHECK_CONFIGURATION == 'True') && file_exists(dirname($_SERVER['SCRIPT_FILENAME']) . '/includes/configure.php') && is_writeable(dirname($_SERVER['SCRIPT_FILENAME']) . '/includes/configure.php')) {
        $messageStack->add('debug', WARNING_CONFIG_FILE_WRITEABLE . ' [' . __CLASS__ . ']', 'warning');
      }

      if ((SERVICE_DEBUG_CHECK_SESSION_DIRECTORY == 'True') && (STORE_SESSIONS == '')) {
        if (!is_dir($osC_Session->save_path)) {
          $messageStack->add('debug', WARNING_SESSION_DIRECTORY_NON_EXISTENT . ' [' . __CLASS__ . ']', 'warning');
        } elseif (!is_writeable($osC_Session->save_path)) {
          $messageStack->add('debug', WARNING_SESSION_DIRECTORY_NOT_WRITEABLE . ' [' . __CLASS__ . ']', 'warning');
        }
      }

      if ((SERVICE_DEBUG_CHECK_SESSION_AUTOSTART == 'True') && (bool)ini_get('session.auto_start')) {
        $messageStack->add('debug', WARNING_SESSION_AUTO_START . ' [' . __CLASS__ . ']', 'warning');
      }

      if ((SERVICE_DEBUG_CHECK_DOWNLOAD_DIRECTORY == 'True') && (DOWNLOAD_ENABLED == 'true')) {
        if (!is_dir(DIR_FS_DOWNLOAD)) {
          $messageStack->add('debug', WARNING_DOWNLOAD_DIRECTORY_NON_EXISTENT . ' [' . __CLASS__ . ']', 'warning');
        }
      }

      return true;
    }

    function stop() {
      if (PHP_VERSION < 4.1) {
        global $_SERVER;
      }

      global $messageStack;

      $time_start = explode(' ', PAGE_PARSE_START_TIME);
      $time_end = explode(' ', microtime());
      $parse_time = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);

      if (tep_not_null(SERVICE_DEBUG_EXECUTION_TIME_LOG)) {
        if (!@error_log(strftime('%c') . ' - ' . $_SERVER['REQUEST_URI'] . ' (' . $parse_time . 's)' . "\n", 3, SERVICE_DEBUG_EXECUTION_TIME_LOG)) {
          if (!file_exists(SERVICE_DEBUG_EXECUTION_TIME_LOG) || !is_writable(SERVICE_DEBUG_EXECUTION_TIME_LOG)) {
            $messageStack->add('debug', 'Error: Execution time log file not writeable: ' . SERVICE_DEBUG_EXECUTION_TIME_LOG . ' [' . __CLASS__ . ']', 'error');
          }
        }
      }

      if (SERVICE_DEBUG_EXECUTION_DISPLAY == 'True') {
        $messageStack->add('debug', 'Execution Time: ' . $parse_time . 's [' . __CLASS__ . ']', 'warning');
      }

      if ($messageStack->size('debug') > 0) {
        echo $messageStack->output('debug');
      }

      return true;
    }

    function install() {
      global $osC_Database;

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Page Execution Time Log File', 'SERVICE_DEBUG_EXECUTION_TIME_LOG', '', 'Location of the page execution time log file (eg, /www/log/page_parse.log).', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Show The Page Execution Time', 'SERVICE_DEBUG_EXECUTION_DISPLAY', 'True', 'Show the page execution time.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Log Database Queries', 'SERVICE_DEBUG_LOG_DB_QUERIES', 'False', 'Log all database queries in the page execution time log file.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Show Database Queries', 'SERVICE_DEBUG_OUTPUT_DB_QUERIES', 'False', 'Show all database queries made.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Show Development Version Warning', 'SERVICE_DEBUG_SHOW_DEVELOPMENT_WARNING', 'True', 'Show an osCommerce development version warning message.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Check Language Locale', 'SERVICE_DEBUG_CHECK_LOCALE', 'True', 'Show a warning message if the set language locale does not exist on the server.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Check Installation Module', 'SERVICE_DEBUG_CHECK_INSTALLATION_MODULE', 'True', 'Show a warning message if the installation module exists.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Check Configuration File', 'SERVICE_DEBUG_CHECK_CONFIGURATION', 'True', 'Show a warning if the configuration file is writeable.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Check Sessions Directory', 'SERVICE_DEBUG_CHECK_SESSION_DIRECTORY', 'True', 'Show a warning if the file-based session directory does not exist.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Check Sessions Auto Start', 'SERVICE_DEBUG_CHECK_SESSION_AUTOSTART', 'True', 'Show a warning if PHP is configured to automatically start sessions.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Check Download Directory', 'SERVICE_DEBUG_CHECK_DOWNLOAD_DIRECTORY', 'True', 'Show a warning if the digital product download directory does not exist.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
    }

    function remove() {
      global $osC_Database;

      $osC_Database->simpleQuery("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('SERVICE_DEBUG_OUTPUT_DB_QUERIES', 'SERVICE_DEBUG_LOG_DB_QUERIES', 'SERVICE_DEBUG_EXECUTION_TIME_LOG', 'SERVICE_DEBUG_EXECUTION_DISPLAY', 'SERVICE_DEBUG_SHOW_DEVELOPMENT_WARNING', 'SERVICE_DEBUG_CHECK_LOCALE', 'SERVICE_DEBUG_CHECK_INSTALLATION_MODULE', 'SERVICE_DEBUG_CHECK_CONFIGURATION', 'SERVICE_DEBUG_CHECK_SESSION_DIRECTORY', 'SERVICE_DEBUG_CHECK_SESSION_AUTOSTART', 'SERVICE_DEBUG_CHECK_DOWNLOAD_DIRECTORY');
    }
  }
?>

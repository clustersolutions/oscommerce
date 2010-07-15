<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Module\Service;

  use osCommerce\OM\Registry;
  use osCommerce\OM\OSCOM;

  class Debug implements \osCommerce\OM\Site\Shop\ServiceInterface {
    public static function start() {
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_MessageStack = Registry::get('MessageStack');

      if ( SERVICE_DEBUG_CHECK_LOCALE == '1' ) {
        $setlocale = osc_setlocale(LC_TIME, explode(',', $OSCOM_Language->getLocale()));

        if ( ($setlocale === false) || ($setlocale === null) ) {
          $OSCOM_MessageStack->add('debug', 'Error: Locale does not exist: ' . $OSCOM_Language->getLocale(), 'error');
        }
      }

      if ( (SERVICE_DEBUG_CHECK_INSTALLATION_MODULE == '1') && file_exists(OSCOM::BASE_DIRECTORY . 'sites/Setup') ) {
        $OSCOM_MessageStack->add('debug', sprintf(OSCOM::getDef('warning_install_directory_exists'), OSCOM::BASE_DIRECTORY . 'sites/Setup'), 'warning');
      }

      if ( (SERVICE_DEBUG_CHECK_CONFIGURATION == '1') && is_writeable(OSCOM::BASE_DIRECTORY . 'config.php') ) {
        $OSCOM_MessageStack->add('debug', sprintf(OSCOM::getDef('warning_config_file_writeable'), OSCOM::BASE_DIRECTORY . 'config.php'), 'warning');
      }

      if ((SERVICE_DEBUG_CHECK_SESSION_DIRECTORY == '1') && (STORE_SESSIONS == '')) {
        if (!is_dir(OSCOM_Registry::get('Session')->getSavePath())) {
          $osC_MessageStack->add('debug', sprintf($osC_Language->get('warning_session_directory_non_existent'), OSCOM_Registry::get('Session')->getSavePath()) . ' [' . __CLASS__ . ']', 'warning');
        } elseif (!is_writeable(OSCOM_Registry::get('Session')->getSavePath())) {
          $osC_MessageStack->add('debug', sprintf($osC_Language->get('warning_session_directory_not_writeable'), OSCOM_Registry::get('Session')->getSavePath()) . ' [' . __CLASS__ . ']', 'warning');
        }
      }

      if ( (SERVICE_DEBUG_CHECK_SESSION_AUTOSTART == '1') && (bool)ini_get('session.auto_start') ) {
        $OSCOM_MessageStack->add('debug', OSCOM::getDef('warning_session_auto_start'), 'warning');
      }

      if ( (SERVICE_DEBUG_CHECK_DOWNLOAD_DIRECTORY == '1') && (DOWNLOAD_ENABLED == '1') ) {
        if ( !is_dir(DIR_FS_DOWNLOAD) ) {
          $OSCOM_MessageStack->add('debug', sprintf(OSCOM::getDef('warning_download_directory_non_existent'), DIR_FS_DOWNLOAD), 'warning');
        }
      }

      return true;
    }

    public static function stop() {
      $OSCOM_MessageStack = Registry::get('MessageStack');
      $OSCOM_Template = Registry::get('Template');

      $time_start = explode(' ', PAGE_PARSE_START_TIME);
      $time_end = explode(' ', microtime());
      $parse_time = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);

      if ( !osc_empty(SERVICE_DEBUG_EXECUTION_TIME_LOG) ) {
        if ( !@error_log(strftime('%c') . ' - ' . $_SERVER['REQUEST_URI'] . ' (' . $parse_time . 's)' . "\n", 3, SERVICE_DEBUG_EXECUTION_TIME_LOG)) {
          if ( !file_exists(SERVICE_DEBUG_EXECUTION_TIME_LOG) || !is_writable(SERVICE_DEBUG_EXECUTION_TIME_LOG) ) {
            $OSCOM_MessageStack->add('debug', 'Error: Execution time log file not writeable: ' . SERVICE_DEBUG_EXECUTION_TIME_LOG, 'error');
          }
        }
      }

      if ( SERVICE_DEBUG_EXECUTION_DISPLAY == '1' ) {
        $OSCOM_MessageStack->add('debug', 'Execution Time: ' . $parse_time . 's', 'warning');
      }

      if ( $OSCOM_Template->showDebugMessages() && $OSCOM_MessageStack->exists('debug') ) {
        echo $OSCOM_MessageStack->get('debug');
      }

      return true;
    }
  }
?>

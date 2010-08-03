<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\PaymentModules;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\DirectoryListing;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Cache;

  class PaymentModules {
    public static function get($code) {
      $OSCOM_Language = Registry::get('Language');

      $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Module\\Payment\\' . $code;

      $OSCOM_Language->injectDefinitions('modules/payment/' . $code . '.xml');

      $OSCOM_PM = new $class();

      $result = array('code' => $OSCOM_PM->getCode(),
                      'title' => $OSCOM_PM->getTitle(),
                      'sort_order' => $OSCOM_PM->getSortOrder(),
                      'status' => $OSCOM_PM->isEnabled(),
                      'keys' => $OSCOM_PM->getKeys());

      return $result;
    }

    public static function getInstalled() {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Language = Registry::get('Language');

      $result = array('entries' => array());

      $Qpm = $OSCOM_Database->query('select code from :table_templates_boxes where modules_group = :modules_group order by code');
      $Qpm->bindValue(':modules_group', 'Payment');
      $Qpm->execute();

      while ( $Qpm->next() ) {
        $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Module\\Payment\\' . $Qpm->value('code');

        if ( class_exists($class) ) {
          $OSCOM_Language->injectDefinitions('modules/payment/' . $Qpm->value('code') . '.xml');

          $OSCOM_PM = new $class();

          $result['entries'][] = array('code' => $OSCOM_PM->getCode(),
                                       'title' => $OSCOM_PM->getTitle(),
                                       'sort_order' => $OSCOM_PM->getSortOrder(),
                                       'status' => $OSCOM_PM->isInstalled() && $OSCOM_PM->isEnabled());
        }
      }

      $result['total'] = count($result['entries']);

      return $result;
    }

    public static function findInstalled($search) {
      $modules = self::getInstalled();

      $result = array('entries' => array());

      foreach ( $modules['entries'] as $module ) {
        if ( (stripos($module['code'], $search) !== false) || (stripos($module['title'], $search) !== false) ) {
          $result['entries'][] = $module;
        }
      }

      $result['total'] = count($result['entries']);

      return $result;
    }

    public static function getUninstalled() {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Language = Registry::get('Language');

      $installed = array();

      $Qpm = $OSCOM_Database->query('select code from :table_templates_boxes where modules_group = :modules_group');
      $Qpm->bindValue(':modules_group', 'Payment');
      $Qpm->execute();

      while ( $Qpm->next() ) {
        $installed[] = $Qpm->value('code');
      }

      $result = array('entries' => array());

      $DLpm = new DirectoryListing(OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/Module/Payment');
      $DLpm->setIncludeDirectories(false);

      foreach ( $DLpm->getFiles() as $file ) {
        $module = substr($file['name'], 0, strrpos($file['name'], '.'));

        if ( !in_array($module, $installed) ) {
          $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Module\\Payment\\' . $module;

          if ( class_exists($class) ) {
            $OSCOM_Language->injectDefinitions('modules/payment/' . $module . '.xml');

            $OSCOM_PM = new $class();

            $result['entries'][] = array('code' => $OSCOM_PM->getCode(),
                                         'title' => $OSCOM_PM->getTitle(),
                                         'sort_order' => $OSCOM_PM->getSortOrder(),
                                         'status' => $OSCOM_PM->isEnabled());
          }
        }
      }

      $result['total'] = count($result['entries']);

      return $result;
    }

    public static function findUninstalled($search) {
      $modules = self::getUninstalled();

      $result = array('entries' => array());

      foreach ( $modules['entries'] as $module ) {
        if ( (stripos($module['code'], $search) !== false) || (stripos($module['title'], $search) !== false) ) {
          $result['entries'][] = $module;
        }
      }

      $result['total'] = count($result['entries']);

      return $result;
    }

    public static function save($data) {
      $OSCOM_Database = Registry::get('Database');

      $error = false;

      $OSCOM_Database->startTransaction();

      foreach ( $data['configuration'] as $key => $value ) {
        $Qupdate = $OSCOM_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
        $Qupdate->bindValue(':configuration_value', is_array($data['configuration'][$key]) ? implode(',', $data['configuration'][$key]) : $value);
        $Qupdate->bindValue(':configuration_key', $key);
// HPDL        $Qupdate->setLogging($_SESSION['module']);
        $Qupdate->execute();

        if ( $OSCOM_Database->isError() ) {
          $error = true;
          break;
        }
      }

      if ( $error === false ) {
        $OSCOM_Database->commitTransaction();

        Cache::clear('configuration');

        return true;
      }

      $OSCOM_Database->rollbackTransaction();

      return false;
    }

    public static function install($module) {
      $OSCOM_Language = Registry::get('Language');

      $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Module\\Payment\\' . $module;

      if ( class_exists($class) ) {
        $OSCOM_Language->injectDefinitions('modules/payment/' . $module . '.xml');

        $OSCOM_PM = new $class();
        $OSCOM_PM->install();

        Cache::clear('modules-payment');
        Cache::clear('configuration');

        return true;
      }

      return false;
    }

    public static function uninstall($module) {
      $OSCOM_Language = Registry::get('Language');

      $class = 'osCommerce\\OM\\Core\\Site\\Admin\\Module\\Payment\\' . $module;

      if ( class_exists($class) ) {
        $OSCOM_Language->injectDefinitions('modules/payment/' . $module . '.xml');

        $OSCOM_PM = new $class();
        $OSCOM_PM->remove();

        Cache::clear('modules-payment');
        Cache::clear('configuration');

        return true;
      }

      return false;
    }
  }
?>

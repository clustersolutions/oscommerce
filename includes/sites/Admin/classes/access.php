<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

/**
 * The osC_Access manages the permission levels of administrators who have access to the Administration Tool
 */

  class osC_Access {

/**
 * Holds the group code for the current access module
 *
 * @var string
 * @access protected
 */

    protected $_group = 'misc';

/**
 * Holds the icon for the current access module
 *
 * @var string
 * @access protected
 */

    protected $_icon = 'default.png';

/**
 * Holds the title of the current access module
 *
 * @var string
 * @access protected
 */

    protected $_title;

/**
 * Holds the sort ordering number for the current access module
 *
 * @var int
 * @access protected
 */

    protected $_sort_order = 0;

/**
 * Return the Administration Tool Application modules the administrator has access to
 *
 * @param int $id The ID of the administrator
 * @access public
 * @return array
 */

    public static function getUserLevels($id, $site = null) {
      $OSCOM_Database = OSCOM_Registry::get('Database');

      if ( empty($site) ) {
        $site = OSCOM::getSite();
      }

      $modules = array();

      $Qaccess = $OSCOM_Database->query('select module from :table_administrators_access where administrators_id = :administrators_id');
      $Qaccess->bindInt(':administrators_id', $id);
      $Qaccess->execute();

      while ( $Qaccess->next() ) {
        $modules[] = $Qaccess->value('module');
      }

      if ( in_array('*', $modules) ) {
        $modules = array();

        $DLapps = new OSCOM_DirectoryListing(OSCOM::BASE_DIRECTORY . 'sites/' . $site . '/applications');
        $DLapps->setIncludeFiles(false);

        foreach ( $DLapps->getFiles() as $file ) {
          if ( preg_match('/[A-Z]/', substr($file['name'], 0, 1)) && !in_array($file['name'], call_user_func(array('OSCOM_' . $site, 'getGuestApplications'))) && file_exists($DLapps->getDirectory() . '/' . $file['name'] . '/' . $file['name'] . '.php') ) { // HPDL remove preg_match
            $modules[] = $file['name'];
          }
        }

        $DLapps = new OSCOM_DirectoryListing(OSCOM::BASE_DIRECTORY . 'sites/' . $site . '/modules/Access'); // HPDL to remove
        $DLapps->setIncludeDirectories(false);

        foreach ( $DLapps->getFiles() as $file ) {
          if ( preg_match('/[^A-Z]/', substr($file['name'], 0, 1)) ) {
            $modules[] = substr($file['name'], 0, strrpos($file['name'], '.'));
          }
        }
      }

      $shortcuts = array();

      $Qshortcuts = $OSCOM_Database->query('select module from :table_administrator_shortcuts where administrators_id = :administrators_id');
      $Qshortcuts->bindInt(':administrators_id', $id);
      $Qshortcuts->execute();

      while ( $Qshortcuts->next() ) {
        $shortcuts[] = $Qshortcuts->value('module');
      }

      $levels = array();

      foreach ( $modules as $module ) {
        if ( preg_match('/[A-Z]/', substr($module, 0, 1)) ) {
          $application_class = 'OSCOM_Site_' . $site . '_Application_' . $module;

          if ( class_exists($application_class) ) {
            if ( OSCOM_Registry::exists('Application') && ($module == OSCOM::getSiteApplication()) ) {
              $OSCOM_Application = OSCOM_Registry::get('Application');
            } else {
              OSCOM_Registry::get('Language')->loadIniFile($module . '.php');
              $OSCOM_Application = new $application_class(false);
            }

            $levels[$module] = array('module' => $module,
                                     'icon' => $OSCOM_Application->getIcon(),
                                     'title' => $OSCOM_Application->getTitle(),
                                     'group' => $OSCOM_Application->getGroup(),
                                     'linkable' => $OSCOM_Application->canLinkTo(),
                                     'shortcut' => in_array($module, $shortcuts),
                                     'sort_order' => $OSCOM_Application->getSortOrder());
          }
        } elseif ( file_exists(OSCOM::BASE_DIRECTORY . 'sites/' . $site . '/modules/Access/' . $module . '.php') ) { // HPDL to remove
          $module_class = 'osC_Access_' . ucfirst($module);

          if ( !class_exists( $module_class ) ) {
            OSCOM_Registry::get('Language')->loadIniFile('modules/access/' . $module . '.php');
            include(OSCOM::BASE_DIRECTORY . 'sites/' . $site . '/modules/Access/' . $module . '.php');
          }

          $module_class = new $module_class();

          $levels[$module] = array('module' => $module,
                                   'icon' => $module_class->getIcon(),
                                   'title' => $module_class->getTitle(),
                                   'group' => $module_class->getGroup(),
                                   'linkable' => true,
                                   'shortcut' => in_array($module, $shortcuts),
                                   'sort_order' => $module_class->getSortOrder());
        }
      }

      return $levels;
    }

    public static function getShortcuts($site = null) {
      if ( empty($site) ) {
        $site = OSCOM::getSite();
      }

      $shortcuts = array();

      if ( isset($_SESSION[$site]['id']) ) {
        foreach ( $_SESSION[$site]['access'] as $module => $data ) {
          if ( $data['shortcut'] === true ) {
            $shortcuts[$module] = $data;
          }
        }

        ksort($shortcuts);
      }

      return $shortcuts;
    }

    public static function hasShortcut($site = null) {
      if ( empty($site) ) {
        $site = OSCOM::getSite();
      }

      if ( isset($_SESSION[$site]['id']) ) {
        foreach ( $_SESSION[$site]['access'] as $module => $data ) {
          if ( $data['shortcut'] === true ) {
            return true;
          }
        }
      }

      return false;
    }

    public static function isShortcut($application, $site = null) {
      if ( empty($site) ) {
        $site = OSCOM::getSite();
      }

      if ( isset($_SESSION[$site]['id']) ) {
        return $_SESSION[$site]['access'][$application]['shortcut'];
      }

      return false;
    }

    public static function getLevels($group = null) {
      $access = array();

      if ( isset($_SESSION['Admin']['id']) && isset($_SESSION['Admin']['access']) ) {
        foreach ( $_SESSION['Admin']['access'] as $module => $data ) {
          if ( ($data['linkable'] === true) && (empty($group) || ($group == $data['group'])) ) {
            if ( !isset($access[$data['group']][$data['sort_order']]) ) {
              $access[$data['group']][$data['sort_order']] = $data;
            } else {
              $access[$data['group']][] = $data;
            }
          }
        }

        ksort($access);

        foreach ( $access as $group => $modules ) {
          ksort($access[$group]);
        }
      }

      return $access;
    }

    function getModule() {
      return $this->_module;
    }

    public static function getGroup($module = null) {
      if ( empty($module) && isset($this) ) { // HPDL to remove
        return $this->_group;
      }

      foreach ( osC_Access::getLevels() as $group => $links ) {
        foreach ( $links as $link ) {
          if ( $link['module'] == $module ) {
            return $group;
          }
        }
      }

      return false;
    }

    public static function getGroupTitle($group) {
      $OSCOM_Language = OSCOM_Registry::get('Language');

      if ( !$OSCOM_Language->isDefined('access_group_' . $group . '_title') ) {
        $OSCOM_Language->loadIniFile( 'modules/access/groups/' . $group . '.php' );
      }

      return $OSCOM_Language->get('access_group_' . $group . '_title');
    }

    function getIcon() {
      return $this->_icon;
    }

    function getTitle() {
      return $this->_title;
    }

    function getSortOrder() {
      return $this->_sort_order;
    }

    public static function hasAccess($site, $application) {
      return in_array($application, call_user_func(array('OSCOM_' . $site, 'getGuestApplications'))) || isset($_SESSION[$site]['access'][$application]);
    }
  }
?>

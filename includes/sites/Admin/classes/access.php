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

    protected $_icon = 'configure.png';

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
 * Holds the sub level children entries for the current access module
 *
 * @var array
 * @access protected
 */

    protected $_subgroups = array();

/**
 * Return the Administration Tool Application modules the administrator has access to
 *
 * @param int $id The ID of the administrator
 * @access public
 * @return array
 */

    public static function getUserLevels($site, $id) {
      $OSCOM_Database = OSCOM_Registry::get('Database');

      $modules = array();

      $Qaccess = $OSCOM_Database->query('select module from :table_administrators_access where administrators_id = :administrators_id');
      $Qaccess->bindInt(':administrators_id', $id);
      $Qaccess->execute();

      while ( $Qaccess->next() ) {
        $modules[] = $Qaccess->value('module');
      }

      if ( in_array('*', $modules) ) {
        $modules = array();

        $DLapps = new OSCOM_DirectoryListing(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSite() . '/applications');
        $DLapps->setIncludeFiles(false);

        foreach ( $DLapps->getFiles() as $file ) {
          if ( preg_match('/[A-Z]/', substr($file['name'], 0, 1)) && !in_array($file['name'], call_user_func(array('OSCOM_' . OSCOM::getSite(), 'getGuestApplications'))) && file_exists($DLapps->getDirectory() . '/' . $file['name'] . '/' . $file['name'] . '.php') ) { // HPDL remove preg_match
            $modules[] = $file['name'];
          }
        }

        $DLapps = new OSCOM_DirectoryListing(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSite() . '/modules/Access'); // HPDL to remove
        $DLapps->setIncludeDirectories(false);

        foreach ( $DLapps->getFiles() as $file ) {
          if ( preg_match('/[^A-Z]/', substr($file['name'], 0, 1)) ) {
            $modules[] = substr($file['name'], 0, strrpos($file['name'], '.'));
          }
        }
      }

      return $modules;
    }

    public static function getLevels($group = null) {
      global $osC_Language;

      $access = array();

      if ( isset($_SESSION['Admin']['id']) ) {
        foreach ( $_SESSION['Admin']['access'] as $module ) {
          if ( preg_match('/[A-Z]/', substr($module, 0, 1)) ) {
            $application_class = 'OSCOM_Site_' . OSCOM::getSite() . '_Application_' . $module;

            if ( class_exists($application_class) ) {
              if ( $module == OSCOM::getSiteApplication() ) {
                $OSCOM_Application = OSCOM_Registry::get('Application');
              } else {
                OSCOM_Registry::get('Language')->loadIniFile($module . '.php');
                $OSCOM_Application = new $application_class(false);
              }

              if ( $OSCOM_Application->canLinkTo() && (empty($group) || ($group == $OSCOM_Application->getGroup())) ) {
                $data = array('module' => $module,
                              'icon' => $OSCOM_Application->getIcon(),
                              'title' => $OSCOM_Application->getTitle(),
                              'subgroups' => $OSCOM_Application->getSubGroups());

                if ( !isset($access[$OSCOM_Application->getGroup()][$OSCOM_Application->getSortOrder()]) ) {
                  $access[$OSCOM_Application->getGroup()][$OSCOM_Application->getSortOrder()] = $data;
                } else {
                  $access[$OSCOM_Application->getGroup()][] = $data;
                }
              }
            }
          } elseif ( file_exists(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSite() . '/modules/Access/' . $module . '.php') ) { // HPDL to remove
            $module_class = 'osC_Access_' . ucfirst($module);

            if ( !class_exists( $module_class ) ) {
              $osC_Language->loadIniFile('modules/access/' . $module . '.php');
              include(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSite() . '/modules/Access/' . $module . '.php');
            }

            $module_class = new $module_class();

            if ( empty($group) || ( $group == $module_class->getGroup() ) ) {
              $data = array('module' => $module,
                            'icon' => $module_class->getIcon(),
                            'title' => $module_class->getTitle(),
                            'subgroups' => $module_class->getSubGroups());

              if ( !isset( $access[$module_class->getGroup()][$module_class->getSortOrder()] ) ) {
                $access[$module_class->getGroup()][$module_class->getSortOrder()] = $data;
              } else {
                $access[$module_class->getGroup()][] = $data;
              }
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

    function getGroup($module = null) {
      if ( empty($module) ) {
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

    function getGroupTitle($group) {
      global $osC_Language;

      if ( !$osC_Language->isDefined('access_group_' . $group . '_title') ) {
        $osC_Language->loadIniFile( 'modules/access/groups/' . $group . '.php' );
      }

      return $osC_Language->get('access_group_' . $group . '_title');
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

    function getSubGroups() {
      return $this->_subgroups;
    }

    public static function hasAccess($site, $application) {
      return in_array($application, call_user_func(array('OSCOM_' . $site, 'getGuestApplications'))) || in_array($application, $_SESSION[$site]['access']);
    }
  }
?>

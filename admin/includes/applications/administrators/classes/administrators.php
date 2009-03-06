<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Administrators_Admin {
    const ACCESS_MODE_ADD = 'add';
    const ACCESS_MODE_SET = 'set';
    const ACCESS_MODE_REMOVE = 'remove';

    public static function get($id) {
      global $osC_Database;

      $Qadmin = $osC_Database->query('select * from :table_administrators where id = :id');
      $Qadmin->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
      $Qadmin->bindInt(':id', $id);
      $Qadmin->execute();

      $modules = array('access_modules' => array());

      $Qaccess = $osC_Database->query('select module from :table_administrators_access where administrators_id = :administrators_id');
      $Qaccess->bindTable(':table_administrators_access', TABLE_ADMINISTRATORS_ACCESS);
      $Qaccess->bindInt(':administrators_id', $id);
      $Qaccess->execute();

      while ( $Qaccess->next() ) {
        $modules['access_modules'][] = $Qaccess->value('module');
      }

      $data = array_merge($Qadmin->toArray(), $modules);

      unset($modules);
      $Qaccess->freeResult();
      $Qadmin->freeResult();

      return $data;
    }

    public static function getAll($pageset = 1) {
      global $osC_Database;

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array('entries' => array());

      $Qadmins = $osC_Database->query('select SQL_CALC_FOUND_ROWS * from :table_administrators order by user_name');
      $Qadmins->bindTable(':table_administrators', TABLE_ADMINISTRATORS);

      if ( $pageset !== -1 ) {
        $Qadmins->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qadmins->execute();

      while ( $Qadmins->next() ) {
        $result['entries'][] = $Qadmins->toArray();
      }

      $result['total'] = $Qadmins->getBatchSize();

      $Qadmins->freeResult();

      return $result;
    }

    public static function find($search, $pageset = 1) {
      global $osC_Database;

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array('entries' => array());

      $Qadmins = $osC_Database->query('select SQL_CALC_FOUND_ROWS * from :table_administrators where (user_name like :user_name) order by user_name');
      $Qadmins->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
      $Qadmins->bindValue(':user_name', '%' . $search . '%');

      if ( $pageset !== -1 ) {
        $Qadmins->setBatchLimit($pageset, MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qadmins->execute();

      while ( $Qadmins->next() ) {
        $result['entries'][] = $Qadmins->toArray();
      }

      $result['total'] = $Qadmins->getBatchSize();

      $Qadmins->freeResult();

      return $result;
    }

    public static function save($id = null, $data, $modules = null) {
      global $osC_Database;

      $error = false;

      $Qcheck = $osC_Database->query('select id from :table_administrators where user_name = :user_name');

      if ( is_numeric($id) ) {
        $Qcheck->appendQuery('and id != :id');
        $Qcheck->bindInt(':id', $id);
      }

      $Qcheck->appendQuery('limit 1');
      $Qcheck->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
      $Qcheck->bindValue(':user_name', $data['username']);
      $Qcheck->execute();

      if ($Qcheck->numberOfRows() < 1) {
        $osC_Database->startTransaction();

        if ( is_numeric($id) ) {
          $Qadmin = $osC_Database->query('update :table_administrators set user_name = :user_name');

          if ( isset($data['password']) && !empty($data['password']) ) {
            $Qadmin->appendQuery(', user_password = :user_password');
            $Qadmin->bindValue(':user_password', osc_encrypt_string(trim($data['password'])));
          }

          $Qadmin->appendQuery('where id = :id');
          $Qadmin->bindInt(':id', $id);
        } else {
          $Qadmin = $osC_Database->query('insert into :table_administrators (user_name, user_password) values (:user_name, :user_password)');
          $Qadmin->bindValue(':user_password', osc_encrypt_string(trim($data['password'])));
        }

        $Qadmin->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
        $Qadmin->bindValue(':user_name', $data['username']);
        $Qadmin->setLogging($_SESSION['module'], $id);
        $Qadmin->execute();

        if ( !$osC_Database->isError() ) {
          if ( !is_numeric($id) ) {
            $id = $osC_Database->nextID();
          }
        } else {
          $error = true;
        }

        if ( $error === false ) {
          if ( !empty($modules) ) {
            if ( in_array('0', $modules) ) {
              $modules = array('*');
            }

            foreach ($modules as $module) {
              $Qcheck = $osC_Database->query('select administrators_id from :table_administrators_access where administrators_id = :administrators_id and module = :module limit 1');
              $Qcheck->bindTable(':table_administrators_access', TABLE_ADMINISTRATORS_ACCESS);
              $Qcheck->bindInt(':administrators_id', $id);
              $Qcheck->bindValue(':module', $module);
              $Qcheck->execute();

              if ( $Qcheck->numberOfRows() < 1 ) {
                $Qinsert = $osC_Database->query('insert into :table_administrators_access (administrators_id, module) values (:administrators_id, :module)');
                $Qinsert->bindTable(':table_administrators_access', TABLE_ADMINISTRATORS_ACCESS);
                $Qinsert->bindInt(':administrators_id', $id);
                $Qinsert->bindValue(':module', $module);
                $Qinsert->setLogging($_SESSION['module'], $id);
                $Qinsert->execute();

                if ( $osC_Database->isError() ) {
                  $error = true;
                  break;
                }
              }
            }
          }
        }

        if ( $error === false ) {
          $Qdel = $osC_Database->query('delete from :table_administrators_access where administrators_id = :administrators_id');

          if ( !empty($modules) ) {
            $Qdel->appendQuery('and module not in (":module")');
            $Qdel->bindRaw(':module', implode('", "', $modules));
          }

          $Qdel->bindTable(':table_administrators_access', TABLE_ADMINISTRATORS_ACCESS);
          $Qdel->bindInt(':administrators_id', $id);
          $Qdel->setLogging($_SESSION['module'], $id);
          $Qdel->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }

        if ( $error === false ) {
          $osC_Database->commitTransaction();

          return 1;
        } else {
          $osC_Database->rollbackTransaction();

          return -1;
        }
      } else {
        return -2;
      }
    }

    public static function delete($id) {
      global $osC_Database;

      $osC_Database->startTransaction();

      $Qdel = $osC_Database->query('delete from :table_administrators_access where administrators_id = :administrators_id');
      $Qdel->bindTable(':table_administrators_access', TABLE_ADMINISTRATORS_ACCESS);
      $Qdel->bindInt(':administrators_id', $id);
      $Qdel->setLogging($_SESSION['module'], $id);
      $Qdel->execute();

      if ( !$osC_Database->isError() ) {
        $Qdel = $osC_Database->query('delete from :table_administrators where id = :id');
        $Qdel->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
        $Qdel->bindInt(':id', $id);
        $Qdel->setLogging($_SESSION['module'], $id);
        $Qdel->execute();

        if ( !$osC_Database->isError() ) {
          $osC_Database->commitTransaction();

          return true;
        }
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    public static function setAccessLevels($id, $modules, $mode = self::ACCESS_MODE_ADD) {
      global $osC_Database;

      $error = false;

      if ( in_array('0', $modules) ) {
        $modules = array('*');
      }

      $osC_Database->startTransaction();

      if ( ($mode == self::ACCESS_MODE_ADD) || ($mode == self::ACCESS_MODE_SET) ) {
        foreach ($modules as $module) {
          $execute = true;

          if ( $module != '*' ) {
            $Qcheck = $osC_Database->query('select administrators_id from :table_administrators_access where administrators_id = :administrators_id and module = :module limit 1');
            $Qcheck->bindTable(':table_administrators_access', TABLE_ADMINISTRATORS_ACCESS);
            $Qcheck->bindInt(':administrators_id', $id);
            $Qcheck->bindValue(':module', '*');
            $Qcheck->execute();

            if ( $Qcheck->numberOfRows() === 1 ) {
              $execute = false;
            }
          }

          if ( $execute === true ) {
            $Qcheck = $osC_Database->query('select administrators_id from :table_administrators_access where administrators_id = :administrators_id and module = :module limit 1');
            $Qcheck->bindTable(':table_administrators_access', TABLE_ADMINISTRATORS_ACCESS);
            $Qcheck->bindInt(':administrators_id', $id);
            $Qcheck->bindValue(':module', $module);
            $Qcheck->execute();

            if ( $Qcheck->numberOfRows() < 1 ) {
              $Qinsert = $osC_Database->query('insert into :table_administrators_access (administrators_id, module) values (:administrators_id, :module)');
              $Qinsert->bindTable(':table_administrators_access', TABLE_ADMINISTRATORS_ACCESS);
              $Qinsert->bindInt(':administrators_id', $id);
              $Qinsert->bindValue(':module', $module);
              $Qinsert->setLogging($_SESSION['module'], $id);
              $Qinsert->execute();

              if ( $osC_Database->isError() ) {
                $error = true;
                break;
              }
            }
          }
        }
      }

      if ( $error === false ) {
        if ( ($mode == self::ACCESS_MODE_REMOVE) || ($mode == self::ACCESS_MODE_SET) || in_array('*', $modules) ) {
          if ( !empty($modules) ) {
            $Qdel = $osC_Database->query('delete from :table_administrators_access where administrators_id = :administrators_id');

            if ( $mode == self::ACCESS_MODE_REMOVE ) {
              if ( !in_array('*', $modules) ) {
                $Qdel->appendQuery('and module in (":module")');
                $Qdel->bindRaw(':module', implode('", "', $modules));
              }
            } else {
              $Qdel->appendQuery('and module not in (":module")');
              $Qdel->bindRaw(':module', implode('", "', $modules));
            }

            $Qdel->bindTable(':table_administrators_access', TABLE_ADMINISTRATORS_ACCESS);
            $Qdel->bindInt(':administrators_id', $id);
            $Qdel->setLogging($_SESSION['module'], $id);
            $Qdel->execute();

            if ( $osC_Database->isError() ) {
              $error = true;
              break;
            }
          }
        }
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    public static function getAccessModules() {
      global $osC_Language;

      $osC_DirectoryListing = new osC_DirectoryListing('includes/modules/access');
      $osC_DirectoryListing->setIncludeDirectories(false);

      $modules = array();

      foreach ( $osC_DirectoryListing->getFiles() as $file ) {
        $module = substr($file['name'], 0, strrpos($file['name'], '.'));

        if ( !class_exists('osC_Access_' . ucfirst($module)) ) {
          $osC_Language->loadIniFile('modules/access/' . $file['name']);
          include($osC_DirectoryListing->getDirectory() . '/' . $file['name']);
        }

        $module = 'osC_Access_' . ucfirst($module);
        $module = new $module();

        $modules[osC_Access::getGroupTitle( $module->getGroup() )][] = array('id' => $module->getModule(),
                                                                             'text' => $module->getTitle());
      }

      ksort($modules);

      return $modules;
    }
  }
?>

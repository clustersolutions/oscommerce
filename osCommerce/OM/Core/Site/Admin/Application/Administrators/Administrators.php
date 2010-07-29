<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Administrators;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\DirectoryListing;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Access;

  class Administrators {
    const ACCESS_MODE_ADD = 'add';
    const ACCESS_MODE_SET = 'set';
    const ACCESS_MODE_REMOVE = 'remove';

    public static function get($id) {
      $OSCOM_Database = Registry::get('Database');

      $Qadmin = $OSCOM_Database->query('select * from :table_administrators where id = :id');
      $Qadmin->bindInt(':id', $id);
      $Qadmin->execute();

      $modules = array('access_modules' => array());

      $Qaccess = $OSCOM_Database->query('select module from :table_administrators_access where administrators_id = :administrators_id');
      $Qaccess->bindInt(':administrators_id', $id);
      $Qaccess->execute();

      while ( $Qaccess->next() ) {
        $modules['access_modules'][] = $Qaccess->value('module');
      }

      $data = array_merge($Qadmin->toArray(), $modules);

      return $data;
    }

    public static function getAll($pageset = 1) {
      $OSCOM_Database = Registry::get('Database');

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array('entries' => array());

      $Qadmins = $OSCOM_Database->query('select SQL_CALC_FOUND_ROWS * from :table_administrators order by user_name');

      if ( $pageset !== -1 ) {
        $Qadmins->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qadmins->execute();

      while ( $Qadmins->next() ) {
        $result['entries'][] = $Qadmins->toArray();
      }

      $result['total'] = $Qadmins->getBatchSize();

      return $result;
    }

    public static function find($search, $pageset = 1) {
      $OSCOM_Database = Registry::get('Database');

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array('entries' => array());

      $Qadmins = $OSCOM_Database->query('select SQL_CALC_FOUND_ROWS * from :table_administrators where (user_name like :user_name) order by user_name');
      $Qadmins->bindValue(':user_name', '%' . $search . '%');

      if ( $pageset !== -1 ) {
        $Qadmins->setBatchLimit($pageset, MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qadmins->execute();

      while ( $Qadmins->next() ) {
        $result['entries'][] = $Qadmins->toArray();
      }

      $result['total'] = $Qadmins->getBatchSize();

      return $result;
    }

    public static function save($id = null, $data, $modules = null) {
      $OSCOM_Database = Registry::get('Database');

      $error = false;

      $Qcheck = $OSCOM_Database->query('select id from :table_administrators where user_name = :user_name');

      if ( is_numeric($id) ) {
        $Qcheck->appendQuery('and id != :id');
        $Qcheck->bindInt(':id', $id);
      }

      $Qcheck->appendQuery('limit 1');
      $Qcheck->bindValue(':user_name', $data['username']);
      $Qcheck->execute();

      if ( $Qcheck->numberOfRows() < 1 ) {
        $OSCOM_Database->startTransaction();

        if ( is_numeric($id) ) {
          $Qadmin = $OSCOM_Database->query('update :table_administrators set user_name = :user_name');

          if ( isset($data['password']) && !empty($data['password']) ) {
            $Qadmin->appendQuery(', user_password = :user_password');
            $Qadmin->bindValue(':user_password', osc_encrypt_string(trim($data['password'])));
          }

          $Qadmin->appendQuery('where id = :id');
          $Qadmin->bindInt(':id', $id);
        } else {
          $Qadmin = $OSCOM_Database->query('insert into :table_administrators (user_name, user_password) values (:user_name, :user_password)');
          $Qadmin->bindValue(':user_password', osc_encrypt_string(trim($data['password'])));
        }

        $Qadmin->bindValue(':user_name', $data['username']);
        $Qadmin->setLogging(null, $id);
        $Qadmin->execute();

        if ( !$OSCOM_Database->isError() ) {
          if ( !is_numeric($id) ) {
            $id = $OSCOM_Database->nextID();
          }
        } else {
          $error = true;
        }

        if ( $error === false ) {
          if ( !empty($modules) ) {
            if ( in_array('0', $modules) ) {
              $modules = array('*');
            }

            foreach ( $modules as $module ) {
              $Qcheck = $OSCOM_Database->query('select administrators_id from :table_administrators_access where administrators_id = :administrators_id and module = :module limit 1');
              $Qcheck->bindInt(':administrators_id', $id);
              $Qcheck->bindValue(':module', $module);
              $Qcheck->execute();

              if ( $Qcheck->numberOfRows() < 1 ) {
                $Qinsert = $OSCOM_Database->query('insert into :table_administrators_access (administrators_id, module) values (:administrators_id, :module)');
                $Qinsert->bindInt(':administrators_id', $id);
                $Qinsert->bindValue(':module', $module);
                $Qinsert->setLogging(null, $id);
                $Qinsert->execute();

                if ( $OSCOM_Database->isError() ) {
                  $error = true;
                  break;
                }
              }
            }
          }
        }

        if ( $error === false ) {
          $Qdel = $OSCOM_Database->query('delete from :table_administrators_access where administrators_id = :administrators_id');

          if ( !empty($modules) ) {
            $Qdel->appendQuery('and module not in (":module")');
            $Qdel->bindRaw(':module', implode('", "', $modules));
          }

          $Qdel->bindInt(':administrators_id', $id);
          $Qdel->setLogging(null, $id);
          $Qdel->execute();

          if ( $OSCOM_Database->isError() ) {
            $error = true;
          }
        }

        if ( $error === false ) {
          $OSCOM_Database->commitTransaction();

          return 1;
        } else {
          $OSCOM_Database->rollbackTransaction();

          return -1;
        }
      } else {
        return -2;
      }
    }

    public static function delete($id) {
      $OSCOM_Database = Registry::get('Database');

      $Qdel = $OSCOM_Database->query('delete from :table_administrators where id = :id');
      $Qdel->bindInt(':id', $id);
      $Qdel->setLogging(null, $id);
      $Qdel->execute();

      return !$OSCOM_Database->isError();
    }

    public static function setAccessLevels($id, $modules, $mode = self::ACCESS_MODE_ADD) {
      $OSCOM_Database = Registry::get('Database');

      $error = false;

      if ( in_array('0', $modules) ) {
        $modules = array('*');
      }

      $OSCOM_Database->startTransaction();

      if ( ($mode == self::ACCESS_MODE_ADD) || ($mode == self::ACCESS_MODE_SET) ) {
        foreach ( $modules as $module ) {
          $execute = true;

          if ( $module != '*' ) {
            $Qcheck = $OSCOM_Database->query('select administrators_id from :table_administrators_access where administrators_id = :administrators_id and module = :module limit 1');
            $Qcheck->bindInt(':administrators_id', $id);
            $Qcheck->bindValue(':module', '*');
            $Qcheck->execute();

            if ( $Qcheck->numberOfRows() === 1 ) {
              $execute = false;
            }
          }

          if ( $execute === true ) {
            $Qcheck = $OSCOM_Database->query('select administrators_id from :table_administrators_access where administrators_id = :administrators_id and module = :module limit 1');
            $Qcheck->bindInt(':administrators_id', $id);
            $Qcheck->bindValue(':module', $module);
            $Qcheck->execute();

            if ( $Qcheck->numberOfRows() < 1 ) {
              $Qinsert = $OSCOM_Database->query('insert into :table_administrators_access (administrators_id, module) values (:administrators_id, :module)');
              $Qinsert->bindInt(':administrators_id', $id);
              $Qinsert->bindValue(':module', $module);
              $Qinsert->setLogging(null, $id);
              $Qinsert->execute();

              if ( $OSCOM_Database->isError() ) {
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
            $Qdel = $OSCOM_Database->query('delete from :table_administrators_access where administrators_id = :administrators_id');

            if ( $mode == self::ACCESS_MODE_REMOVE ) {
              if ( !in_array('*', $modules) ) {
                $Qdel->appendQuery('and module in (":module")');
                $Qdel->bindRaw(':module', implode('", "', $modules));
              }
            } else {
              $Qdel->appendQuery('and module not in (":module")');
              $Qdel->bindRaw(':module', implode('", "', $modules));
            }

            $Qdel->bindInt(':administrators_id', $id);
            $Qdel->setLogging(null, $id);
            $Qdel->execute();

            if ( $OSCOM_Database->isError() ) {
              $error = true;
              break;
            }
          }
        }
      }

      if ( $error === false ) {
        $OSCOM_Database->commitTransaction();

        return true;
      }

      $OSCOM_Database->rollbackTransaction();

      return false;
    }

    public static function getAccessModules() {
      $OSCOM_Language = Registry::get('Language');

      $module_files = array();

      $DLapps = new DirectoryListing(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::getSite() . '/Application');
      $DLapps->setIncludeFiles(false);

      foreach ( $DLapps->getFiles() as $file ) {
        if ( preg_match('/[A-Z]/', substr($file['name'], 0, 1)) && !in_array($file['name'], call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite(), 'getGuestApplications'))) && file_exists($DLapps->getDirectory() . '/' . $file['name'] . '/' . $file['name'] . '.php') ) { // HPDL remove preg_match
          $module_files[] = $file['name'];
        }
      }

      $modules = array();

      foreach ( $module_files as $module ) {
        $application_class = 'osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Application\\' . $module;

        if ( class_exists($application_class) ) {
          if ( $module == OSCOM::getSiteApplication() ) {
            $OSCOM_Application = Registry::get('Application');
          } else {
            Registry::get('Language')->loadIniFile($module . '.php');
            $OSCOM_Application = new $application_class(false);
          }

          $modules[Access::getGroupTitle($OSCOM_Application->getGroup())][] = array('id' => $module,
                                                                                    'text' => $OSCOM_Application->getTitle(),
                                                                                    'icon' => $OSCOM_Application->getIcon());
        }
      }

      ksort($modules);

      return $modules;
    }
  }
?>

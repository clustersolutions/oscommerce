<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  define('OSC_ADMINISTRATORS_ACCESS_MODE_ADD', 'add');
  define('OSC_ADMINISTRATORS_ACCESS_MODE_SET', 'set');
  define('OSC_ADMINISTRATORS_ACCESS_MODE_REMOVE', 'remove');

  class osC_Administrators_Admin {
    function getData($id) {
      global $osC_Database;

      $Qadmin = $osC_Database->query('select id, user_name from :table_administrators where id = :id');
      $Qadmin->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
      $Qadmin->bindInt(':id', $id);
      $Qadmin->execute();

      $modules = array( 'access_modules' => array() );

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

    function save($id = null, $data, $modules = null) {
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
            $Qadmin->appendQuery('and user_password = :user_password');
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
            if ( in_array('*', $modules) ) {
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
          $Qdel->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }

        if ( $error === false ) {
          $osC_Database->commitTransaction();

          return true;
        } else {
          $osC_Database->rollbackTransaction();

          return false;
        }
      } else {
        return -1;
      }
    }

    function delete($id) {
      global $osC_Database;

      $osC_Database->startTransaction();

      $Qdel = $osC_Database->query('delete from :table_administrators_access where administrators_id = :administrators_id');
      $Qdel->bindTable(':table_administrators_access', TABLE_ADMINISTRATORS_ACCESS);
      $Qdel->bindInt(':administrators_id', $id);
      $Qdel->execute();

      if ( !$osC_Database->isError() ) {
        $Qdel = $osC_Database->query('delete from :table_administrators where id = :id');
        $Qdel->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
        $Qdel->bindInt(':id', $id);
        $Qdel->execute();

        if ( !$osC_Database->isError() ) {
          $osC_Database->commitTransaction();

          return true;
        }
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    function setAccessLevels($id, $modules, $mode = OSC_ADMINISTRATORS_ACCESS_MODE_ADD) {
      global $osC_Database;

      $error = false;

      if ( in_array('*', $modules) ) {
        $modules = array('*');
      }

      $osC_Database->startTransaction();

      if ( ($mode == OSC_ADMINISTRATORS_ACCESS_MODE_ADD) || ($mode == OSC_ADMINISTRATORS_ACCESS_MODE_SET) ) {
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
        if ( ($mode == OSC_ADMINISTRATORS_ACCESS_MODE_REMOVE) || ($mode == OSC_ADMINISTRATORS_ACCESS_MODE_SET) || in_array('*', $modules) ) {
          if ( !empty($modules) ) {
            $Qdel = $osC_Database->query('delete from :table_administrators_access where administrators_id = :administrators_id');

            if ( $mode == OSC_ADMINISTRATORS_ACCESS_MODE_REMOVE ) {
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
  }
?>

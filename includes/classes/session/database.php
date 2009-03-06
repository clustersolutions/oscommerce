<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

/**
 * The osC_Session_database class stores the session data in the database
 */

  class osC_Session_database extends osC_Session {

/**
 * Constructor, loads the database based session storage handler
 *
 * @param string $name The name of the session
 * @access public
 */

    public function __construct($name = null) {
      parent::__construct($name);

      session_set_save_handler(array(&$this, '_custom_open'),
                               array(&$this, '_custom_close'),
                               array(&$this, '_custom_read'),
                               array(&$this, '_custom_write'),
                               array(&$this, '_custom_destroy'),
                               array(&$this, '_custom_gc'));
    }

/**
 * Opens the database based session storage handler
 *
 * @access protected
 */

    protected function _custom_open() {
      return true;
    }

/**
 * Closes the database based session storage handler
 *
 * @access protected
 */

    protected function _custom_close() {
      return true;
    }

/**
 * Read session data from the database based session storage handler
 *
 * @param string $id The ID of the session
 * @access protected
 */

    protected function _custom_read($id) {
      global $osC_Database;

      $Qsession = $osC_Database->query('select value from :table_sessions where sesskey = :sesskey');

      if ( SERVICE_SESSION_EXPIRATION_TIME > 0 ) {
        $Qsession->appendQuery('and expiry > :expiry');
        $Qsession->bindInt(':expiry', time());
      }

      $Qsession->bindTable(':table_sessions', TABLE_SESSIONS);
      $Qsession->bindValue(':sesskey', $id);
      $Qsession->execute();

      if ( $Qsession->numberOfRows() === 1 ) {
        return $Qsession->value('value');
      }

      return false;
    }

/**
 * Writes session data to the database based session storage handler
 *
 * @param string $id The ID of the session
 * @param string $value The session data to store
 * @access protected
 */

    protected function _custom_write($id, $value) {
      global $osC_Database;

      $Qsession = $osC_Database->query('select sesskey from :table_sessions where sesskey = :sesskey');
      $Qsession->bindTable(':table_sessions', TABLE_SESSIONS);
      $Qsession->bindValue(':sesskey', $id);
      $Qsession->execute();

      if ( $Qsession->numberOfRows() === 1 ) {
        $Qsession = $osC_Database->query('update :table_sessions set expiry = :expiry, value = :value where sesskey = :sesskey');
      } else {
        $Qsession = $osC_Database->query('insert into :table_sessions values (:sesskey, :expiry, :value)');
      }
      $Qsession->bindTable(':table_sessions', TABLE_SESSIONS);
      $Qsession->bindInt(':expiry', time() + (SERVICE_SESSION_EXPIRATION_TIME * 60));
      $Qsession->bindValue(':value', $value);
      $Qsession->bindValue(':sesskey', $id);
      $Qsession->execute();

      return ( $Qsession->affectedRows() === 1 );
    }

/**
 * Destroys the session data from the database based session storage handler
 *
 * @param string $id The ID of the session
 * @access protected
 */

    protected function _custom_destroy($id) {
      return $this->delete($id);
    }

/**
 * Garbage collector for the database based session storage handler
 *
 * @param string $max_life_time The maxmimum time a session should exist
 * @access protected
 */

    protected function _custom_gc($max_life_time) {
      global $osC_Database;

// $max_life_time is already added to the time in the _custom_write method

      $Qsession = $osC_Database->query('delete from :table_sessions where expiry < :expiry');
      $Qsession->bindTable(':table_sessions', TABLE_SESSIONS);
      $Qsession->bindValue(':expiry', time());
      $Qsession->execute();

      return ( $Qsession->affectedRows() > 0 );
    }

/**
 * Deletes the session data from the database based session storage handler
 *
 * @param string $id The ID of the session
 * @access public
 */

    public function delete($id = null) {
      global $osC_Database;

      if ( empty($id) ) {
        $id = $this->_id;
      }

      $Qsession = $osC_Database->query('delete from :table_sessions where sesskey = :sesskey');
      $Qsession->bindTable(':table_sessions', TABLE_SESSIONS);
      $Qsession->bindValue(':sesskey', $id);
      $Qsession->execute();

      return ( $Qsession->affectedRows() === 1 );
    }
  }
?>

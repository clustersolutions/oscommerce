<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2014 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Session;

  use osCommerce\OM\Core\OSCOM;

/**
 * The Session\Database class stores the session data in the database
 * 
 * @since v3.0.0
 */

  class Database extends \osCommerce\OM\Core\SessionAbstract implements \SessionHandlerInterface {

/**
 * Initialize database storage handler
 *
 * @since v3.0.0
 */

    public function __construct() {
      session_set_save_handler($this, true);
    }

/**
 * Checks if a session exists
 *
 * @param string $id The ID of the session
 * @since v3.0.2
 */

    public function exists($id) {
      $data = array('id' => $id);

      return OSCOM::callDB('Session\Database\Check', $data, 'Core');
    }

/**
 * Opens the database storage handler
 *
 * @since v3.0.3
 */

    public function open($save_path, $id) {
      return true;
    }

/**
 * Closes the database storage handler
 *
 * @since v3.0.3
 */

    public function close() {
      return true;
    }

/**
 * Read session data from the database storage handler
 *
 * @param string $id The ID of the session
 * @since v3.0.3
 */

    public function read($id) {
      $data = array('id' => $id);

      $result = OSCOM::callDB('Session\Database\Get', $data, 'Core');

      if ( $result !== false ) {
        return $result['value'];
      }

      return false;
    }

/**
 * Writes session data to the database storage handler
 *
 * @param string $id The ID of the session
 * @param string $value The session data to store
 * @since v3.0.3
 */

    public function write($id, $value) {
      $data = array('id' => $id,
                    'expiry' => time(),
                    'value' => $value);

      return OSCOM::callDB('Session\Database\Save', $data, 'Core');
    }

/**
 * Deletes the session data from the database storage handler
 *
 * @param string $id The ID of the session
 * @since v3.0.3
 */

    public function destroy($id) {
      $data = array('id' => $id);

      return OSCOM::callDB('Session\Database\Delete', $data, 'Core');
    }

/**
 * Garbage collector for the database storage handler
 *
 * @param string $max_life_time The maxmimum time a session should exist
 * @since v3.0.3
 */

    public function gc($max_life_time) {
      $data = array('expiry' => $max_life_time);

      return OSCOM::callDB('Session\Database\DeleteExpired', $data, 'Core');
    }
  }
?>

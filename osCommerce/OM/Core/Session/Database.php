<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Session;

  use osCommerce\OM\Core\OSCOM;

/**
 * The Session\Database class stores the session data in the database
 */

  class Database extends \osCommerce\OM\Core\SessionAbstract {

/**
 * Initialize database based session storage handler
 *
 * @param string $name The name of the session
 * @access public
 */

    public function __construct($name) {
      $this->setName($name);

      session_set_save_handler(array($this, 'handlerOpen'),
                               array($this, 'handlerClose'),
                               array($this, 'handlerRead'),
                               array($this, 'handlerWrite'),
                               array($this, 'handlerDestroy'),
                               array($this, 'handlerClean'));
    }

/**
 * Opens the database based session storage handler
 *
 * @access public
 */

    public function handlerOpen() {
      return true;
    }

/**
 * Closes the database based session storage handler
 *
 * @access public
 */

    public function handlerClose() {
      return true;
    }

/**
 * Read session data from the database based session storage handler
 *
 * @param string $id The ID of the session
 * @access public
 */

    public function handlerRead($id) {
      $data = array('id' => $id);

      if ( $this->_life_time > 0 ) {
        $data['expiry'] = time();
      }

      $result = OSCOM::callDB('Session\Database\Get', $data, 'Core');

      if ( $result !== false ) {
        return base64_decode($result['value']);
      }

      return false;
    }

/**
 * Writes session data to the database based session storage handler
 *
 * @param string $id The ID of the session
 * @param string $value The session data to store
 * @access public
 */

    public function handlerWrite($id, $value) {
      $data = array('id' => $id,
                    'expiry' => time() + $this->_life_time,
                    'value' => base64_encode($value));

      return OSCOM::callDB('Session\Database\Save', $data, 'Core');
    }

/**
 * Destroys the session data from the database based session storage handler
 *
 * @param string $id The ID of the session
 * @access public
 */

    public function handlerDestroy($id) {
      return $this->delete($id);
    }

/**
 * Garbage collector for the database based session storage handler
 *
 * @param string $max_life_time The maxmimum time a session should exist
 * @access public
 */

    public function handlerClean($max_life_time) {
// $max_life_time is already added to the time in the _custom_write method

      $data = array('expiry' => time());

      return OSCOM::callDB('Session\Database\DeleteExpired', $data, 'Core');
    }

/**
 * Deletes the session data from the database based session storage handler
 *
 * @param string $id The ID of the session
 * @access public
 */

    public function delete($id = null) {
      if ( empty($id) ) {
        $id = $this->_id;
      }

      $data = array('id' => $id);

      return OSCOM::callDB('Session\Database\Delete', $data, 'Core');
    }
  }
?>

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
 * The Session\File class stores the session data in files
 * 
 * @since v3.0.0
 */

  class File extends \osCommerce\OM\Core\SessionAbstract {

/**
 * Holds the file system path where sessions are saved
 *
 * @var string
 * @since v3.0.0
 */

    protected $_save_path;

/**
 * Initialize file storage handler
 *
 * @since v3.0.0
 */

    public function __construct() {
      $this->setSavePath(OSCOM::BASE_DIRECTORY . 'Work/Session');

      register_shutdown_function('session_write_close');
    }

/**
 * Checks if a session exists
 *
 * @param string $id The ID of the session
 * @since v3.0.2
 */

    public function exists($id) {
      $id = basename($id);

      return file_exists($this->_save_path . '/sess_' . $id);
    }

/**
 * Deletes the session data from the file storage handler
 *
 * @param string $id The ID of the session
 * @since v3.0.0
 */

    public function destroy($id) {
      $id = basename($id);

      if ( $this->exists($id) ) {
        return unlink($this->_save_path . '/sess_' . $id);
      }

      return false;
    }

/**
 * Return the session file storage location
 *
 * @return string
 * @since v3.0.0
 */

    public function getSavePath() {
      return $this->_save_path;
    }

/**
 * Sets the storage location for the file storage handler
 *
 * @param string $path The file path to store the session data in
 * @since v3.0.0
 */

    public function setSavePath($path) {
      if ( substr($path, -1) == '/' ) {
        $path = substr($path, 0, -1);
      }

      session_save_path($path);

      $this->_save_path = session_save_path();
    }
  }
?>

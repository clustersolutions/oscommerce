<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class NavigationHistory {
    protected $_data = array();
    protected $_snapshot = array();

    public function __construct($add_current_page = false) {
      if ( isset($_SESSION['osC_NavigationHistory_data']) && is_array($_SESSION['osC_NavigationHistory_data']) && !empty($_SESSION['osC_NavigationHistory_data']) ) {
        $this->_data =& $_SESSION['osC_NavigationHistory_data'];
      }

      if ( isset($_SESSION['osC_NavigationHistory_snapshot']) && is_array($_SESSION['osC_NavigationHistory_snapshot']) && !empty($_SESSION['osC_NavigationHistory_snapshot']) ) {
        $this->_snapshot =& $_SESSION['osC_NavigationHistory_snapshot'];
      }

      if ( $add_current_page === true ) {
        $this->addCurrentPage();
      }
    }

    public function addCurrentPage() {
      $set = 'true';

      for ( $i=0, $n=sizeof($this->_data); $i<$n; $i++ ) {
        if ( $this->_data[$i]['page'] == basename($_SERVER['SCRIPT_FILENAME']) ) {
          array_splice($this->_data, $i);
          $set = 'true';
          break;
        }
      }

      if ( $set == 'true' ) {
        $this->_data[] = array('page' => basename($_SERVER['SCRIPT_FILENAME']),
                               'mode' => OSCOM::getRequestType(),
                               'get' => $_GET,
                               'post' => $_POST);

        if ( !isset($_SESSION['osC_NavigationHistory_data']) ) {
          $_SESSION['osC_NavigationHistory_data'] = $this->_data;
        }
      }
    }

    function removeCurrentPage() {
      $last_entry_position = sizeof($this->_data) - 1;

      if ( $this->_data[$last_entry_position]['page'] == basename($_SERVER['SCRIPT_FILENAME']) ) {
        unset($this->_data[$last_entry_position]);

        if ( sizeof($this->_data) > 0 ) {
          if ( !isset($_SESSION['osC_NavigationHistory_data']) ) {
            $_SESSION['osC_NavigationHistory_data'] = $this->_data;
          }
        } else {
          $this->resetPath();
        }
      }
    }

    function hasPath($back = 1) {
      if ( (is_numeric($back) === false) || (is_numeric($back) && ($back < 1)) ) {
        $back = 1;
      }

      return isset($this->_data[sizeof($this->_data) - $back]);
    }

    function getPathURL($back = 1, $exclude = array()) {
      if ( (is_numeric($back) === false) || (is_numeric($back) && ($back < 1)) ) {
        $back = 1;
      }

      $back = sizeof($this->_data) - $back;

      return OSCOM::getLink(null, null, $this->_parseParameters($this->_data[$back]['get'], $exclude), $this->_data[$back]['mode']);
    }

    function setSnapshot($page = '') {
      if ( is_array($page) ) {
        $this->_snapshot = array('page' => $page['page'],
                                 'mode' => $page['mode'],
                                 'get' => $page['get'],
                                 'post' => $page['post']);
      } else {
        $this->_snapshot = array('page' => basename($_SERVER['SCRIPT_FILENAME']),
                                 'mode' => OSCOM::getRequestType(),
                                 'get' => $_GET,
                                 'post' => $_POST);
      }

      if ( !isset($_SESSION['osC_NavigationHistory_snapshot']) ) {
        $_SESSION['osC_NavigationHistory_snapshot'] = $this->_snapshot;
      }
    }

    function hasSnapshot() {
      return !empty($this->_snapshot);
    }

    function getSnapshot($key) {
      if ( isset($this->_snapshot[$key]) ) {
        return $this->_snapshot[$key];
      }
    }

    function getSnapshotURL($auto_mode = false) {
      if ( $this->hasSnapshot() ) {
        $target = OSCOM::getLink(null, null, $this->_parseParameters($this->_snapshot['get']), ($auto_mode === true) ? 'AUTO' : $this->_snapshot['mode']);
      } else {
        $target = OSCOM::getLink(null, null, null, ($auto_mode === true) ? 'AUTO' : $this->_snapshot['mode']);
      }

      return $target;
    }

    function redirectToSnapshot() {
      $target = $this->getSnapshotURL(true);

      $this->resetSnapshot();

      OSCOM::redirect($target);
    }

    function resetPath() {
      $this->_data = array();

      if ( isset($_SESSION['osC_NavigationHistory_data']) ) {
        unset($_SESSION['osC_NavigationHistory_data']);
      }
    }

    function resetSnapshot() {
      $this->_snapshot = array();

      if ( isset($_SESSION['osC_NavigationHistory_snapshot']) ) {
        unset($_SESSION['osC_NavigationHistory_snapshot']);
      }
    }

    function reset() {
      $this->resetPath();
      $this->resetSnapshot();
    }

    function _parseParameters($array, $additional_exclude = array()) {
      $exclude = array('x', 'y', Registry::get('Session')->getName());

      if ( is_array($additional_exclude) && !empty($additional_exclude) ) {
        $exclude = array_merge($exclude, $additional_exclude);
      }

      $string = '';

      if ( is_array($array) && !empty($array) ) {
        foreach ( $array as $key => $value ) {
          if ( !in_array($key, $exclude) ) {
            $string .= $key . '=' . $value . '&';
          }
        }

        $string = substr($string, 0, -1);
      }

      return $string;
    }
  }
?>

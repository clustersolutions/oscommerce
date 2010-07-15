<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core;

/**
 * The MessageStack class manages information messages to be displayed.
 * Messages shown are automatically removed from the stack.
 * Core message types: info, success, warning, error
 */

  class MessageStack {

/**
 * The storage handler for the messages
 *
 * @var array
 * @access protected
 */

    protected $_data = array();

/**
 * Constructor, registers a shutdown function to store the remaining messages
 * in the session
 *
 * @access public
 */

    public function __construct() {
      register_shutdown_function(array($this, 'saveInSession'));
    }

/**
 * Loads messages stored in the session into the stack
 *
 * @access public
 */

    public function loadFromSession() {
      if ( isset($_SESSION['osC_MessageStack_Data']) && !empty($_SESSION['osC_MessageStack_Data']) ) {
        foreach ( $_SESSION['osC_MessageStack_Data'] as $group => $messages ) {
          foreach ( $messages as $message ) {
            $this->_data[$group][] = $message;
          }
        }

        unset($_SESSION['osC_MessageStack_Data']);
      }
    }

/**
 * Stores remaining messages in the session
 *
 * @access public
 */

    public function saveInSession() {
      if ( !empty($this->_data) ) {
        $_SESSION['osC_MessageStack_Data'] = $this->_data;
      }
    }

/**
 * Add a message to the stack
 *
 * @param string $group The group the message belongs to
 * @param string $message The message information text
 * @param string $type The type of message: info, error, warning, success
 * @access public
 */

    public function add($group = null, $message, $type = 'error') {
      if ( empty($group) ) {
        $group = OSCOM::getSiteApplication();
      }

      $this->_data[$group][] = array('text' => $message,
                                     'type' => $type);
    }

/**
 * Reset the message stack
 *
 * @access public
 */

    public function reset() {
      $this->_data = array();
    }

/**
 * Checks to see if a group in the stack contains messages
 *
 * @param string $group The name of the group to check
 * @access public
 */

    public function exists($group = null) {
      if ( empty($group) ) {
        $group = OSCOM::getSiteApplication();
      }

      return ( isset($this->_data[$group]) && !empty($this->_data[$group]) );
    }

/**
 * Checks to see if the message stack contains messages
 *
 * @access public
 */

    public function hasContent() {
      return !empty($this->_data);
    }

/**
 * Get the messages belonging to a group. The messages are placed into an
 * unsorted list wrapped in a DIV element with the "messageStack" style sheet
 * class.
 *
 * @param string $group The name of the group to get the messages from
 * @access public
 */

    public function get($group = null) {
      if ( empty($group) ) {
        $group = OSCOM::getSiteApplication();
      }

      $result = false;

      if ( $this->exists($group) ) {
        $result = '<div class="messageStack"><ul>';

        foreach ( $this->_data[$group] as $message ) {
          switch ( $message['type'] ) {
            case 'error':
              $bullet_image = 'error.gif';
              break;

            case 'warning':
              $bullet_image = 'warning.gif';
              break;

            case 'success':
              $bullet_image = 'success.gif';
              break;

            default:
              $bullet_image = 'bullet_default.gif';
          }

          $result .= '<li style="list-style-image: url(\'' . DIR_WS_IMAGES . 'icons/' . $bullet_image . '\')">' . osc_output_string($message['text']) . '</li>';
        }

        $result .= '</ul></div>';

        unset($this->_data[$group]);
      }

      return $result;
    }

/**
 * Get the messages belonging to a group. The messages are separated by a new
 * line character.
 *
 * @param string $group The name of the group to get the messages from
 * @access public
 */

    public function getRaw($group = null) {
      if ( empty($group) ) {
        $group = OSCOM::getSiteApplication();
      }

      $result = false;

      if ( $this->exists($group) ) {
        $result = '';

        foreach ( $this->_data[$group] as $message ) {
          $result .= osc_output_string($message['text']) . "\n";
        }

        unset($this->_data[$group]);
      }

      return $result;
    }

/**
 * Get the message stack array data set
 *
 * @access public
 */

    public function getAll() {
      return $this->_data;
    }

/**
 * Get the number of messages belonging to a group
 *
 * @param string $group The name of the group to check
 * @access public
 */

    public function size($group = null) {
      if ( empty($group) ) {
        $group = OSCOM::getSiteApplication();
      }

      $size = 0;

      if ( $this->exists($group) ) {
        $size = sizeof($this->_data[$group]);
      }

      return $size;
    }
  }
?>

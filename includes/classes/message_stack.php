<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

/**
 * The osC_MessageStack class manages information messages to be displayed.
 * Messages that are shown are automatically removed from the stack.
 */

  class osC_MessageStack {

/**
 * A reference to the messages stored in the session messageToStack variable
 *
 * @var array
 * @access private
 */

    private $_data = array();

/**
 * Constructor, references the session data to the private $_data variable
 *
 * @access public
 */

    public function __construct() {
      if ( !isset($_SESSION['messageToStack']) ) {
        $_SESSION['messageToStack'] = array();
      }

      $this->_data =& $_SESSION['messageToStack'];
    }

/**
 * Add a message to the stack
 *
 * @param string $group The group the message belongs to
 * @param string $message The message information text
 * @param string $type The type of message: error, warning, success
 * @access public
 */

    public function add($group, $message, $type = 'error') {
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

    public function exists($group) {
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

    public function get($group) {
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

    public function getRaw($group) {
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

    public function size($group) {
      $size = 0;

      if ( $this->exists($group) ) {
        $size = sizeof($this->_data[$group]);
      }

      return $size;
    }
  }
?>

<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  class messageStack {
    var $messages;

// class constructor
    function messageStack() {
      $this->messages = array();
    }

// class methods
    function add($class, $message, $type = 'error') {
      $this->messages[] = array('class' => $class, 'type' => $type, 'message' => $message);
    }

    function add_session($class, $message, $type = 'error') {
      global $osC_Session;

      if ($osC_Session->exists('messageToStack')) {
        $messageToStack = $osC_Session->value('messageToStack');
      } else {
        $messageToStack = array();
      }

      $messageToStack[] = array('class' => $class, 'text' => $message, 'type' => $type);

      $osC_Session->set('messageToStack', $messageToStack);

      $this->add($class, $message, $type);
    }

    function reset() {
      $this->messages = array();
    }

    function output($class) {
      $messages = '<ul>';
      for ($i=0, $n=sizeof($this->messages); $i<$n; $i++) {
        if ($this->messages[$i]['class'] == $class) {
          switch ($this->messages[$i]['type']) {
            case 'error':
              $bullet_image = DIR_WS_ICONS . 'error.gif';
              break;
            case 'warning':
              $bullet_image = DIR_WS_ICONS . 'warning.gif';
              break;
            case 'success':
              $bullet_image = DIR_WS_ICONS . 'success.gif';
              break;
            default:
              $bullet_image = DIR_WS_ICONS . 'bullet_default.gif';
          }

          $messages .= '<li style="list-style-image: url(\'' . $bullet_image . '\')">' . tep_output_string($this->messages[$i]['message']) . '</li>';
        }
      }
      $messages .= '</ul>';

      return '<div class="messageStack">' . $messages . '</div>';
    }

    function outputPlain($class) {
      $message = false;

      for ($i=0, $n=sizeof($this->messages); $i<$n; $i++) {
        if ($this->messages[$i]['class'] == $class) {
          $message = tep_output_string($this->messages[$i]['message']);
          break;
        }
      }

      return $message;
    }

    function size($class) {
      $class_size = 0;

      for ($i=0, $n=sizeof($this->messages); $i<$n; $i++) {
        if ($this->messages[$i]['class'] == $class) {
          $class_size++;
        }
      }

      return $class_size;
    }

    function loadFromSession() {
      global $osC_Session;

      if ($osC_Session->exists('messageToStack')) {
        $messageToStack = $osC_Session->value('messageToStack');

        for ($i=0, $n=sizeof($messageToStack); $i<$n; $i++) {
          $this->add($messageToStack[$i]['class'], $messageToStack[$i]['text'], $messageToStack[$i]['type']);
        }

        $osC_Session->remove('messageToStack');
      }
    }
  }
?>

<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('../includes/classes/message_stack.php');

  class osC_MessageStack_Admin extends osC_MessageStack {
    public function __construct() {
      parent::__construct();
    }

    public function get($group) {
      $result = false;

      if ( $this->exists($group) ) {
        $data = array();

        foreach ( $this->_data[$group] as $message ) {
          $data['messageStack' . ucfirst($message['type'])][] = $message['text'];
        }

        $result = '';

        foreach ( $data as $type => $messages ) {
          $result .= '<div class="' . osc_output_string_protected($type) . '" onmouseover="$(this).find(\'span:first\').show();" onmouseout="$(this).find(\'span:first\').hide();"><span style="float: right; display: none;"><a href="#" onclick="$(this).parent().parent().slideFadeToggle();">' . osc_icon('minimize.png', 'Hide') . '</a></span>';

          foreach ( $messages as $message ) {
            $result .= '<p>' . osc_output_string_protected($message) . '</p>';
          }

          $result .= '</div>';
        }

        unset($this->_data[$group]);
      }

      return $result;
    }
  }
?>

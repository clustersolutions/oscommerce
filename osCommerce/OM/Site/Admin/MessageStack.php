<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Admin;

  use osCommerce\OM\OSCOM;

  class MessageStack extends \osCommerce\OM\MessageStack {
    public function get($group = null) {
      if ( empty($group) ) {
        $group = OSCOM::getSiteApplication();
      }

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

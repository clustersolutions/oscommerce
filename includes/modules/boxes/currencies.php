<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Boxes_currencies extends osC_Modules {
    var $_title = 'Currencies',
        $_code = 'currencies',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'boxes';

    function osC_Boxes_currencies() {
//      $this->_title = BOX_HEADING_CURRENCIES;
    }

    function initialize() {
      global $osC_Session, $osC_Currencies;

      $data = array();

      foreach ($osC_Currencies->currencies as $key => $value) {
        $data[] = array('id' => $key, 'text' => $value['title']);
      }

      if (sizeof($data) > 1) {
        $hidden_get_variables = '';

        foreach ($_GET as $key => $value) {
          if ( ($key != 'currency') && ($key != $osC_Session->getName()) && ($key != 'x') && ($key != 'y') ) {
            $hidden_get_variables .= osc_draw_hidden_field($key, $value);
          }
        }

        $this->_content = '<form name="currencies" action="' . tep_href_link(basename($_SERVER['PHP_SELF']), '', $request_type, false) . '" method="get">' . "\n" .
                          osc_draw_pull_down_menu('currency', $data, $_SESSION['currency'], 'onchange="this.form.submit();" style="width: 100%"') . $hidden_get_variables . tep_hide_session_id() . "\n" .
                          '</form>' . "\n";
      }
    }
  }
?>

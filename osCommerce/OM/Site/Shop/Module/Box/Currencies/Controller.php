<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Module\Box\Currencies;

  use osCommerce\OM\OSCOM;
  use osCommerce\OM\Registry;

  class Controller extends \osCommerce\OM\Modules {
    var $_title,
        $_code = 'Currencies',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Box';

    public function __construct() {
      $this->_title = OSCOM::getDef('box_currencies_heading');
    }

    public function initialize() {
      $OSCOM_Currencies = Registry::get('Currencies');

      $data = array();

      foreach ( $OSCOM_Currencies->getData() as $key => $value ) {
        $data[] = array('id' => $key,
                        'text' => $value['title']);
      }

      if ( sizeof($data) > 1 ) {
        $hidden_get_variables = '';

        foreach ( $_GET as $key => $value ) {
          if ( ($key != 'currency') && ($key != Registry::get('Session')->getName()) && ($key != 'x') && ($key != 'y') ) {
            $hidden_get_variables .= osc_draw_hidden_field($key, $value);
          }
        }

        $this->_content = '<form name="currencies" action="' . OSCOM::getLink(null, null, null, 'AUTO', false) . '" method="get">' .
                          $hidden_get_variables .
                          osc_draw_pull_down_menu('currency', $data, $_SESSION['currency'], 'onchange="this.form.submit();" style="width: 100%"') .
                          osc_draw_hidden_session_id_field() .
                          '</form>';
      }
    }
  }
?>

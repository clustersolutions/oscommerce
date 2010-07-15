<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Module\Box\Templates;

  use osCommerce\OM\OSCOM;
  use osCommerce\OM\Template;
  use osCommerce\OM\Registry;

  class Controller extends \osCommerce\OM\Modules {
    var $_title,
        $_code = 'Templates',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Box';

    public function __construct() {
      $this->_title = OSCOM::getDef('box_templates_heading');
    }

    public function initialize() {
      $OSCOM_Session = Registry::get('Session');

      $data = array();

      foreach ( Template::getTemplates() as $template ) {
        $data[] = array('id' => $template['code'],
                        'text' => $template['title']);
      }

      if ( count($data) > 1 ) {
        $hidden_get_variables = '';

        foreach ( $_GET as $key => $value ) {
          if ( ($key != 'template') && ($key != $OSCOM_Session->getName()) && ($key != 'x') && ($key != 'y') ) {
            $hidden_get_variables .= osc_draw_hidden_field($key, $value);
          }
        }

        $this->_content = '<form name="templates" action="' . OSCOM::getLink(null, null, null, 'AUTO', false) . '" method="get">' .
                          $hidden_get_variables . osc_draw_pull_down_menu('template', $data, $_SESSION['template']['code'], 'onchange="this.form.submit();" style="width: 100%"') . osc_draw_hidden_session_id_field() .
                          '</form>';
      }
    }
  }
?>

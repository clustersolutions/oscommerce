<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Module\Box\Templates;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Template;

  class Controller extends \osCommerce\OM\Core\Modules {
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
        $hidden_get_params = '';

        foreach ( $_GET as $key => $value ) {
          if ( ($key != 'template') && ($key != $OSCOM_Session->getName()) && ($key != 'x') && ($key != 'y') ) {
            $hidden_get_params .= HTML::hiddenField($key, $value);
          }
        }

        $this->_content = '<form name="templates" action="' . OSCOM::getLink(null, null, null, 'AUTO', false) . '" method="get">' .
                          $hidden_get_params . HTML::selectMenu('template', $data, $_SESSION['template']['code'], 'onchange="this.form.submit();" style="width: 100%"') . HTML::hiddenSessionIDField() .
                          '</form>';
      }
    }
  }
?>

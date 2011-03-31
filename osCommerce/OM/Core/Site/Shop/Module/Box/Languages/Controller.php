<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Module\Box\Languages;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Controller extends \osCommerce\OM\Core\Modules {
    var $_title,
        $_code = 'Languages',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Box';

    public function __construct() {
      $this->_title = OSCOM::getDef('box_languages_heading');
    }

    function initialize() {
      $OSCOM_Language = Registry::get('Language');

      $this->_content = '';

      $get_params = array();

      foreach ( $_GET as $key => $value ) {
        if ( ($key != 'language') && ($key != Registry::get('Session')->getName()) && ($key != 'x') && ($key != 'y') ) {
          $get_params[] = $key . '=' . $value;
        }
      }

      $get_params = implode($get_params, '&');

      if ( !empty($get_params) ) {
        $get_params .= '&';
      }

      foreach ( $OSCOM_Language->getAll() as $value ) {
        $this->_content .= ' ' . HTML::link(OSCOM::getLink(null, null, $get_params . 'language=' . $value['code'], 'AUTO'), $OSCOM_Language->showImage($value['code'])) . ' ';
      }
    }
  }
?>

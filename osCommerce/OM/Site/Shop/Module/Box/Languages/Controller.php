<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Module\Box\Languages;

  use osCommerce\OM\OSCOM;
  use osCommerce\OM\Registry;

  class Controller extends \osCommerce\OM\Modules {
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

      foreach ( $OSCOM_Language->getAll() as $value ) {
        $this->_content .= ' ' . osc_link_object(OSCOM::getLink(null, null, osc_get_all_get_params(array('language', 'currency')) . '&language=' . $value['code'], 'AUTO'), $OSCOM_Language->showImage($value['code'])) . ' ';
      }
    }
  }
?>

<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Module\Box\Information;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;

  class Controller extends \osCommerce\OM\Core\Modules {
    var $_title,
        $_code = 'Information',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Box';

    public function __construct() {
      $this->_title = OSCOM::getDef('box_information_heading');
    }

    function initialize() {
      $this->_title_link = OSCOM::getLink(null, 'Info');

      $this->_content = '<ol style="list-style: none; margin: 0; padding: 0;">' .
                        '  <li>' . HTML::link(OSCOM::getLink(null, 'Info', 'Shipping'), OSCOM::getDef('box_information_shipping')) . '</li>' .
                        '  <li>' . HTML::link(OSCOM::getLink(null, 'Info', 'Privacy'), OSCOM::getDef('box_information_privacy')) . '</li>' .
                        '  <li>' . HTML::link(OSCOM::getLink(null, 'Info', 'Conditions'), OSCOM::getDef('box_information_conditions')) . '</li>' .
                        '  <li>' . HTML::link(OSCOM::getLink(null, 'Info', 'Contact'), OSCOM::getDef('box_information_contact')) . '</li>' .
                        '  <li>' . HTML::link(OSCOM::getLink(null, 'Info', 'Sitemap'), OSCOM::getDef('box_information_sitemap')) . '</li>' .
                        '</ol>';
    }
  }
?>

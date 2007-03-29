<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Boxes_information extends osC_Modules {
    var $_title,
        $_code = 'information',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'boxes';

    function osC_Boxes_information() {
      global $osC_Language;

      $this->_title = $osC_Language->get('box_information_heading');
    }

    function initialize() {
      global $osC_Language;

      $this->_title_link = osc_href_link(FILENAME_INFO);

      $this->_content = '<ol style="list-style: none; margin: 0; padding: 0;">' .
                        '  <li>' . osc_link_object(osc_href_link(FILENAME_INFO, 'shipping'), $osC_Language->get('box_information_shipping')) . '</li>' .
                        '  <li>' . osc_link_object(osc_href_link(FILENAME_INFO, 'privacy'), $osC_Language->get('box_information_privacy')) . '</li>' .
                        '  <li>' . osc_link_object(osc_href_link(FILENAME_INFO, 'conditions'), $osC_Language->get('box_information_conditions')) . '</li>' .
                        '  <li>' . osc_link_object(osc_href_link(FILENAME_INFO, 'contact'), $osC_Language->get('box_information_contact')) . '</li>' .
                        '  <li>' . osc_link_object(osc_href_link(FILENAME_INFO, 'sitemap'), $osC_Language->get('box_information_sitemap')) . '</li>' .
                        '</ol>';
    }
  }
?>

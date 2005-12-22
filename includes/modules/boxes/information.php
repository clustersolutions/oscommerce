<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Boxes_information extends osC_Modules {
    var $_title = 'Information',
        $_code = 'information',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'boxes';

    function osC_Boxes_information() {
//      $this->_title = BOX_HEADING_INFORMATION;
      $this->_title_link = tep_href_link(FILENAME_INFO);
    }

    function initialize() {
      $this->_content = '<a href="' . tep_href_link(FILENAME_INFO, 'shipping') . '">' . BOX_INFORMATION_SHIPPING . '</a><br />' .
                        '<a href="' . tep_href_link(FILENAME_INFO, 'privacy') . '">' . BOX_INFORMATION_PRIVACY . '</a><br />' .
                        '<a href="' . tep_href_link(FILENAME_INFO, 'conditions') . '">' . BOX_INFORMATION_CONDITIONS . '</a><br />' .
                        '<a href="' . tep_href_link(FILENAME_INFO, 'contact') . '">' . BOX_INFORMATION_CONTACT . '</a><br />' .
                        '<a href="' . tep_href_link(FILENAME_INFO, 'sitemap') . '">' . BOX_INFORMATION_SITEMAP . '</a>';
    }
  }
?>

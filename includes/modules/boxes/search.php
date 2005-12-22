<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Boxes_search extends osC_Modules {
    var $_title = 'Search',
        $_code = 'search',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'boxes';

    function osC_Boxes_search() {
//      $this->_title = BOX_HEADING_SEARCH;
      $this->_title_link = tep_href_link(FILENAME_SEARCH);
    }

    function initialize() {
      $this->_content = '<form name="search" action="' . tep_href_link(FILENAME_SEARCH, '', 'NONSSL', false) . '" method="get">' . "\n" .
                        osc_draw_input_field('keywords', '', 'style="width: 80%;" maxlength="30"') . '&nbsp;' . tep_hide_session_id() . tep_image_submit('button_quick_find.gif', BOX_HEADING_SEARCH) . '<br />' . BOX_SEARCH_TEXT . "\n" .
                        '</form>' . "\n";
    }
  }
?>

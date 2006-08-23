<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Boxes_search extends osC_Modules {
    var $_title,
        $_code = 'search',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'boxes';

    function osC_Boxes_search() {
      global $osC_Language;

      $this->_title = $osC_Language->get('box_search_heading');
      $this->_title_link = osc_href_link(FILENAME_SEARCH);
    }

    function initialize() {
      global $osC_Language;

      $this->_content = '<form name="search" action="' . osc_href_link(FILENAME_SEARCH, null, 'NONSSL', false) . '" method="get">' .
                        osc_draw_input_field('keywords', null, 'style="width: 80%;" maxlength="30"') . '&nbsp;' . osc_draw_hidden_session_id_field() . osc_draw_image_submit_button('button_quick_find.gif', $osC_Language->get('box_search_heading')) . '<br />' . sprintf($osC_Language->get('box_search_text'), osc_href_link(FILENAME_SEARCH)) .
                        '</form>';
    }
  }
?>

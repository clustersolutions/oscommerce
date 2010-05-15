<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Module\Box\Search;

  use osCommerce\OM\OSCOM;

  class Controller extends \osCommerce\OM\Modules {
    var $_title,
        $_code = 'Search',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Box';

    public function __construct() {
      $this->_title = OSCOM::getDef('box_search_heading');
    }

    function initialize() {
      $this->_title_link = OSCOM::getLink(null, 'Search');

      $this->_content = '<form name="search" action="' . OSCOM::getLink(null, '') . '" method="get">' . osc_draw_hidden_field('Search', null) .
                        osc_draw_input_field('keywords', null, 'style="width: 80%;" maxlength="30"') . '&nbsp;' . osc_draw_hidden_session_id_field() . osc_draw_image_submit_button('button_quick_find.gif', OSCOM::getDef('box_search_heading')) . '<br />' . sprintf(OSCOM::getDef('box_search_text'), OSCOM::getLink(null, 'Search')) .
                        '</form>';
    }
  }
?>

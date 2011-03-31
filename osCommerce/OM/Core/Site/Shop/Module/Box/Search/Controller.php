<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Module\Box\Search;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;

  class Controller extends \osCommerce\OM\Core\Modules {
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

      $this->_content = '<form name="search" action="' . OSCOM::getLink() . '" method="get">' . HTML::hiddenField('Search', null) .
                        HTML::inputField('Q', null, 'style="width: 80%;" maxlength="30"') . '&nbsp;' . HTML::hiddenSessionIDField() . HTML::imageSubmit('button_quick_find.gif', OSCOM::getDef('box_search_heading')) . '<br />' . sprintf(OSCOM::getDef('box_search_text'), OSCOM::getLink(null, 'Search')) .
                        '</form>';
    }
  }
?>

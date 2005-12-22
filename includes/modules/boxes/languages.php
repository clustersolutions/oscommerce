<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Boxes_languages extends osC_Modules {
    var $_title = 'Languages',
        $_code = 'languages',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'boxes';

    function osC_Boxes_languages() {
//      $this->_title = BOX_HEADING_LANGUAGES;
    }

    function initialize() {
      global $osC_Language, $request_type;

      $data = '';

      foreach ($osC_Language->getAll() as $language) {
        $data .= ' <a href="' . tep_href_link(basename($_SERVER['PHP_SELF']), tep_get_all_get_params(array('language', 'currency')) . 'language=' . $language['code'], $request_type) . '">' . tep_image('includes/languages/' .  $language['directory'] . '/images/' . $language['image'], $language['name']) . '</a> ';
      }

      if (empty($data) === false) {
        $this->_content = $data;
      }
    }
  }
?>

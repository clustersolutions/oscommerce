<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Boxes_languages extends osC_Modules {
    var $_title,
        $_code = 'languages',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'boxes';

    function osC_Boxes_languages() {
      global $osC_Language;

      $this->_title = $osC_Language->get('box_languages_heading');
    }

    function initialize() {
      global $osC_Language, $request_type;

      $data = '';

      foreach ($osC_Language->getAll() as $key => $value) {
        $data .= ' <a href="' . tep_href_link(basename($_SERVER['PHP_SELF']), tep_get_all_get_params(array('language', 'currency')) . 'language=' . $key, $request_type) . '">' . tep_image('includes/languages/' .  $key . '/images/' . $value['image'], $value['name']) . '</a> ';
      }

      if (empty($data) === false) {
        $this->_content = $data;
      }
    }
  }
?>

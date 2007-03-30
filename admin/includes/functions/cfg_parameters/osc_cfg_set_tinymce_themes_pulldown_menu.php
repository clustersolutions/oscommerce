<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  function osc_cfg_set_tinymce_themes_pulldown_menu($default, $key = null) {
    $name = (!empty($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $themes_array = array();

    foreach (osC_TinyMCE::getThemes() as $themefolder) {
      $themes_array[] = array('id' => $themefolder['themeid'],
                              'text' => $themefolder['themename']);
    }

    return osc_draw_pull_down_menu($name, $themes_array, $default);
  }
?>

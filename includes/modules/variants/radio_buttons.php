<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Variants_radio_buttons extends osC_Variants_Abstract {
    const ALLOW_MULTIPLE_VALUES = false;
    const HAS_CUSTOM_VALUE = false;

    static public function parse($data) {
      $default_value = null;

      foreach ( $data['data'] as $variant ) {
        if ( $variant['default'] === true ) {
          $default_value = (string)$variant['id'];

          break;
        }
      }

      $string = '<table border="0" cellspacing="0" cellpadding="2">' .
                '  <tr>' .
                '    <td width="100">' . $data['title'] . ':</td>' .
                '    <td>' . osc_draw_radio_field('variants[' . $data['group_id'] . ']', $data['data'], $default_value, 'onchange="refreshVariants();" id="variants_' . $data['group_id'] . '"') . '</td>' .
                '  </tr>' .
                '</table>';

      return $string;
    }

    static public function allowsMultipleValues() {
      return self::ALLOW_MULTIPLE_VALUES;
    }

    static public function hasCustomValue() {
      return self::HAS_CUSTOM_VALUE;
    }
  }
?>

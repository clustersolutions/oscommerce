<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Module\ProductVariant;

  class TextField implements \osCommerce\OM\Core\Site\Shop\ProductVariantInterface {
    const ALLOW_MULTIPLE_VALUES = true;
    const HAS_CUSTOM_VALUE = true;

    public static function parse($data) {
      $string = '<table border="0" cellspacing="0" cellpadding="2">';

      $i = 0;

      foreach ( $data['data'] as $field ) {
        $i++;

        $string .= '  <tr>' .
                   '    <td width="100">' . $field['text'] . ':</td>' .
                   '    <td>' . osc_draw_input_field('variants[' . $data['group_id'] . '][' . $field['id'] . ']', null, 'id="variants_' . $data['group_id'] . '_' . $i . '"') . '</td>' .
                   '  </tr>';
      }

      $string .= '</table>';

      return $string;
    }

    public static function getGroupTitle($data) {
      return $data['value_title'];
    }

    public static function getValueTitle($data) {
      return $_POST['variants'][$data['group_id']][$data['value_id']];
    }

    public static function allowsMultipleValues() {
      return self::ALLOW_MULTIPLE_VALUES;
    }

    public static function hasCustomValue() {
      return self::HAS_CUSTOM_VALUE;
    }
  }
?>

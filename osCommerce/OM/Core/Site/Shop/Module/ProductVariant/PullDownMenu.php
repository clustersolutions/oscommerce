<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Module\ProductVariant;

  use osCommerce\OM\Core\HTML;

  class PullDownMenu implements \osCommerce\OM\Core\Site\Shop\ProductVariantInterface {
    const ALLOW_MULTIPLE_VALUES = false;
    const HAS_CUSTOM_VALUE = false;

    public static function parse($data) {
      $default_value = null;

      foreach ( $data['data'] as $variant ) {
        if ( $variant['default'] === true ) {
          $default_value = $variant['id'];

          break;
        }
      }

      $string = '<table border="0" cellspacing="0" cellpadding="2">' .
                '  <tr>' .
                '    <td width="100">' . $data['title'] . ':</td>' .
                '    <td>' . HTML::selectMenu('variants[' . $data['group_id'] . ']', $data['data'], $default_value, 'onchange="refreshVariants();" id="variants_' . $data['group_id'] . '"') . '</td>' .
                '  </tr>' .
                '</table>';

      return $string;
    }

    public static function allowsMultipleValues() {
      return self::ALLOW_MULTIPLE_VALUES;
    }

    public static function hasCustomValue() {
      return self::HAS_CUSTOM_VALUE;
    }

    public static function getGroupTitle($data) {
      return $data['group_title'];
    }

    public static function getValueTitle($data) {
      return $data['value_title'];
    }
  }
?>

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

  abstract class osC_Variants_Abstract {
    abstract static public function parse($data);
    abstract static public function allowsMultipleValues();
    abstract static public function hasCustomValue();

    static public function getGroupTitle($data) {
      return $data['group_title'];
    }

    static public function getValueTitle($data) {
      return $data['value_title'];
    }
  }

  class osC_Variants {
    static public function parse($module, $data) {
      if ( !class_exists('osC_Variants_' . $module) ) {
        if ( file_exists(DIR_FS_CATALOG . 'includes/modules/variants/' . basename($module) . '.php') ) {
          include(DIR_FS_CATALOG . 'includes/modules/variants/' . basename($module) . '.php');
        }
      }

      if ( class_exists('osC_Variants_' . $module) ) {
        return call_user_func(array('osC_Variants_' . $module, 'parse'), $data);
      }
    }

    static public function getGroupTitle($module, $data) {
      if ( !class_exists('osC_Variants_' . $module) ) {
        if ( file_exists(DIR_FS_CATALOG . 'includes/modules/variants/' . basename($module) . '.php') ) {
          include(DIR_FS_CATALOG . 'includes/modules/variants/' . basename($module) . '.php');
        }
      }

      if ( class_exists('osC_Variants_' . $module) ) {
        return call_user_func(array('osC_Variants_' . $module, 'getGroupTitle'), $data);
      }

      return $data['group_title'];
    }

    static public function getValueTitle($module, $data) {
      if ( !class_exists('osC_Variants_' . $module) ) {
        if ( file_exists(DIR_FS_CATALOG . 'includes/modules/variants/' . basename($module) . '.php') ) {
          include(DIR_FS_CATALOG . 'includes/modules/variants/' . basename($module) . '.php');
        }
      }

      if ( class_exists('osC_Variants_' . $module) ) {
        return call_user_func(array('osC_Variants_' . $module, 'getValueTitle'), $data);
      }

      return $data['value_title'];
    }

    static public function allowsMultipleValues($module) {
      if ( !class_exists('osC_Variants_' . $module) ) {
        if ( file_exists(DIR_FS_CATALOG . 'includes/modules/variants/' . basename($module) . '.php') ) {
          include(DIR_FS_CATALOG . 'includes/modules/variants/' . basename($module) . '.php');
        }
      }

      if ( class_exists('osC_Variants_' . $module) ) {
        return call_user_func(array('osC_Variants_' . $module, 'allowsMultipleValues'));
      }

      return false;
    }

    static public function hasCustomValue($module) {
      if ( !class_exists('osC_Variants_' . $module) ) {
        if ( file_exists(DIR_FS_CATALOG . 'includes/modules/variants/' . basename($module) . '.php') ) {
          include(DIR_FS_CATALOG . 'includes/modules/variants/' . basename($module) . '.php');
        }
      }

      if ( class_exists('osC_Variants_' . $module) ) {
        return call_user_func(array('osC_Variants_' . $module, 'hasCustomValue'));
      }

      return false;
    }

    static public function defineJavascript($products) {
      global $osC_Currencies;

      $string = '<script language="javascript" type="text/javascript">var combos = new Array();' . "\n";

      foreach ( $products as $product_id => $product ) {
        $string .= 'combos[' . $product_id . '] = new Array();' . "\n" .
                   'combos[' . $product_id . '] = { price: "' . addslashes($osC_Currencies->displayPrice($product['data']['price'], $product['data']['tax_class_id'])) . '", model: "' . addslashes($product['data']['model']) . '", availability_shipping: ' . (int)$product['data']['availability_shipping'] . ', values: [] };' . "\n";

        foreach ( $product['values'] as $group_id => $variants ) {
          $check_flag = false;

          foreach ( $variants as $variant ) {
            if ( !osC_Variants::hasCustomValue($variant['module']) ) {
              if ( $check_flag === false ) {
                $check_flag = true;

                $string .= 'combos[' . $product_id . ']["values"][' . $group_id . '] = new Array();' . "\n";
              }

              $string .= 'combos[' . $product_id . ']["values"][' . $group_id . '][' . $variant['value_id'] . '] = ' . $variant['value_id'] . ';' . "\n";
            }
          }
        }
      }

      $string .= '</script>';

      return $string;
    }
  }
?>

<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Module\Service;

  use osCommerce\OM\Registry;
  use osCommerce\OM\Site\Shop\CategoryTree;

  class CategoryPath implements \osCommerce\OM\Site\Shop\ServiceInterface {
    public static function start() {
      self::process();

      Registry::set('CategoryTree', new CategoryTree());

      return true;
    }

    public static function process($id = null) {
      global $cPath, $cPath_array, $current_category_id;

      $cPath = '';
      $cPath_array = array();
      $current_category_id = 0;

      if ( isset($_GET['cPath']) ) {
        $cPath = $_GET['cPath'];
      } elseif ( isset($id) ) {
        $cPath = Registry::get('CategoryTree')->buildBreadcrumb($id);
      }

      if ( !empty($cPath) ) {
        $cPath_array = array_unique(array_filter(explode('_', $cPath), 'is_numeric'));
        $cPath = implode('_', $cPath_array);
        $current_category_id = end($cPath_array);
      }
    }

    public static function stop() {
      return true;
    }
  }
?>

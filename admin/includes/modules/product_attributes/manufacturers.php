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

  class osC_ProductAttributes_manufacturers extends osC_ProductAttributes_Admin {
    public function setFunction($value) {
      global $osC_Database, $osC_Language;

      $string = '';

      $Qmanufacturers = $osC_Database->query('select manufacturers_id, manufacturers_name from :table_manufacturers order by manufacturers_name');
      $Qmanufacturers->bindTable(':table_manufacturers');
      $Qmanufacturers->execute();

      $array = array(array('id' => '',
                           'text' => $osC_Language->get('none')));

      while ( $Qmanufacturers->next() ) {
        $array[] = array('id' => $Qmanufacturers->valueInt('manufacturers_id'),
                         'text' => $Qmanufacturers->value('manufacturers_name'));
      }

      if ( !empty($array) ) {
        $string = osc_draw_pull_down_menu('attributes[' . self::getID() . ']', $array, $value);
      }

      return $string;
    }
  }
?>

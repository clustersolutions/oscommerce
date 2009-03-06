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

  class osC_ProductAttributes_shipping_availability extends osC_ProductAttributes_Admin {
    public function setFunction($value) {
      global $osC_Database, $osC_Language;

      $string = '';

      $Qstatus = $osC_Database->query('select id, title from :table_shipping_availability where languages_id = :languages_id order by title');
      $Qstatus->bindTable(':table_shipping_availability');
      $Qstatus->bindInt(':languages_id', $osC_Language->getID());
      $Qstatus->execute();

      $array = array();

      while ( $Qstatus->next() ) {
        $array[] = array('id' => $Qstatus->valueInt('id'),
                         'text' => $Qstatus->value('title'));
      }

      if ( !empty($array) ) {
        $string = osc_draw_pull_down_menu('attributes[' . self::getID() . ']', $array, $value);
      }

      return $string;
    }
  }
?>

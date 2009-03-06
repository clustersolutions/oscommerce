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

  class osC_ProductAttributes_shipping_availability {
    static public function getValue($value) {
      global $osC_Database, $osC_Language;

      $string = '';

      $Qstatus = $osC_Database->query('select title, css_key from :table_shipping_availability where id = :id and languages_id = :languages_id');
      $Qstatus->bindTable(':table_shipping_availability');
      $Qstatus->bindInt(':id', $value);
      $Qstatus->bindInt(':languages_id', $osC_Language->getID());
      $Qstatus->execute();

      if ( $Qstatus->numberOfRows() === 1 ) {
        $string = $Qstatus->value('title');

        if ( !osc_empty($Qstatus->value('css_key')) ) {
          $string = '<span class="' . $Qstatus->value('css_key') . '">' . $string . '</span>';
        }
      }

      return $string;
    }
  }
?>

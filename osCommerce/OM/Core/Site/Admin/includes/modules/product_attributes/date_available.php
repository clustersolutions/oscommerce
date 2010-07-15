<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_ProductAttributes_date_available extends osC_ProductAttributes_Admin {
    public function setFunction($value) {
      $string = osc_draw_input_field('attributes[' . self::getID() . ']', $value, 'id="attributes_' . self::getID() . '"') . '<script type="text/javascript">$(function() { $("#attributes_' . self::getID() . '").datepicker( { dateFormat: "yy-mm-dd", changeMonth: true, changeYear: true } ); });</script><small>(YYYY-MM-DD)</small>';

      return $string;
    }
  }
?>

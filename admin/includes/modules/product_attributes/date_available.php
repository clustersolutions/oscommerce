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

  class osC_ProductAttributes_date_available extends osC_ProductAttributes_Admin {
    public function setFunction($value) {
      $string = '<style type="text/css">@import url("external/jscalendar/calendar-win2k-1.css");</style>' .
                '<script type="text/javascript" src="external/jscalendar/calendar.js"></script>' .
                '<script type="text/javascript" src="external/jscalendar/lang/calendar-en.js"></script>' .
                '<script type="text/javascript" src="external/jscalendar/calendar-setup.js"></script>';

      $string .= osc_draw_input_field('attributes[' . self::getID() . ']', $value) . '<input type="button" value="..." id="calendarTrigger" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "attributes[' . self::getID() . ']", ifFormat: "%Y-%m-%d", button: "calendarTrigger" } );</script><small>(YYYY-MM-DD)</small>';

      return $string;
    }
  }
?>

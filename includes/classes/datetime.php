<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  class osC_DateTime {
    var $date = '',
        $month = '',
        $year = '',
        $hour = '',
        $minute = '',
        $second = '',
        $long_date_format = '',
        $short_date_format = '',
        $long_date = '',
        $short_date = '';

    function osC_DateTime($raw_date = '', $long_date_format = '', $short_date_format = '') {
      if (tep_not_null($long_date_format)) {
        $this->long_date_format = $long_date_format;
      } else {
        $this->long_date_format = DATE_FORMAT_LONG;
      }

      if (tep_not_null($short_date_format)) {
        $this->short_date_format = $short_date_format;
      } else {
        $this->short_date_format = DATE_FORMAT;
      }

      if (tep_not_null($raw_date)) {
        $this->format($raw_date);
      } else {
        $this->setCurrentDate();
      }
    }

    function setCurrentDate() {
      $this->date = date('d');
      $this->month = date('m');
      $this->year = date('Y');

      $this->hour = date('H');
      $this->minute = date('i');
      $this->second = date('s');
    }

    function format($raw_date) {
      if (empty($raw_date) || ($raw_date == '0000-00-00 00:00:00')) {
        return false;
      }

      $this->year = (int)substr($raw_date, 0, 4);
      $this->month = (int)substr($raw_date, 5, 2);
      $this->day = (int)substr($raw_date, 8, 2);
      $this->hour = (int)substr($raw_date, 11, 2);
      $this->minute = (int)substr($raw_date, 14, 2);
      $this->second = (int)substr($raw_date, 17, 2);

      $this->long_date = strftime($this->long_date_format, mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year));

      if (@date('Y', mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year)) == $this->year) {
        $this->short_date = date($this->short_date_format, mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year));
      } else {
        $this->short_date = ereg_replace('2037$', $this->year, date($this->short_date_format, mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, 2037)));
      }
    }

    function isLeapYear($year = '') {
      if (empty($year)) {
        $year = $this->year;
      }

      if ($year % 100 == 0) {
        if ($year % 400 == 0) {
          return true;
        }
      } else {
        if (($year % 4) == 0) {
          return true;
        }
      }

      return false;
    }

    function validate($date_to_check, $format_string, &$date_array) {
      $separator_idx = -1;

      $separators = array('-', ' ', '/', '.');
      $month_abbr = array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');
      $no_of_days = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

      $format_string = strtolower($format_string);

      if (strlen($date_to_check) != strlen($format_string)) {
        return false;
      }

      $size = sizeof($separators);
      for ($i=0; $i<$size; $i++) {
        $pos_separator = strpos($date_to_check, $separators[$i]);
        if ($pos_separator != false) {
          $date_separator_idx = $i;
          break;
        }
      }

      for ($i=0; $i<$size; $i++) {
        $pos_separator = strpos($format_string, $separators[$i]);
        if ($pos_separator != false) {
          $format_separator_idx = $i;
          break;
        }
      }

      if ($date_separator_idx != $format_separator_idx) {
        return false;
      }

      if ($date_separator_idx != -1) {
        $format_string_array = explode( $separators[$date_separator_idx], $format_string );
        if (sizeof($format_string_array) != 3) {
          return false;
        }

        $date_to_check_array = explode( $separators[$date_separator_idx], $date_to_check );
        if (sizeof($date_to_check_array) != 3) {
          return false;
        }

        $size = sizeof($format_string_array);
        for ($i=0; $i<$size; $i++) {
          if ($format_string_array[$i] == 'mm' || $format_string_array[$i] == 'mmm') $month = $date_to_check_array[$i];
          if ($format_string_array[$i] == 'dd') $day = $date_to_check_array[$i];
          if ( ($format_string_array[$i] == 'yyyy') || ($format_string_array[$i] == 'aaaa') ) $year = $date_to_check_array[$i];
        }
      } else {
        if (strlen($format_string) == 8 || strlen($format_string) == 9) {
          $pos_month = strpos($format_string, 'mmm');
          if ($pos_month != false) {
            $month = substr( $date_to_check, $pos_month, 3 );
            $size = sizeof($month_abbr);
            for ($i=0; $i<$size; $i++) {
              if ($month == $month_abbr[$i]) {
                $month = $i;
                break;
              }
            }
          } else {
            $month = substr($date_to_check, strpos($format_string, 'mm'), 2);
          }
        } else {
          return false;
        }

        $day = substr($date_to_check, strpos($format_string, 'dd'), 2);
        $year = substr($date_to_check, strpos($format_string, 'yyyy'), 4);
      }

      if (strlen($year) != 4) {
        return false;
      }

      if (!settype($year, 'integer') || !settype($month, 'integer') || !settype($day, 'integer')) {
        return false;
      }

      if ($month > 12 || $month < 1) {
        return false;
      }

      if ($day < 1) {
        return false;
      }

      if ($this->isLeapYear($year)) {
        $no_of_days[1] = 29;
      }

      if ($day > $no_of_days[$month - 1]) {
        return false;
      }

      $date_array = array($year, $month, $day);

      return true;
    }
  }
?>

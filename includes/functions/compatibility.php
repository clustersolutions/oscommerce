<?php
/*
  $Id: compatibility.php,v 1.24 2004/07/22 16:29:56 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  if (PHP_VERSION < 4.1) {
    if (isset($HTTP_SERVER_VARS)) $_SERVER =& $HTTP_SERVER_VARS;
    if (isset($HTTP_GET_VARS)) $_GET =& $HTTP_GET_VARS;
    if (isset($HTTP_POST_VARS)) $_POST =& $HTTP_POST_VARS;
    if (isset($HTTP_COOKIE_VARS)) $_COOKIE =& $HTTP_COOKIE_VARS;
    if (isset($HTTP_POST_FILES)) $_FILES =& $HTTP_POST_FILES;
    if (isset($HTTP_ENV_VARS)) $_ENV =& $HTTP_ENV_VARS;
  }

// Recursively handle magic_quotes_gpc turned off.
  function osc_remove_magic_quotes(&$array) {
    if (!is_array($array) || (sizeof($array) < 1)) {
      return false;
    }

    foreach ($array as $key => $value) {
      if (is_array($value)) {
        osc_remove_magic_quotes($array[$key]);
      } else {
        $array[$key] = stripslashes($value);
      }
    }
  }

// handle magic_quotes_gpc turned off.
  if (get_magic_quotes_gpc() > 0) {
    if (isset($_GET)) {
      osc_remove_magic_quotes($_GET);
    }

    if (isset($_POST)) {
      osc_remove_magic_quotes($_POST);
    }

    if (isset($_COOKIE)) {
      osc_remove_magic_quotes($_COOKIE);
    }
  }

  if (!function_exists('constant')) {
    function constant($constant) {
      eval("\$temp=$constant;");

      return $temp;
    }
  }

  if (!function_exists('checkdnsrr')) {
    function checkdnsrr($host, $type) {
      if(tep_not_null($host) && tep_not_null($type)) {
        @exec("nslookup -type=$type $host", $output);
        while(list($k, $line) = each($output)) {
          if(eregi("^$host", $line)) {
            return true;
          }
        }
      }
      return false;
    }
  }

  if (!function_exists('array_unique')) {
    function array_unique($array) {
      $tmp_array = array();

      for ($i=0, $n=sizeof($array); $i<$n; $i++) {
        if (!in_array($array[$i], $tmp_array)) {
          $tmp_array[] = $array[$i];
        }
      }

      return $tmp_array;
    }
  }

  if (!function_exists('array_search')) {
    function array_search($needle, $haystack) {
      $match = false;

      foreach ($haystack as $key => $value) {
        if ($value == $needle) {
          $match = $key;
          break;
        }
      }

      return $match;
    }
  }
?>

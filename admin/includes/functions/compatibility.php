<?php
/*
  $Id: compatibility.php,v 1.14 2004/11/20 02:08:20 hpdl Exp $

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

// unset variables in the global scope if register_globals is enabled
  if (ini_get('register_globals')) {
    $superglobals = array($_SERVER, $_ENV, $_FILES, $_COOKIE, $_POST, $_GET);

    if (isset($_SESSION)) {
      array_unshift($superglobals, $_SESSION);
    }

    foreach ($superglobals as $superglobal) {
      foreach ($superglobal as $name => $global) {
        unset($GLOBALS[$name]);
      }
    }

    ini_set('register_globals', false);
  }

// remove slashes from variables if magic_quotes is enabled
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

  if (!function_exists('is_uploaded_file')) {
    function is_uploaded_file($filename) {
      if (!$tmp_file = get_cfg_var('upload_tmp_dir')) {
        $tmp_file = dirname(tempnam('', ''));
      }

      if (strchr($tmp_file, '/')) {
        if (substr($tmp_file, -1) != '/') $tmp_file .= '/';
      } elseif (strchr($tmp_file, '\\')) {
        if (substr($tmp_file, -1) != '\\') $tmp_file .= '\\';
      }

      return file_exists($tmp_file . basename($filename));
    }
  }

  if (!function_exists('move_uploaded_file')) {
    function move_uploaded_file($file, $target) {
      return copy($file, $target);
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

  if (!function_exists('array_map')) {
    function array_map($callback, $array) {
      if (is_array($array)) {
        $_new_array = array();
        reset($array);
        while (list($key, $value) = each($array)) {
          $_new_array[$key] = array_map($callback, $array[$key]);
        }
        return $_new_array;
      } else {
        return $callback($array);
      }
    }
  }

  if (!function_exists('file_get_contents')) {
    function file_get_contents($filename) {
      if ($handle = @fopen($filename, 'rb')) {
        $data = fread($handle, filesize($filename));
        fclose($fh);

        return $data;
      } else {
        return false;
      }
    }
  }

  if (!function_exists('constant')) {
    function constant($constant) {
      eval("\$temp=$constant;");

      return $temp;
    }
  }

  if (!function_exists('posix_getpwuid')) {
    function posix_getpwuid($id) {
      return '-?-';
    }
  }

  if (!function_exists('posix_getgrgid')) {
    function posix_getgrgid($id) {
      return '-?-';
    }
  }
?>

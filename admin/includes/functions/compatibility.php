<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

// Based from work by Richard Heyes (http://www.phpguru.org)
  if ((int)ini_get('register_globals') > 0) {
    if (isset($_REQUEST['GLOBALS'])) {
      die('GLOBALS overwrite attempt detected');
    }

// variables that shouldn't be unset
    $noUnset = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');

    $input = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset($_SESSION) ? (array)$_SESSION : array());

    foreach ($input as $k => $v) {
      if (!in_array($k, $noUnset) && isset($GLOBALS[$k])) {
        unset($GLOBALS[$k]);
      }
    }

    unset($noUnset);
    unset($input);
    unset($k);
    unset($v);
  }

// Based from work by Ilia Alshanetsky (Advanced PHP Security)
  if ((int)get_magic_quotes_gpc() > 0) {
    $in = array(&$_GET, &$_POST, &$_COOKIE);

    while (list($k,$v) = each($in)) {
      foreach ($v as $key => $val) {
        if (!is_array($val)) {
          $in[$k][$key] = stripslashes($val);

          continue;
        }

        $in[] =& $in[$k][$key];
      }
    }

    unset($in);
    unset($k);
    unset($v);
    unset($key);
    unset($val);
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

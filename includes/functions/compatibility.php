<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

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

  if (!function_exists('ctype_alnum')) {
    function ctype_alnum($string) {
      return (eregi('^[a-z0-9]*$', $string) > 0);
    }
  }

  if (!function_exists('ctype_xdigit')) {
    function ctype_xdigit($string) {
      return (eregi('^([a-f0-9][a-f0-9])*$', $string) > 0);
    }
  }

  if (!function_exists('is_a')) {
    function is_a($object, $class) {
      if (!is_object($object)) {
        return false;
      }

      if (get_class($object) == strtolower($class)) {
        return true;
      } else {
        return is_subclass_of($object, $class);
      }
    }
  }

  if (!function_exists('floatval')) {
    function floatval($float) {
      return doubleval($float);
    }
  }

  if (!function_exists('stream_get_contents')) {
    function stream_get_contents($resource) {
      $result = '';

      if (is_resource($resource)) {
        while (!feof($resource)) {
          $result .= @fread($resource, 2048);
        }
      }

      return $result;
    }
  }

  if (!function_exists('sha1')) {
    function sha1($source) {
      if (function_exists('mhash')) {
        if (($hash = @mhash(MHASH_SHA1, $source)) !== false) {
          return bin2hex($hash);
        }
      }

      if (!function_exists('calc_sha1')) {
        include('ext/sha1/sha1.php');
      }

      return calc_sha1($source);
    }
  }
?>

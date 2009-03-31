<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

/**
 * Set the error reporting level to E_ALL and log all errors
 */

  if ( defined('DIR_FS_WORK') ) {
    error_reporting(E_ALL);
    ini_set('log_errors', true);
    ini_set('error_log', DIR_FS_WORK . 'oscommerce_errors.log');
  }

/**
 * Forcefully disable register_globals if enabled
 *
 * Based from work by Richard Heyes (http://www.phpguru.org)
 */

  if ( (int)ini_get('register_globals') > 0 ) {
    if ( isset($_REQUEST['GLOBALS']) ) {
      die('GLOBALS overwrite attempt detected');
    }

    $noUnset = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');

    $input = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset($_SESSION) ? (array)$_SESSION : array());

    foreach ( $input as $k => $v ) {
      if ( !in_array($k, $noUnset) && isset($GLOBALS[$k]) ) {
        unset($GLOBALS[$k]);
      }
    }

    unset($noUnset);
    unset($input);
    unset($k);
    unset($v);
  }

/**
 * Forcefully disable magic_quotes_gpc if enabled
 *
 * Based from work by Ilia Alshanetsky (Advanced PHP Security)
 */

  if ( (int)get_magic_quotes_gpc() > 0 ) {
    $in = array(&$_GET, &$_POST, &$_COOKIE);

    foreach ( $in as $k => $v ) {
      foreach ( $v as $key => $val ) {
        if ( !is_array($val) ) {
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

/**
 * checkdnsrr() not implemented on Microsoft Windows platforms in PHP < 5.3.0
 */

  if ( !function_exists('checkdnsrr') ) {
    function checkdnsrr($host, $type) {
      if ( !empty($host) && !empty($type) ) {
        @exec('nslookup -type=' . escapeshellarg($type) . ' ' . escapeshellarg($host), $output);

        foreach ( $output as $k => $line ) {
          if ( eregi('^' . $host, $line) ) {
            return true;
          }
        }
      }

      return false;
    }
  }

/*
 * posix_getpwuid() not implemented on Microsoft Windows platforms
 */

  if ( !function_exists('posix_getpwuid') ) {
    function posix_getpwuid($id) {
      return '-?-';
    }
  }

/*
 * posix_getgrgid() not implemented on Microsoft Windows platforms
 */

  if ( !function_exists('posix_getgrgid') ) {
    function posix_getgrgid($id) {
      return '-?-';
    }
  }

/*
 * imagetypes() is only available when GD is configured with PHP
 */

  if ( !function_exists('imagetypes') ) {
    define('IMG_JPG', false);
    define('IMG_GIF', false);
    define('IMG_PNG', false);

    function imagetypes() {
      return false;
    }
  }
?>

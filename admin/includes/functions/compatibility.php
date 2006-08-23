<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

/*
 * file_get_contents() natively supported from PHP 4.3
 */

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

/*
 * posix_getpwuid() not implemented on Microsoft Windows platforms
 */

  if (!function_exists('posix_getpwuid')) {
    function posix_getpwuid($id) {
      return '-?-';
    }
  }

/*
 * posix_getgrgid() not implemented on Microsoft Windows platforms
 */

  if (!function_exists('posix_getgrgid')) {
    function posix_getgrgid($id) {
      return '-?-';
    }
  }
?>

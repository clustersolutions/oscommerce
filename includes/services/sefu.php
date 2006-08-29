<?php
/*
  $Id:sefu.php 293 2005-11-29 17:34:26Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_sefu {
    var $title = 'Search Engine Friendly URLs',
        $description = 'Search engine friendly urls.',
        $uninstallable = true,
        $depends,
        $precedes = 'session';

    function start() {
      if (isset($_SERVER['ORIG_PATH_INFO'])) {
        if (isset($_SERVER['PATH_INFO']) && empty($_SERVER['PATH_INFO'])) {
          $_SERVER['PATH_INFO'] = $_SERVER['ORIG_PATH_INFO'];
        }
      }

      if (isset($_SERVER['PATH_INFO']) && (strlen($_SERVER['PATH_INFO']) > 1)) {
        $parameters = explode('/', substr($_SERVER['PATH_INFO'], 1));

        $_GET = array();
        $GET_array = array();

        foreach ($parameters as $parameter) {
          $param_array = explode(',', $parameter, 2);

          if (!isset($param_array[1])) {
            $param_array[1] = '';
          }

          if (strpos($param_array[0], '[]') !== false) {
            $GET_array[substr($param_array[0], 0, -2)][] = $param_array[1];
          } else {
            $_GET[$param_array[0]] = $param_array[1];
          }

          $i++;
        }

        if (sizeof($GET_array) > 0) {
          foreach ($GET_array as $key => $value) {
            $_GET[$key] = $value;
          }
        }
      }

      return true;
    }

    function stop() {
      return true;
    }

    function install() {
      return false;
    }

    function remove() {
      return false;
    }

    function keys() {
      return false;
    }
  }
?>

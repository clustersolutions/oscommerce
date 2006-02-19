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
      if (isset($_SERVER['PATH_INFO']) && (strlen($_SERVER['PATH_INFO']) > 1)) {
        $_SERVER['PHP_SELF'] = str_replace($_SERVER['PATH_INFO'], '', $_SERVER['PHP_SELF']);

        $parameters = explode('/', substr($_SERVER['PATH_INFO'], 1));

        $GET_array = array();

        for ($i=0, $n=sizeof($parameters); $i<$n; $i++) {
          if (!isset($parameters[$i+1])) $parameters[$i+1] = '';

          if (strpos($parameters[$i], '[]')) {
            $GET_array[substr($parameters[$i], 0, -2)][] = $parameters[$i+1];
          } else {
            $_GET[$parameters[$i]] = $parameters[$i+1];
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

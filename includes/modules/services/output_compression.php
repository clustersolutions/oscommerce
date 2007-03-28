<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_output_compression {
    function start() {
      if (extension_loaded('zlib')) {
        if ((int)ini_get('zlib.output_compression') < 1) {
          ini_set('zlib.output_compression_level', SERVICE_OUTPUT_COMPRESSION_GZIP_LEVEL);
          ob_start('ob_gzhandler');

          return false; // no call to stop() is needed
        }
      }

      return false;
    }

    function stop() {
      return true;
    }
  }
?>

<?php
/*
  $Id:output_compression.php 293 2005-11-29 17:34:26Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_output_compression {
    var $title = 'Output Compression',
        $description = 'Compress the content with GZIP.',
        $uninstallable = true,
        $depends,
        $precedes = 'session';

    function start() {
      if (extension_loaded('zlib')) {
        if ((int)ini_get('zlib.output_compression') < 1) {
          if (function_exists('ob_gzhandler')) {
            ini_set('zlib.output_compression_level', SERVICE_OUTPUT_COMPRESSION_GZIP_LEVEL);
            ob_start('ob_gzhandler');

            return false; // no call to stop() is needed
          } else {
            ob_start();
            ob_implicit_flush();
          }

          return true;
        }
      }

      return false;
    }

    function stop() {
      $encoding = false;
      if (!headers_sent() && !connection_aborted()) {
        if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false)) {
          $encoding = 'x-gzip';
        } elseif (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false)) {
          $encoding = 'gzip';
        }
      }

      if ($encoding !== false) {
        $contents = ob_get_contents();
        ob_end_clean();

        header('Content-Encoding: ' . $encoding);

        $size = strlen($contents);
        $crc = crc32($contents);

        $contents = gzcompress($contents, SERVICE_OUTPUT_COMPRESSION_GZIP_LEVEL);
        $contents = substr($contents, 0, strlen($contents) - 4);

        echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
        echo $contents;
        echo pack('V', $crc);
        echo pack('V', $size);
      } else {
        ob_end_flush();
      }

      return true;
    }

    function install() {
      global $osC_Database;

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('GZIP Compression Level', 'SERVICE_OUTPUT_COMPRESSION_GZIP_LEVEL', '5', 'Set the GZIP compression level to this value (0=min, 9=max).', '6', '0', 'tep_cfg_select_option(array(\'0\', \'1\', \'2\', \'3\', \'4\', \'5\', \'6\', \'7\', \'8\', \'9\'), ', now())");
    }

    function remove() {
      global $osC_Database;

      $osC_Database->simpleQuery("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('SERVICE_OUTPUT_COMPRESSION_GZIP_LEVEL');
    }
  }
?>

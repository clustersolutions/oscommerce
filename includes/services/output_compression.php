<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

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

    function install() {
      global $osC_Database;

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('GZIP Compression Level', 'SERVICE_OUTPUT_COMPRESSION_GZIP_LEVEL', '5', 'Set the GZIP compression level to this value (0=min, 9=max).', '6', '0', 'osc_cfg_set_boolean_value(array(\'0\', \'1\', \'2\', \'3\', \'4\', \'5\', \'6\', \'7\', \'8\', \'9\'))', now())");
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

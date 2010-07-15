<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Module\Service;

  class OutputCompression implements \osCommerce\OM\Core\Site\Shop\ServiceInterface {
    public static function start() {
      if ( extension_loaded('zlib') ) {
        if ( (int)ini_get('zlib.output_compression') < 1 ) {
          ini_set('zlib.output_compression_level', SERVICE_OUTPUT_COMPRESSION_GZIP_LEVEL);
          ob_start('ob_gzhandler');

          return false; // no call to stop() is needed
        }
      }

      return false;
    }

    public static function stop() {
      return true;
    }
  }
?>

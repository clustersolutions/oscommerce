<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\Model;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\HttpRequest;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\DateTime;

  class getAvailablePackages {
    public static function execute() {
      $OSCOM_Cache = Registry::get('Cache');

      $result = array('entries' => array());

      if ( $OSCOM_Cache->read('coreupdate-availablepackages', 360) ) {
        $versions = $OSCOM_Cache->getCache();
      } else {
        $versions = HttpRequest::getResponse(array('url' => 'http://www.oscommerce.com/version/online_merchant/3', 'method' => 'get'));

        $OSCOM_Cache->write($versions);
      }

      $versions_array = explode("\n", $versions);

      $counter = 0;

      foreach ( $versions_array as $v ) {
        $v_info = explode('|', $v);

        if ( version_compare(OSCOM::getVersion(), $v_info[0], '<') ) {
          $result['entries'][] = array('key' => $counter,
                                       'version' => $v_info[0],
                                       'date' => DateTime::getShort(DateTime::fromUnixTimestamp(DateTime::getTimestamp($v_info[1], 'Ymd'))),
                                       'announcement' => $v_info[2],
                                       'update_package' => (isset($v_info[3]) ? $v_info[3] : null));

          $counter++;
        }
      }

      usort($result['entries'], function ($a, $b) {
        return version_compare($a['version'], $b['version'], '>');
      });


      $result['total'] = count($result['entries']);

      return $result;
    }
  }
?>

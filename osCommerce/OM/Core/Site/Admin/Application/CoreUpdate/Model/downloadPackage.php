<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\Model;

  use osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\CoreUpdate;
  use osCommerce\OM\Core\HttpRequest;
  use osCommerce\OM\Core\OSCOM;

  class downloadPackage {
    public static function execute($version = null) {
      if ( empty($version) ) {
        $link = CoreUpdate::getAvailablePackageInfo('update_package');
      } else {
        $versions = CoreUpdate::getAvailablePackages();

        foreach ( $versions['entries'] as $v ) {
          if ( $v['version'] == $version ) {
            $link = $v['update_package'];

            break;
          }
        }
      }

      $response = HttpRequest::getResponse(array('url' => $link, 'parameters' => 'check=true'));

      return file_put_contents(OSCOM::BASE_DIRECTORY . 'Work/CoreUpdate/update.phar', $response);
    }
  }
?>

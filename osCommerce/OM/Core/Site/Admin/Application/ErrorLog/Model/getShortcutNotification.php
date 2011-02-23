<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\ErrorLog\Model;

  use osCommerce\OM\Core\ErrorHandler;
  use osCommerce\OM\Core\DateTime;

  class getShortcutNotification {
    public static function execute($datetime) {
      $errors = ErrorHandler::getAll(100);

      $from_timestamp = DateTime::getTimestamp($datetime, 'Y-m-d H:i:s');

      $result = 0;

      foreach ( $errors as $error ) {
        if ( $error['timestamp'] > $from_timestamp ) {
          $result++;
        }
      }

      return $result;
    }
  }
?>

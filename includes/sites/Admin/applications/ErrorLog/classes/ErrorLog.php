<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class OSCOM_Site_Admin_Application_ErrorLog_ErrorLog {
    public static function getAll($pageset = 1) {
      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array('entries' => array(),
                      'total' => OSCOM_ErrorHandler::getTotalEntries());

      foreach ( OSCOM_ErrorHandler::getAll(MAX_DISPLAY_SEARCH_RESULTS, $pageset) as $row ) {
        $result['entries'][] = array('date' => OSCOM_DateTime::getShort(OSCOM_DateTime::fromUnixTimestamp($row['timestamp']), true),
                                     'message' => $row['message']);
      }

      return $result;
    }

    public static function find($search, $pageset = 1) {
      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array('entries' => array(),
                      'total' => OSCOM_ErrorHandler::getTotalFindEntries($search));

      foreach ( OSCOM_ErrorHandler::find($search, MAX_DISPLAY_SEARCH_RESULTS, $pageset) as $row ) {
        $result['entries'][] = array('date' => OSCOM_DateTime::getShort(OSCOM_DateTime::fromUnixTimestamp($row['timestamp']), true),
                                     'message' => $row['message']);
      }

      return $result;
    }

    public static function delete() {
      OSCOM_ErrorHandler::clear();

      return true;
    }
  }
?>

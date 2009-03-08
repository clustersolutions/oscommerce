<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  define('osC_ErrorLog_Admin_logfile', ini_get('error_log'));

  class osC_ErrorLog_Admin {
    const logfile = osC_ErrorLog_Admin_logfile;

    public static function getAll($pageset = 1) {
      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $messages = array();

      if ( file_exists(self::logfile) ) {
        $messages = array_reverse(array_unique(file(self::logfile)));
      }

      $result = array('entries' => array(),
                      'total' => sizeof($messages));

      if ( $pageset !== -1 ) {
        $messages = array_slice($messages, (MAX_DISPLAY_SEARCH_RESULTS * ($pageset - 1)), MAX_DISPLAY_SEARCH_RESULTS);
      }

      foreach ( $messages as $message ) {
        if ( preg_match('/^\[([0-9]{2})-([A-Za-z]{3})-([0-9]{4}) ([0-9]{2}):([0-5][0-9]):([0-5][0-9])\] (.*)$/', $message) ) {
          $result['entries'][] = array('date' => substr($message, 1, 20),
                                       'message' => substr($message, 23));
        }
      }

      return $result;
    }

    public static function find($search, $pageset = 1) {
      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $messages = array();

      if ( file_exists(self::logfile) ) {
        $messages = array_reverse(array_unique(file(self::logfile)));
      }

      foreach ( $messages as $key => $message ) {
        if ( !preg_match('/^\[([0-9]{2})-([A-Za-z]{3})-([0-9]{4}) ([0-9]{2}):([0-5][0-9]):([0-5][0-9])\] (.*)' . preg_replace('/[^A-Za-z0-9s]/', '', $search) . '(.*)$/i', $message) ) {
          unset($messages[$key]);
        }
      }

      $result = array('entries' => array(),
                      'total' => sizeof($messages));

      if ( $pageset !== -1 ) {
        $messages = array_slice($messages, (MAX_DISPLAY_SEARCH_RESULTS * ($pageset - 1)), MAX_DISPLAY_SEARCH_RESULTS);
      }

      foreach ( $messages as $message ) {
        $result['entries'][] = array('date' => substr($message, 1, 20),
                                     'message' => substr($message, 23));
      }

      return $result;
    }

    public static function delete() {
      if ( file_exists(self::logfile) ) {
        return unlink(self::logfile);
      }

      return true;
    }
  }
?>

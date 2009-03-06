<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

/**
 * The osC_Breadcrumb class handles the breadcrumb navigation path
 */

  class osC_Breadcrumb {

/**
 * An array containing the breadcrumb navigation path
 *
 * @var array
 * @access private
 */

    private $_path = array();

/**
 * Resets the breadcrumb navigation path
 *
 * @access public
 */

    public function reset() {
      $this->_path = array();
    }

/**
 * Adds an entry to the breadcrumb navigation path
 *
 * @param string $title The title of the breadcrumb navigation entry
 * @param string $link The link of the breadcrumb navigation entry
 * @access public
 */

    public function add($title, $link = null) {
      $this->_path[] = array('title' => $title,
                             'link' => $link);
    }

/**
 * Returns the breadcrumb navigation path with the entries separated by $separator
 *
 * @param string $separator The string value to separate the breadcrumb navigation path entries with
 * @access public
 * @return string
 */

    public function getPath($separator = ' - ') {
      $trail_string = '';

      $trail_size = sizeof($this->_path);
      $counter = 0;

      foreach ( $this->_path as $entry ) {
        $counter++;

        if ( !empty($entry['link']) ) {
          $trail_string .= osc_link_object($entry['link'], $entry['title']);
        } else {
          $trail_string .= $entry['title'];
        }

        if ( $counter < $trail_size ) {
          $trail_string .= $separator;
        }
      }

      return $trail_string;
    }

/**
 * Returns the breadcrumb navigation path array
 *
 * @access public
 * @return array
 */

    public function getArray() {
      return $this->_path;
    }
  }
?>

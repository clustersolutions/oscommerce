<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop;

  use osCommerce\OM\Registry;

/**
 * The Category class manages category information
 */

  class Category {

/**
 * An array containing the category information
 *
 * @var array
 * @access protected
 */

    protected $_data = array();

/**
 * Constructor
 *
 * @param int $id The ID of the category to retrieve information from
 * @access public
 */

    public function __construct($id) {
      $OSCOM_CategoryTree = Registry::get('CategoryTree');

      if ( $OSCOM_CategoryTree->exists($id) ) {
        $this->_data = $OSCOM_CategoryTree->getData($id);
      }
    }

/**
 * Return the ID of the assigned category
 *
 * @access public
 * @return integer
 */

    public function getID() {
      return $this->_data['id'];
    }

/**
 * Return the title of the assigned category
 *
 * @access public
 * @return string
 */

    public function getTitle() {
      return $this->_data['name'];
    }

/**
 * Check if the category has an image
 *
 * @access public
 * @return string
 */

    public function hasImage() {
      return ( !empty($this->_data['image']) );
    }

/**
 * Return the image of the assigned category
 *
 * @access public
 * @return string
 */

    public function getImage() {
      return $this->_data['image'];
    }

/**
 * Check if the assigned category has a parent category
 *
 * @access public
 * @return boolean
 */

    public function hasParent() {
      return ( $this->_data['parent_id'] > 0 );
    }

/**
 * Return the parent ID of the assigned category
 *
 * @access public
 * @return integer
 */

    public function getParent() {
      return $this->_data['parent_id'];
    }

/**
 * Return the breadcrumb path of the assigned category
 *
 * @access public
 * @return string
 */

    public function getPath() {
      $OSCOM_CategoryTree = Registry::get('CategoryTree');

      return $OSCOM_CategoryTree->buildBreadcrumb($this->_data['id']);
    }

/**
 * Return specific information from the assigned category
 *
 * @access public
 * @return mixed
 */

    public function getData($keyword) {
      return $this->_data[$keyword];
    }
  }
?>

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

  class Image {
    protected $_groups;

    public function __construct() {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Language = Registry::get('Language');

      $this->_groups = array();

      $Qgroups = $OSCOM_Database->query('select * from :table_products_images_groups where language_id = :language_id');
      $Qgroups->bindInt(':language_id', $OSCOM_Language->getID());
      $Qgroups->setCache('images_groups-' . $OSCOM_Language->getID());
      $Qgroups->execute();

      while ( $Qgroups->next() ) {
        $this->_groups[$Qgroups->valueInt('id')] = $Qgroups->toArray();
      }

      $Qgroups->freeResult();
    }

    public function getID($code) {
      foreach ( $this->_groups as $group ) {
        if ( $group['code'] == $code ) {
          return $group['id'];
        }
      }

      return 0;
    }

    public function getCode($id) {
      return $this->_groups[$id]['code'];
    }

    public function getWidth($code) {
      return $this->_groups[$this->getID($code)]['size_width'];
    }

    public function getHeight($code) {
      return $this->_groups[$this->getID($code)]['size_height'];
    }

    public function exists($code) {
      return isset($this->_groups[$this->getID($code)]);
    }

    public function show($image, $title, $parameters = null, $group = null) {
      if ( empty($group) || !$this->exists($group) ) {
        $group = $this->getCode(DEFAULT_IMAGE_GROUP_ID);
      }

      $group_id = $this->getID($group);

      $width = $height = '';

      if ( ($this->_groups[$group_id]['force_size'] == '1') || empty($image) ) {
        $width = $this->_groups[$group_id]['size_width'];
        $height = $this->_groups[$group_id]['size_height'];
      }

      if ( empty($image) ) {
        $image = 'pixel_trans.gif';
      } else {
        $image = 'products/' . $this->_groups[$group_id]['code'] . '/' . $image;
      }

      return osc_image(DIR_WS_IMAGES . $image, $title, $width, $height, $parameters);
    }

    public function getAddress($image, $group = 'default') {
      $group_id = $this->getID($group);

      return DIR_WS_IMAGES . 'products/' . $this->_groups[$group_id]['code'] . '/' . $image;
    }
  }
?>
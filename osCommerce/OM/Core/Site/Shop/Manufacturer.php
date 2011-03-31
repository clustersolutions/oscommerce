<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop;

  use osCommerce\OM\Core\Registry;

  class Manufacturer {
    protected $_data = array();

    public function __construct($id) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qmanufacturer = $OSCOM_PDO->prepare('select manufacturers_id as id, manufacturers_name as name, manufacturers_image as image from :table_manufacturers where manufacturers_id = :manufacturers_id');
      $Qmanufacturer->bindInt(':manufacturers_id', $id);
      $Qmanufacturer->execute();

      $result = $Qmanufacturer->fetch();

      if ( $result !== false ) {
        $this->_data = $result;
      }
    }

    function getID() {
      if ( isset($this->_data['id']) ) {
        return $this->_data['id'];
      }

      return false;
    }

    function getTitle() {
      if ( isset($this->_data['name']) ) {
        return $this->_data['name'];
      }

      return false;
    }

    function getImage() {
      if ( isset($this->_data['image']) ) {
        return $this->_data['image'];
      }

      return false;
    }
  }
?>

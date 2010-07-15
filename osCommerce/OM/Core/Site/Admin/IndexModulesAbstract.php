<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin;

  abstract class IndexModulesAbstract {
    protected $_title;
    protected $_title_link;
    protected $_data;

    public function getTitle() {
      return $this->_title;
    }

    public function getTitleLink() {
      return $this->_title_link;
    }

    public function hasTitleLink() {
      return isset($this->_title_link) && !empty($this->_title_link);
    }

    public function getData() {
      return $this->_data;
    }

    public function hasData() {
      return isset($this->_data) && !empty($this->_data);
    }
  }
?>

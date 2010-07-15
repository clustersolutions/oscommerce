<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM;

  abstract class ApplicationAbstract {
    protected $_page_contents = 'main.php';
    protected $_page_title;

    abstract protected function initialize();

    public function getPageTitle() {
      return $this->_page_title;
    }

    public function setPageTitle($title) {
      $this->_page_title = $title;
    }

    public function getPageContent() {
      return $this->_page_contents;
    }

    public function setPageContent($filename) {
      $this->_page_contents = $filename;
    }
  }
?>

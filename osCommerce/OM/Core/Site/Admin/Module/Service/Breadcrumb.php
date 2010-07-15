<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Module\Service;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  class Breadcrumb {
    var $title,
        $description,
        $uninstallable = true,
        $depends,
        $precedes;

    public function __construct() {
      $OSCOM_Language = Registry::get('Language');

      $OSCOM_Language->loadIniFile('modules/services/breadcrumb.php');

      $this->title = OSCOM::getDef('services_breadcrumb_title');
      $this->description = OSCOM::getDef('services_breadcrumb_description');
    }

    public function install() {
      return false;
    }

    public function remove() {
      return false;
    }

    public function keys() {
      return false;
    }
  }
?>

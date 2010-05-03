<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Admin\Application\ZoneGroups;

  use osCommerce\OM\Site\Admin\ApplicationAbstract;
  use osCommerce\OM\OSCOM;

  class Controller extends ApplicationAbstract {
    protected $_group = 'configuration';
    protected $_icon = 'zonegroups.png';
    protected $_sort_order = 700;

    protected function initialize() {
      $this->_title = OSCOM::getDef('app_title');
    }

    protected function process() {
      $this->_page_title = OSCOM::getDef('heading_title');

      if ( isset($_GET['id']) && is_numeric($_GET['id']) ) {
        $this->_page_contents = 'entries.php';
        $this->_page_title .= ': ' . ZoneGroups::get($_GET['id'], 'geo_zone_name');
      }
    }
  }
?>

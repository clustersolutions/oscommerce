<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Module\Service;

  use osCommerce\OM\Registry;

  class RecentlyVisited implements \osCommerce\OM\Site\Shop\ServiceInterface {
    public static function start() {
      $OSCOM_Service = Registry::get('Service');

      Registry::set('RecentlyVisited', new \osCommerce\OM\Site\Shop\RecentlyVisited());

      $OSCOM_Service->addCallBeforePageContent('RecentlyVisited', 'initialize');

      return true;
    }

    public static function stop() {
      return true;
    }
  }
?>

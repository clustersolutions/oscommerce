<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Module\Service;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\RecentlyVisited as RecentlyVisitedClass;

  class RecentlyVisited implements \osCommerce\OM\Core\Site\Shop\ServiceInterface {
    public static function start() {
      $OSCOM_Service = Registry::get('Service');

      Registry::set('RecentlyVisited', new RecentlyVisitedClass());

      $OSCOM_Service->addCallBeforePageContent('RecentlyVisited', 'initialize');

      return true;
    }

    public static function stop() {
      return true;
    }
  }
?>

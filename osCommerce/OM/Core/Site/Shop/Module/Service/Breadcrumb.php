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
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\Breadcrumb as BreadcrumbClass;

  class Breadcrumb implements \osCommerce\OM\Core\Site\Shop\ServiceInterface {
    public static function start() {
      Registry::set('Breadcrumb', new BreadcrumbClass());

      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');

      $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_top'), OSCOM::getLink(OSCOM::getDefaultSite(), OSCOM::getDefaultSiteApplication()));
      $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_shop'), OSCOM::getLink('Shop', 'Index'));

      return true;
    }

    public static function stop() {
      return true;
    }
  }
?>

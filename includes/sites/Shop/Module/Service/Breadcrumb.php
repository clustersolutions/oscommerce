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
  use osCommerce\OM\OSCOM;

  class Breadcrumb implements \osCommerce\OM\Site\Shop\ServiceInterface {
    public static function start() {
      Registry::set('Breadcrumb', new \osCommerce\OM\Site\Shop\Breadcrumb());

      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');

      $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_top'), OSCOM::getLink(OSCOM::getDefaultSite(), OSCOM::getDefaultSiteApplication()));
      $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_shop'), OSCOM::getLink());

      return true;
    }

    public static function stop() {
      return true;
    }
  }
?>

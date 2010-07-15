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
  use osCommerce\OM\Core\Site\Shop\Banner as BannerClass;

  class Banner implements \osCommerce\OM\Core\Site\Shop\ServiceInterface {
    public static function start() {
      Registry::set('Banner', new BannerClass());

      $OSCOM_Banner = Registry::get('Banner');

      $OSCOM_Banner->activateAll();
      $OSCOM_Banner->expireAll();

      return true;
    }

    public static function stop() {
      return true;
    }
  }
?>

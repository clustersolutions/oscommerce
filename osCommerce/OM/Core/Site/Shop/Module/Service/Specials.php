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

  class Specials implements \osCommerce\OM\Site\Shop\ServiceInterface {
    public static function start() {
      Registry::set('Specials', new \osCommerce\OM\Site\Shop\Specials());

      $OSCOM_Specials = Registry::get('Specials');

      $OSCOM_Specials->activateAll();
      $OSCOM_Specials->expireAll();

      return true;
    }

    public static function stop() {
      return true;
    }
  }
?>

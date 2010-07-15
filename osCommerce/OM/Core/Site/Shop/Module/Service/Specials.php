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
  use osCommerce\OM\Core\Site\Shop\Specials as SpecialsClass;

  class Specials implements \osCommerce\OM\Core\Site\Shop\ServiceInterface {
    public static function start() {
      Registry::set('Specials', new SpecialsClass());

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

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

  class Language implements \osCommerce\OM\Site\Shop\ServiceInterface {
    public static function start() {
      Registry::set('Language', new \osCommerce\OM\Language());

      $OSCOM_Language = Registry::get('Language');

      if ( isset($_GET['language']) && !empty($_GET['language']) ) {
        $OSCOM_Language->set($_GET['language']);
      }

      $OSCOM_Language->load('general');
      $OSCOM_Language->load('modules-boxes');
      $OSCOM_Language->load('modules-content');

      header('Content-Type: text/html; charset=' . $OSCOM_Language->getCharacterSet());

      osc_setlocale(LC_TIME, explode(',', $OSCOM_Language->getLocale()));

      return true;
    }

    public static function stop() {
      return true;
    }
  }
?>

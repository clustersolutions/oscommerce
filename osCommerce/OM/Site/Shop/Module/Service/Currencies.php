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

  class Currencies implements \osCommerce\OM\Site\Shop\ServiceInterface {
    public static function start() {
      Registry::set('Currencies', new \osCommerce\OM\Site\Shop\Currencies());
      $OSCOM_Currencies = Registry::get('Currencies');

      $OSCOM_Language = Registry::get('Language');

      if ( !isset($_SESSION['currency']) || isset($_GET['currency']) || ( (USE_DEFAULT_LANGUAGE_CURRENCY == '1') && ($OSCOM_Currencies->getCode($OSCOM_Language->getCurrencyID()) != $_SESSION['currency']) ) ) {
        if ( isset($_GET['currency']) && $OSCOM_Currencies->exists($_GET['currency']) ) {
          $_SESSION['currency'] = $_GET['currency'];
        } else {
          $_SESSION['currency'] = (USE_DEFAULT_LANGUAGE_CURRENCY == '1') ? $OSCOM_Currencies->getCode($OSCOM_Language->getCurrencyID()) : DEFAULT_CURRENCY;
        }

        if ( isset($_SESSION['cartID']) ) {
          unset($_SESSION['cartID']);
        }
      }

      return true;
    }

    public static function stop() {
      return true;
    }
  }
?>

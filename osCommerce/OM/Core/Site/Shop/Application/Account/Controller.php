<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Application\Account;

  use osCommerce\OM\Registry;
  use osCommerce\OM\OSCOM;

  class Controller extends \osCommerce\OM\Site\Shop\ApplicationAbstract {
    protected function initialize() {}

    protected function process() {
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');

      $OSCOM_Language->load('account');

      $this->_page_title = OSCOM::getDef('account_heading');

      if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
        $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_my_account'), OSCOM::getLink(null, null, null, 'SSL'));
      }
    }
  }
?>

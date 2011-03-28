<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Login\Action;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  class Logoff {
    public static function execute(ApplicationAbstract $application) {
      unset($_SESSION[OSCOM::getSite()]);

      Registry::get('MessageStack')->add('header', OSCOM::getDef('ms_success_logged_out'), 'success');

      OSCOM::redirect(OSCOM::getLink(null, OSCOM::getDefaultSiteApplication()));
    }
  }
?>

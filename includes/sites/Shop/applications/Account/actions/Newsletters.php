<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Application\Account\Action;

  use osCommerce\OM\ApplicationAbstract;
  use osCommerce\OM\Registry;
  use osCommerce\OM\OSCOM;

  class Newsletters {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_NavigationHistory = Registry::get('NavigationHistory');
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');

      if ( $OSCOM_Customer->isLoggedOn() === false ) {
        $OSCOM_NavigationHistory->setSnapshot();

        osc_redirect(OSCOM::getLink(null, null, 'LogIn', 'SSL'));
      }

      $application->setPageTitle(OSCOM::getDef('newsletters_heading'));
      $application->setPageContent('newsletters.php');

      if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
        $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_newsletters'), OSCOM::getLink(null, null, 'Newsletters', 'SSL'));
      }

      if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'process') ) {
        self::_process();
      }
    }

    protected static function _process() {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_MessageStack = Registry::get('MessageStack');

      if ( isset($_POST['newsletter_general']) && is_numeric($_POST['newsletter_general']) ) {
        $newsletter_general = (int)$_POST['newsletter_general'];
      } else {
        $newsletter_general = 0;
      }

// HPDL Should be moved to the customers class!
      $Qnewsletter = $OSCOM_Database->query('select customers_newsletter from :table_customers where customers_id = :customers_id');
      $Qnewsletter->bindInt(':customers_id', $OSCOM_Customer->getID());
      $Qnewsletter->execute();

      if ( $newsletter_general !== $Qnewsletter->valueInt('customers_newsletter') ) {
        $newsletter_general = (($Qnewsletter->value('customers_newsletter') == '1') ? '0' : '1');

        $Qupdate = $OSCOM_Database->query('update :table_customers set customers_newsletter = :customers_newsletter where customers_id = :customers_id');
        $Qupdate->bindInt(':customers_newsletter', $newsletter_general);
        $Qupdate->bindInt(':customers_id', $OSCOM_Customer->getID());
        $Qupdate->execute();

        if ( $Qupdate->affectedRows() === 1 ) {
          $OSCOM_MessageStack->add('Account', OSCOM::getDef('success_newsletter_updated'), 'success');
        }
      }

      osc_redirect(OSCOM::getLink(null, 'Account', null, 'SSL'));
    }
  }
?>

<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Application\Products\Action\Reviews;

  use osCommerce\OM\ApplicationAbstract;
  use osCommerce\OM\Registry;
  use osCommerce\OM\Site\Shop\Reviews;
  use osCommerce\OM\Site\Shop\Product;
  use osCommerce\OM\OSCOM;

  class View {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');

      $review_check = false;

      if ( is_numeric($_GET['View']) ) {
        if ( Reviews::exists($_GET['View']) ) {
          $review_check = true;

          Registry::set('Product', new Product(Reviews::getProductID($_GET['View'])));
          $OSCOM_Product = Registry::get('Product');

          $application->setPageTitle($OSCOM_Product->getTitle());
          $application->setPageContent('reviews_view.php');

          if ( $OSCOM_Service->isStarted('Breadcrumb')) {
            $OSCOM_Breadcrumb->add($OSCOM_Product->getTitle(), OSCOM::getLink(null, null, 'Reviews&View=' . $_GET['View'] . '&' . $OSCOM_Product->getKeyword()));
          }
        }
      }

      if ( $review_check === false ) {
        $application->setPageContent('reviews_not_found.php');
      }
    }
  }
?>

<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Application\Products\Action;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Shop\Product;

  class Reviews {
    public static function execute(ApplicationAbstract $application) {
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');

      if ( $OSCOM_Service->isStarted('Reviews') === false ) {
        osc_redirect(OSCOM::getLink(null, OSCOM::getDefaultSiteApplication()));
      }

      $application->setPageTitle(OSCOM::getDef('reviews_heading'));
      $application->setPageContent('reviews.php');

      if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
        $OSCOM_Breadcrumb->add(OSCOM::getDef('breadcrumb_reviews'), OSCOM::getLink(null, null, 'Reviews'));
      }

      $requested_product = null;
      $product_check = false;

      if ( count($_GET) > 2 ) {
        $requested_product = basename(key(array_slice($_GET, 2, 1, true)));

        if ( $requested_product == OSCOM::getSiteApplication() ) {
          unset($requested_product);

          if ( count($_GET) > 3 ) {
            $requested_product = basename(key(array_slice($_GET, 3, 1, true)));
          }
        }
      }

      if ( isset($requested_product) ) {
        if ( Product::checkEntry($requested_product) ) {
          $product_check = true;

          Registry::set('Product', new Product($requested_product));
          $OSCOM_Product = Registry::get('Product');

          $application->setPageTitle($OSCOM_Product->getTitle());
          $application->setPageContent('reviews_product.php');

          if ( $OSCOM_Service->isStarted('Breadcrumb')) {
            $OSCOM_Breadcrumb->add($OSCOM_Product->getTitle(), OSCOM::getLink(null, null, 'Reviews&' . $OSCOM_Product->getKeyword()));
          }
        }

        if ( $product_check === false ) {
          $application->setPageContent('not_found.php');
        }
      }
    }
  }
?>

<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop\Application\Products;

  use osCommerce\OM\Registry;
  use osCommerce\OM\Site\Shop\Product;
  use osCommerce\OM\OSCOM;

  class Controller extends \osCommerce\OM\Site\Shop\ApplicationAbstract {
    protected function initialize() {}

    protected function process() {
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_Session = Registry::get('Session');
      $OSCOM_Template = Registry::get('Template');
      $OSCOM_Service = Registry::get('Service');
      $OSCOM_Breadcrumb = Registry::get('Breadcrumb');

      $OSCOM_Language->load('products');

      $requested_product = null;
      $product_check = false;

      if ( count($_GET) > 1 ) {
        $requested_product = basename(key(array_slice($_GET, 1, 1)));

        if ( $requested_product == OSCOM::getSiteApplication() ) {
          unset($requested_product);

          if ( count($_GET) > 2 ) {
            $requested_product = basename(key(array_slice($_GET, 2, 1)));
          }
        }
      }

      if ( isset($requested_product) ) {
        if ( !self::siteApplicationSubActionExists(OSCOM::getSiteApplication(), $requested_product) ) {
          if ( Product::checkEntry($requested_product) ) {
            $product_check = true;

            Registry::set('Product', new Product($requested_product));
            $OSCOM_Product = Registry::get('Product');
            $OSCOM_Product->incrementCounter();

            $OSCOM_Template->addPageTags('keywords', $OSCOM_Product->getTitle());
            $OSCOM_Template->addPageTags('keywords', $OSCOM_Product->getModel());

            if ( $OSCOM_Product->hasTags() ) {
              $OSCOM_Template->addPageTags('keywords', $OSCOM_Product->getTags());
            }

            $OSCOM_Template->addJavascriptFilename('templates/' . $OSCOM_Template->getCode() . '/javascript/Products/info.js');

// HPDL            osC_Services_category_path::process($osC_Product->getCategoryID());

            if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
              $OSCOM_Breadcrumb->add($OSCOM_Product->getTitle(), OSCOM::getLink(null, null, $OSCOM_Product->getKeyword()));
            }

            $this->_page_title = $OSCOM_Product->getTitle();
          }
        }
      }

      if ( $product_check === false ) {
        $this->_page_title = OSCOM::getDef('product_not_found_heading');
        $this->_page_contents = 'not_found.php';
      }
    }
  }
?>

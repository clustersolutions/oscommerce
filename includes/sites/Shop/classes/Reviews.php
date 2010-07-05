<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop;

  use osCommerce\OM\Registry;

  class Reviews {
     protected $is_enabled = false;
     protected $is_moderated = false;

    public function __construct() {
      $this->enableReviews();
      $this->enableModeration();
    }

    public function isEnabled() {
      return $this->is_enabled;
    }

    public function isModerated() {
      return $this->is_moderated;
    }

    function enableReviews() {
      $OSCOM_Customer = Registry::get('Customer');

      switch ( SERVICE_REVIEW_ENABLE_REVIEWS ) {
        case 0:
          $this->is_enabled = true;
          break;

        case 1:
          if ( $OSCOM_Customer->isLoggedOn() ) {
            $this->is_enabled = true;
          } else {
            $this->is_enabled = false;
          }
          break;

        case 2:
          if ( $this->hasPurchased() ) {
            $this->is_enabled = true;
          } else {
            $this->is_enabled = false;
          }
          break;

        default:
          $this->is_enabled = false;
          break;
        }
      }

    function hasPurchased() {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Customer = Registry::get('Customer');

      $Qhaspurchased = $OSCOM_Database->query('select count(*) as total from :table_orders o, :table_orders_products op, :table_products p where o.customers_id = :customers_id and o.orders_id = op.orders_id and op.products_id = p.products_id and op.products_id = :products_id');
      $Qhaspurchased->bindInt(':customers_id', $OSCOM_Customer->getID());
      $Qhaspurchased->bindInt(':products_id', $_GET['products_id']);
      $Qhaspurchased->execute();

      return ($Qhaspurchased->valueInt('total') >= '1');
    }

    function enableModeration() {
      $OSCOM_Customer = Registry::get('Customer');

      switch ( SERVICE_REVIEW_ENABLE_MODERATION ) {
      case -1:
        $this->is_moderated = false;
        break;

      case 0:
        if ( $OSCOM_Customer->isLoggedOn() ) {
          $this->is_moderated = false;
        } else {
          $this->is_moderated = true;
        }
        break;

      case 1:
        $this->is_moderated = true;
        break;

      default:
        $this->is_moderated = true;
        break;
      }
    }

    function getTotal($id) {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Language = Registry::get('Language');

      $Qcheck = $OSCOM_Database->query('select count(*) as total from :table_reviews where products_id = :products_id and languages_id = :languages_id and reviews_status = 1 limit 1');
      $Qcheck->bindInt(':products_id', $id);
      $Qcheck->bindInt(':languages_id', $OSCOM_Language->getID());
      $Qcheck->execute();

      return $Qcheck->valueInt('total');
    }

    public static function exists($id = null, $groupped = false) {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Language = Registry::get('Language');

      $Qcheck = $OSCOM_Database->query('select reviews_id from :table_reviews where');

      if ( is_numeric($id) ) {
        if ( $groupped === false ) {
          $Qcheck->appendQuery('reviews_id = :reviews_id and');
          $Qcheck->bindInt(':reviews_id', $id);
        } else {
          $Qcheck->appendQuery('products_id = :products_id and');
          $Qcheck->bindInt(':products_id', $id);
        }
      }

      $Qcheck->appendQuery('languages_id = :languages_id and reviews_status = 1 limit 1');
      $Qcheck->bindInt(':languages_id', $OSCOM_Language->getID());
      $Qcheck->execute();

      return ($Qcheck->numberOfRows() === 1);
    }

    public static function getProductID($id) {
      $OSCOM_Database = Registry::get('Database');

      $Qreview = $OSCOM_Database->query('select products_id from :table_reviews where reviews_id = :reviews_id');
      $Qreview->bindInt(':reviews_id', $id);
      $Qreview->execute();

      return $Qreview->valueInt('products_id');
    }

    public static function getListing($id = null) {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Language = Registry::get('Language');

      if ( isset($id) && is_numeric($id) ) {
        $Qreviews = $OSCOM_Database->query('select reviews_id, reviews_text, reviews_rating, date_added, customers_name from :table_reviews where products_id = :products_id and languages_id = :languages_id and reviews_status = 1 order by reviews_id desc');
        $Qreviews->bindInt(':products_id', $id);
        $Qreviews->bindInt(':languages_id', $OSCOM_Language->getID());
      } else {
        $Qreviews = $OSCOM_Database->query('select r.reviews_id, left(r.reviews_text, 100) as reviews_text, r.reviews_rating, r.date_added, r.customers_name, p.products_id, p.products_price, p.products_tax_class_id, pd.products_name, pd.products_keyword, i.image from :table_reviews r, :table_products p left join :table_products_images i on (p.products_id = i.products_id and i.default_flag = :default_flag), :table_products_description pd where r.reviews_status = 1 and r.languages_id = :languages_id and r.products_id = p.products_id and p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id order by r.reviews_id desc');
        $Qreviews->bindInt(':default_flag', 1);
        $Qreviews->bindInt(':languages_id', $OSCOM_Language->getID());
        $Qreviews->bindInt(':language_id', $OSCOM_Language->getID());
      }
      $Qreviews->setBatchLimit((isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1), MAX_DISPLAY_NEW_REVIEWS);
      $Qreviews->execute();

      return $Qreviews;
    }

    public static function getEntry($id) {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Language = Registry::get('Language');

      $Qreviews = $OSCOM_Database->query('select reviews_id, reviews_text, reviews_rating, date_added, customers_name from :table_reviews where reviews_id = :reviews_id and languages_id = :languages_id and reviews_status = 1');
      $Qreviews->bindInt(':reviews_id', $id);
      $Qreviews->bindInt(':languages_id', $OSCOM_Language->getID());
      $Qreviews->execute();

      return $Qreviews;
    }

    public static function saveEntry($data) {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Language = Registry::get('Language');

      $Qreview = $OSCOM_Database->query('insert into :table_reviews (products_id, customers_id, customers_name, reviews_rating, languages_id, reviews_text, reviews_status, date_added) values (:products_id, :customers_id, :customers_name, :reviews_rating, :languages_id, :reviews_text, :reviews_status, now())');
      $Qreview->bindInt(':products_id', $data['products_id']);
      $Qreview->bindInt(':customers_id', $data['customer_id']);
      $Qreview->bindValue(':customers_name', $data['customer_name']);
      $Qreview->bindValue(':reviews_rating', $data['rating']);
      $Qreview->bindInt(':languages_id', $OSCOM_Language->getID());
      $Qreview->bindValue(':reviews_text', $data['review']);
      $Qreview->bindInt(':reviews_status', $data['status']);
      $Qreview->execute();
    }
  }
?>

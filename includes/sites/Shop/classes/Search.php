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

  class Search extends Products {
    var $_period_min_year,
        $_period_max_year,
        $_date_from,
        $_date_to,
        $_price_from,
        $_price_to,
        $_keywords,
        $_number_of_results;

    public function __construct() {
      $OSCOM_Database = Registry::get('Database');

      $Qproducts = $OSCOM_Database->query('select min(year(products_date_added)) as min_year, max(year(products_date_added)) as max_year from :table_products limit 1');
      $Qproducts->execute();

      $this->_period_min_year = $Qproducts->valueInt('min_year');
      $this->_period_max_year = $Qproducts->valueInt('max_year');
    }

    function getMinYear() {
      return $this->_period_min_year;
    }

    function getMaxYear() {
      return $this->_period_max_year;
    }

    function getDateFrom() {
      return $this->_date_from;
    }

    function getDateTo() {
      return $this->_date_to;
    }

    function getPriceFrom() {
      return $this->_price_from;
    }

    function getPriceTo() {
      return $this->_price_to;
    }

    function getKeywords() {
      return $this->_keywords;
    }

    function getNumberOfResults() {
      return $this->_number_of_results;
    }

    function hasDateSet($flag = null) {
      if ($flag == 'from') {
        return isset($this->_date_from);
      } elseif ($flag == 'to') {
        return isset($this->_date_to);
      }

      return isset($this->_date_from) && isset($this->_date_to);
    }

    function hasPriceSet($flag = null) {
      if ($flag == 'from') {
        return isset($this->_price_from);
      } elseif ($flag == 'to') {
        return isset($this->_price_to);
      }

      return isset($this->_price_from) && isset($this->_price_to);
    }

    function hasKeywords() {
      return isset($this->_keywords) && !empty($this->_keywords);
    }

    function setDateFrom($timestamp) {
      $this->_date_from = $timestamp;
    }

    function setDateTo($timestamp) {
      $this->_date_to = $timestamp;
    }

    function setPriceFrom($price) {
      $this->_price_from = $price;
    }

    function setPriceTo($price) {
      $this->_price_to = $price;
    }

    function setKeywords($keywords) {
      $terms = explode(' ', trim($keywords));

      $terms_array = array();

      $counter = 0;

      foreach ($terms as $word) {
        $counter++;

        if ($counter > 5) {
          break;
        } elseif (!empty($word)) {
          if (!in_array($word, $terms_array)) {
            $terms_array[] = $word;
          }
        }
      }

      $this->_keywords = implode(' ', $terms_array);
    }

    function execute() {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Customer = Registry::get('Customer');
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_CategoryTree = Registry::get('CategoryTree');
      $OSCOM_Currencies = Registry::get('Currencies');

      $Qlisting = $OSCOM_Database->query('select SQL_CALC_FOUND_ROWS distinct p.*, pd.*, m.*, i.image, if(s.status, s.specials_new_products_price, null) as specials_new_products_price, if(s.status, s.specials_new_products_price, p.products_price) as final_price');

      if ( ($this->hasPriceSet('from') || $this->hasPriceSet('to')) && (DISPLAY_PRICE_WITH_TAX == '1') ) {
        $Qlisting->appendQuery(', sum(tr.tax_rate) as tax_rate');
      }

      $Qlisting->appendQuery('from :table_products p left join :table_manufacturers m using(manufacturers_id) left join :table_specials s on (p.products_id = s.products_id) left join :table_products_images i on (p.products_id = i.products_id and i.default_flag = :default_flag)');
      $Qlisting->bindInt(':default_flag', 1);

      if ( ($this->hasPriceSet('from') || $this->hasPriceSet('to')) && (DISPLAY_PRICE_WITH_TAX == '1') ) {
        if ( $OSCOM_Customer->isLoggedOn() ) {
          $customer_country_id = $OSCOM_Customer->getCountryID();
          $customer_zone_id = $OSCOM_Customer->getZoneID();
        } else {
          $customer_country_id = STORE_COUNTRY;
          $customer_zone_id = STORE_ZONE;
        }

        $Qlisting->appendQuery('left join :table_tax_rates tr on p.products_tax_class_id = tr.tax_class_id left join :table_zones_to_geo_zones gz on tr.tax_zone_id = gz.geo_zone_id and (gz.zone_country_id is null or gz.zone_country_id = 0 or gz.zone_country_id = :zone_country_id) and (gz.zone_id is null or gz.zone_id = 0 or gz.zone_id = :zone_id)');
        $Qlisting->bindInt(':zone_country_id', $customer_country_id);
        $Qlisting->bindInt(':zone_id', $customer_zone_id);
      }

      $Qlisting->appendQuery(', :table_products_description pd, :table_categories c, :table_products_to_categories p2c where p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id');
      $Qlisting->bindInt(':language_id', $OSCOM_Language->getID());

      if ( $this->hasCategory() ) {
        if ( $this->isRecursive() ) {
          $subcategories_array = array($this->_category);

          $Qlisting->appendQuery('and p2c.products_id = p.products_id and p2c.products_id = pd.products_id and p2c.categories_id in (:categories_id)');
          $Qlisting->bindRaw(':categories_id', implode(',', $OSCOM_CategoryTree->getChildren($this->_category, $subcategories_array)));
        } else {
          $Qlisting->appendQuery('and p2c.products_id = p.products_id and p2c.products_id = pd.products_id and pd.language_id = :language_id and p2c.categories_id = :categories_id');
          $Qlisting->bindInt(':language_id', $OSCOM_Language->getID());
          $Qlisting->bindInt(':categories_id', $this->_category);
        }
      }

      if ( $this->hasManufacturer() ) {
        $Qlisting->appendQuery('and m.manufacturers_id = :manufacturers_id');
        $Qlisting->bindInt(':manufacturers_id', $this->_manufacturer);
      }

      if ( $this->hasKeywords() ) {
        $Qlisting->prepareSearch($this->_keywords, array('pd.products_name', 'pd.products_description'), true);
      }

      if ( $this->hasDateSet('from') ) {
        $Qlisting->appendQuery('and p.products_date_added >= :products_date_added');
        $Qlisting->bindValue(':products_date_added', date('Y-m-d H:i:s', $this->_date_from));
      }

      if ( $this->hasDateSet('to') ) {
        $Qlisting->appendQuery('and p.products_date_added <= :products_date_added');
        $Qlisting->bindValue(':products_date_added', date('Y-m-d H:i:s', $this->_date_to));
      }

      if ( $this->hasPriceSet('from') ) {
        if ( $OSCOM_Currencies->exists($_SESSION['currency']) ) {
          $this->_price_from = $this->_price_from / $OSCOM_Currencies->value($_SESSION['currency']);
        }
      }

      if ( $this->hasPriceSet('to') ) {
        if ( $OSCOM_Currencies->exists($_SESSION['currency']) ) {
          $this->_price_to = $this->_price_to / $OSCOM_Currencies->value($_SESSION['currency']);
        }
      }

      if ( DISPLAY_PRICE_WITH_TAX == '1' ) {
        if ( $this->_price_from > 0 ) {
          $Qlisting->appendQuery('and (if(s.status, s.specials_new_products_price, p.products_price) * if(gz.geo_zone_id is null, 1, 1 + (tr.tax_rate / 100) ) >= :price_from)');
          $Qlisting->bindFloat(':price_from', $this->_price_from);
        }

        if ( $this->_price_to > 0 ) {
          $Qlisting->appendQuery('and (if(s.status, s.specials_new_products_price, p.products_price) * if(gz.geo_zone_id is null, 1, 1 + (tr.tax_rate / 100) ) <= :price_to)');
          $Qlisting->bindFloat(':price_to', $this->_price_to);
        }
      } else {
        if ( $this->_price_from > 0 ) {
          $Qlisting->appendQuery('and (if(s.status, s.specials_new_products_price, p.products_price) >= :price_from)');
          $Qlisting->bindFloat(':price_from', $this->_price_from);
        }

        if ( $this->_price_to > 0 ) {
          $Qlisting->appendQuery('and (if(s.status, s.specials_new_products_price, p.products_price) <= :price_to)');
          $Qlisting->bindFloat(':price_to', $this->_price_to);
        }
      }

      if ( ($this->hasPriceSet('from') || $this->hasPriceSet('to')) && (DISPLAY_PRICE_WITH_TAX == '1') ) {
        $Qlisting->appendQuery('group by p.products_id, tr.tax_priority');
      }

      $Qlisting->appendQuery('order by');

      if ( isset($this->_sort_by) ) {
        $Qlisting->appendQuery(':order_by :order_by_direction, pd.products_name');
        $Qlisting->bindRaw(':order_by', $this->_sort_by);
        $Qlisting->bindRaw(':order_by_direction', (($this->_sort_by_direction == '-') ? 'desc' : ''));
      } else {
        $Qlisting->appendQuery('pd.products_name :order_by_direction');
        $Qlisting->bindRaw(':order_by_direction', (($this->_sort_by_direction == '-') ? 'desc' : ''));
      }

      $Qlisting->setBatchLimit((isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1), MAX_DISPLAY_SEARCH_RESULTS);
      $Qlisting->execute();

      $this->_number_of_results = $Qlisting->getBatchSize();

      return $Qlisting;
    }
  }
?>

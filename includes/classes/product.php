<?php
/*
  $Id: account.php 207 2005-09-26 01:29:31 +0200 (Mo, 26 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Product {
    var $_data = array();

    function osC_Product($id) {
      global $osC_Database, $osC_Services, $osC_Language, $osC_Image;

      if (!empty($id)) {
        $Qproduct = $osC_Database->query('select p.products_id as id, p.products_quantity as quantity, p.products_price as price, p.products_tax_class_id as tax_class_id, p.products_date_added as date_added, p.products_date_available as date_available, p.manufacturers_id, pd.products_name as name, pd.products_description as description, pd.products_model as model, pd.products_keyword as keyword, pd.products_tags as tags, pd.products_url as url from :table_products p, :table_products_description pd where');
        $Qproduct->bindTable(':table_products', TABLE_PRODUCTS);
        $Qproduct->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);

        if (is_numeric($id) || ereg('[0-9]+[{[0-9]+}[0-9]+]*$', $id)) {
          $Qproduct->appendQuery('p.products_id = :products_id');
          $Qproduct->bindInt(':products_id', osc_get_product_id($id));
        } else {
          $Qproduct->appendQuery('pd.products_keyword = :products_keyword');
          $Qproduct->bindValue(':products_keyword', $id);
        }

        $Qproduct->appendQuery('and p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id');
        $Qproduct->bindInt(':language_id', $osC_Language->getID());
        $Qproduct->execute();

        if ($Qproduct->numberOfRows() === 1) {
          $this->_data = $Qproduct->toArray();

          $this->_data['images'] = array();

          $Qimages = $osC_Database->query('select id, image, default_flag from :table_products_images where products_id = :products_id order by sort_order');
          $Qimages->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
          $Qimages->bindInt(':products_id', $this->_data['id']);
          $Qimages->execute();

          while ($Qimages->next()) {
            $this->_data['images'][] = $Qimages->toArray();
          }

          $Qcategory = $osC_Database->query('select categories_id from :table_products_to_categories where products_id = :products_id limit 1');
          $Qcategory->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
          $Qcategory->bindInt(':products_id', $this->_data['id']);
          $Qcategory->execute();

          $this->_data['category_id'] = $Qcategory->valueInt('categories_id');

          $Qcheck = $osC_Database->query('select products_attributes_id from :table_products_attributes patrib where products_id = :products_id limit 1');
          $Qcheck->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
          $Qcheck->bindInt(':products_id', $this->_data['id']);
          $Qcheck->execute();

          if ($Qcheck->numberOfRows() === 1) {
            $this->_data['attributes'] = array();

            $Qattributes = $osC_Database->query('select pa.*, po.*, pov.* from :table_products_attributes pa, :table_products_options po, :table_products_options_values pov where pa.products_id = :products_id and pa.options_id = po.products_options_id and po.language_id = :language_id and pa.options_values_id = pov.products_options_values_id and pov.language_id = :language_id order by po.products_options_name, pov.products_options_values_name');
            $Qattributes->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
            $Qattributes->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
            $Qattributes->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
            $Qattributes->bindInt(':products_id', $this->_data['id']);
            $Qattributes->bindInt(':language_id', $osC_Language->getID());
            $Qattributes->bindInt(':language_id', $osC_Language->getID());
            $Qattributes->execute();

            while ($Qattributes->next()) {
              $this->_data['attributes'][] = array('options_id' => $Qattributes->valueInt('options_id'),
                                                   'options_name' => $Qattributes->value('products_options_name'),
                                                   'options_values_id' => $Qattributes->valueInt('options_values_id'),
                                                   'options_values_name' => $Qattributes->value('products_options_values_name'),
                                                   'options_values_price' => $Qattributes->value('options_values_price'),
                                                   'price_prefix' => $Qattributes->value('price_prefix'));
            }
          }

          if ($osC_Services->isStarted('reviews')) {
            $Qavg = $osC_Database->query('select avg(reviews_rating) as rating from :table_reviews where products_id = :products_id and languages_id = :languages_id and reviews_status = 1');
            $Qavg->bindTable(':table_reviews', TABLE_REVIEWS);
            $Qavg->bindInt(':products_id', $this->_data['id']);
            $Qavg->bindInt(':languages_id', $osC_Language->getID());
            $Qavg->execute();

            $this->_data['reviews_average_rating'] = round($Qavg->value('rating'));
          }
        }
      }
    }

    function isValid() {
      if (empty($this->_data)) {
        return false;
      }

      return true;
    }

    function getData($key) {
      if (isset($this->_data[$key])) {
        return $this->_data[$key];
      }

      return false;
    }

    function getID() {
      return $this->_data['id'];
    }

    function getTitle() {
      return $this->_data['name'];
    }

    function getDescription() {
      return $this->_data['description'];
    }

    function hasModel() {
      return (isset($this->_data['model']) && !empty($this->_data['model']));
    }

    function getModel() {
      return $this->_data['model'];
    }

    function hasKeyword() {
      return (isset($this->_data['keyword']) && !empty($this->_data['keyword']));
    }

    function getKeyword() {
      return $this->_data['keyword'];
    }

    function hasTags() {
      return (isset($this->_data['tags']) && !empty($this->_data['tags']));
    }

    function getTags() {
      return $this->_data['tags'];
    }

    function getPrice() {
    }

    function getPriceFormated($with_special = false) {
      global $osC_Services, $osC_Specials, $osC_Currencies;

      if (($with_special === true) && $osC_Services->isStarted('specials') && ($new_price = $osC_Specials->getPrice($this->_data['id']))) {
        $price = '<s>' . $osC_Currencies->displayPrice($this->_data['price'], $this->_data['tax_class_id']) . '</s> <span class="productSpecialPrice">' . $osC_Currencies->displayPrice($new_price, $this->_data['tax_class_id']) . '</span>';
      } else {
        $price = $osC_Currencies->displayPrice($this->_data['price'], $this->_data['tax_class_id']);
      }

      return $price;
    }

    function getCategoryID() {
      return $this->_data['category_id'];
    }

    function getImages() {
      return $this->_data['images'];
    }

    function hasImage() {
      foreach ($this->_data['images'] as $image) {
        if ($image['default_flag'] == '1') {
          return true;
        }
      }
    }

    function getImage() {
      foreach ($this->_data['images'] as $image) {
        if ($image['default_flag'] == '1') {
          return $image['image'];
        }
      }
    }

    function hasURL() {
      return (isset($this->_data['url']) && !empty($this->_data['url']));
    }

    function getURL() {
      return $this->_data['url'];
    }

    function getDateAvailable() {
      return $this->_data['date_available'];
    }

    function getDateAdded() {
      return $this->_data['date_added'];
    }

    function hasAttributes() {
      return (isset($this->_data['attributes']) && !empty($this->_data['attributes']));
    }

    function &getAttributes() {
      global $osC_Currencies;

      $array = array();

      foreach ($this->_data['attributes'] as $attribute) {
        if (!isset($array[$attribute['options_id']])) {
          $array[$attribute['options_id']] = array('options_name' => $attribute['options_name'],
                                                   'values' => array(),
                                                   'data' => array());
        }

        $array[$attribute['options_id']]['values'][] = array('options_values_id' => $attribute['options_values_id'],
                                                             'options_values_name' => $attribute['options_values_name'],
                                                             'options_values_price' => $attribute['options_values_price'],
                                                             'price_prefix' => $attribute['price_prefix']);

        $array[$attribute['options_id']]['data'][] = array('id' => $attribute['options_values_id'],
                                                           'text' => $attribute['options_values_name'] . ($attribute['options_values_price'] != '0' ? ' (' . $attribute['price_prefix'] . $osC_Currencies->displayPrice($attribute['options_values_price'], $this->_data['tax_class_id']) . ')' : ''));
      }

      return $array;
    }

    function checkEntry($id) {
      global $osC_Database;

      $Qcheck = $osC_Database->query('select p.products_id from :table_products p');
      $Qcheck->bindTable(':table_products', TABLE_PRODUCTS);

      if (is_numeric($id) || ereg('[0-9]+[{[0-9]+}[0-9]+]*$', $id)) {
        $Qcheck->appendQuery('where p.products_id = :products_id');
        $Qcheck->bindInt(':products_id', osc_get_product_id($id));
      } else {
        $Qcheck->appendQuery(', :table_products_description pd where pd.products_keyword = :products_keyword and pd.products_id = p.products_id');
        $Qcheck->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
        $Qcheck->bindValue(':products_keyword', $id);
      }

      $Qcheck->appendQuery('and p.products_status = 1 limit 1');
      $Qcheck->execute();

      if ($Qcheck->numberOfRows() === 1) {
        return true;
      }

      return false;
    }

    function incrementCounter() {
      global $osC_Database, $osC_Language;

      $Qupdate = $osC_Database->query('update :table_products_description set products_viewed = products_viewed+1 where products_id = :products_id and language_id = :language_id');
      $Qupdate->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
      $Qupdate->bindInt(':products_id', osc_get_product_id($this->_data['id']));
      $Qupdate->bindInt(':language_id', $osC_Language->getID());
      $Qupdate->execute();
    }

    function numberOfImages() {
      return sizeof($this->_data['images']);
    }

    function &getListingNew() {
      global $osC_Database, $osC_Language, $osC_Image;

      $Qproducts = $osC_Database->query('select p.products_id, p.products_price, p.products_tax_class_id, p.products_date_added, pd.products_name, pd.products_keyword, m.manufacturers_name, i.image from :table_products p left join :table_manufacturers m on (p.manufacturers_id = m.manufacturers_id) left join :table_products_images i on (p.products_id = i.products_id and i.default_flag = :default_flag), :table_products_description pd where p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id order by p.products_date_added desc, pd.products_name');
      $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
      $Qproducts->bindTable(':table_manufacturers', TABLE_MANUFACTURERS);
      $Qproducts->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
      $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
      $Qproducts->bindInt(':default_flag', 1);
      $Qproducts->bindInt(':language_id', $osC_Language->getID());
      $Qproducts->setBatchLimit($_GET['page'], MAX_DISPLAY_PRODUCTS_NEW);
      $Qproducts->execute();

      return $Qproducts;
    }
  }
?>

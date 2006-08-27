<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_ShoppingCart {
    var $_contents = array(),
        $_sub_total = 0,
        $_total = 0,
        $_weight = 0,
        $_tax = 0,
        $_tax_groups = array(),
        $_cartID,
        $_content_type,
        $_products_in_stock = true;

    function osC_ShoppingCart() {
      if (!isset($_SESSION['osC_ShoppingCart_data'])) {
        $_SESSION['osC_ShoppingCart_data'] = array('contents' => array(),
                                                   'sub_total_cost' => 0,
                                                   'total_cost' => 0,
                                                   'total_weight' => 0,
                                                   'tax' => 0,
                                                   'tax_groups' => array(),
                                                   'shipping_boxes_weight' => 0,
                                                   'shipping_boxes' => 1,
                                                   'cart_id' => $this->generateCartID(),
                                                   'shipping_address' => array('zone_id' => STORE_ZONE, 'country_id' => STORE_COUNTRY),
                                                   'shipping_method' => array(),
                                                   'billing_address' => array('zone_id' => STORE_ZONE, 'country_id' => STORE_COUNTRY),
                                                   'billing_method' => array(),
                                                   'order_totals' => array());

        $this->resetShippingAddress();
        $this->resetBillingAddress();
      }

      $this->_contents =& $_SESSION['osC_ShoppingCart_data']['contents'];
      $this->_sub_total =& $_SESSION['osC_ShoppingCart_data']['sub_total_cost'];
      $this->_total =& $_SESSION['osC_ShoppingCart_data']['total_cost'];
      $this->_weight =& $_SESSION['osC_ShoppingCart_data']['total_weight'];
      $this->_tax =& $_SESSION['osC_ShoppingCart_data']['tax'];
      $this->_tax_groups =& $_SESSION['osC_ShoppingCart_data']['tax_groups'];
      $this->_shipping_boxes_weight =& $_SESSION['osC_ShoppingCart_data']['shipping_boxes_weight'];
      $this->_shipping_boxes =& $_SESSION['osC_ShoppingCart_data']['shipping_boxes'];
      $this->_cartID =& $_SESSION['osC_ShoppingCart_data']['cart_id'];
      $this->_shipping_address =& $_SESSION['osC_ShoppingCart_data']['shipping_address'];
      $this->_shipping_method =& $_SESSION['osC_ShoppingCart_data']['shipping_method'];
      $this->_billing_address =& $_SESSION['osC_ShoppingCart_data']['billing_address'];
      $this->_billing_method =& $_SESSION['osC_ShoppingCart_data']['billing_method'];
      $this->_order_totals =& $_SESSION['osC_ShoppingCart_data']['order_totals'];
    }

    function hasContents() {
      return !empty($this->_contents);
    }

    function synchronizeWithDatabase() {
      global $osC_Database, $osC_Services, $osC_Language, $osC_Customer, $osC_Image;

      if (!$osC_Customer->isLoggedOn()) {
        return false;
      }

// insert current cart contents in database
      if ($this->hasContents()) {
        foreach ($this->_contents as $products_id_string => $data) {
          $Qproduct = $osC_Database->query('select products_id, customers_basket_quantity from :table_customers_basket where customers_id = :customers_id and products_id = :products_id');
          $Qproduct->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
          $Qproduct->bindInt(':customers_id', $osC_Customer->getID());
          $Qproduct->bindValue(':products_id', $products_id_string);
          $Qproduct->execute();

          if ($Qproduct->numberOfRows() > 0) {
            $Qupdate = $osC_Database->query('update :table_customers_basket set customers_basket_quantity = :customers_basket_quantity where customers_id = :customers_id and products_id = :products_id');
            $Qupdate->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
            $Qupdate->bindInt(':customers_basket_quantity', $data['quantity'] + $Qproduct->valueInt('customers_basket_quantity'));
            $Qupdate->bindInt(':customers_id', $osC_Customer->getID());
            $Qupdate->bindValue(':products_id', $products_id_string);
            $Qupdate->execute();
          } else {
            $Qnew = $osC_Database->query('insert into :table_customers_basket (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) values (:customers_id, :products_id, :customers_basket_quantity, now())');
            $Qnew->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
            $Qnew->bindInt(':customers_id', $osC_Customer->getID());
            $Qnew->bindValue(':products_id', $products_id_string);
            $Qnew->bindInt(':customers_basket_quantity', $data['quantity']);
            $Qnew->execute();
          }
        }
      }

// reset per-session cart contents, but not the database contents
      $this->reset();

      $Qproducts = $osC_Database->query('select cb.products_id, cb.customers_basket_quantity, cb.customers_basket_date_added, p.products_price, p.products_tax_class_id, p.products_weight, p.products_weight_class, pd.products_name, pd.products_keyword, i.image from :table_customers_basket cb, :table_products p left join :table_products_images i on (p.products_id = i.products_id and i.default_flag = :default_flag), :table_products_description pd where cb.customers_id = :customers_id and cb.products_id = p.products_id and p.products_id = pd.products_id and pd.language_id = :language_id order by cb.customers_basket_date_added desc');
      $Qproducts->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
      $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
      $Qproducts->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
      $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
      $Qproducts->bindInt(':default_flag', 1);
      $Qproducts->bindInt(':customers_id', $osC_Customer->getID());
      $Qproducts->bindInt(':language_id', $osC_Language->getID());
      $Qproducts->execute();

      while ($Qproducts->next()) {
        $product = explode('#', $Qproducts->value('products_id'), 2);
        $attributes_array = array();

        if (isset($product[1])) {
          $attributes = explode(';', $product[1]);

          foreach ($attributes as $set) {
            $attribute = explode(':', $set);

            if (!is_numeric($attribute[0]) || !is_numeric($attribute[1])) {
              continue 2; // skip product
            }

            $attributes_array[$attribute[0]] = $attribute[1];
          }
        }

        $price = $Qproducts->value('products_price');

        if ($osC_Services->isStarted('specials')) {
          global $osC_Specials;

          if ($new_price = $osC_Specials->getPrice(osc_get_product_id($Qproducts->value('products_id')))) {
            $price = $new_price;
          }
        }

        $this->_contents[$Qproducts->value('products_id')] = array('id' => $Qproducts->value('products_id'),
                                                                   'name' => $Qproducts->value('products_name'),
                                                                   'keyword' => $Qproducts->value('products_keyword'),
                                                                   'image' => $Qproducts->value('image'),
                                                                   'price' => $price,
                                                                   'final_price' => $price,
                                                                   'quantity' => $Qproducts->valueInt('customers_basket_quantity'),
                                                                   'weight' => $Qproducts->value('products_weight'),
                                                                   'tax_class_id' => $Qproducts->valueInt('products_tax_class_id'),
                                                                   'date_added' => osC_DateTime::getShort($Qproducts->value('customers_basket_date_added')),
                                                                   'weight_class_id' => $Qproducts->valueInt('products_weight_class'));

        if (!empty($attributes_array)) {
          foreach ($attributes_array as $option_id => $value_id) {
            $Qattributes = $osC_Database->query('select pa.options_values_price, pa.price_prefix, po.products_options_name, pov.products_options_values_name from :table_products_attributes pa, :table_products_options po, :table_products_options_values pov where pa.products_id = :products_id and pa.options_id = :options_id and pa.options_values_id = :options_values_id and pa.options_id = po.products_options_id and po.language_id = :language_id and pa.options_values_id = pov.products_options_values_id and pov.language_id = :language_id');
            $Qattributes->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
            $Qattributes->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
            $Qattributes->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
            $Qattributes->bindInt(':products_id', osc_get_product_id($Qproducts->value('products_id')));
            $Qattributes->bindInt(':options_id', $option_id);
            $Qattributes->bindInt(':options_values_id', $value_id);
            $Qattributes->bindInt(':language_id', $osC_Language->getID());
            $Qattributes->bindInt(':language_id', $osC_Language->getID());
            $Qattributes->execute();

            if ($Qattributes->numberOfRows() > 0) {
              $this->_contents[$Qproducts->value('products_id')]['attributes'][$option_id] = array('options_id' => $option_id,
                                                                                                   'options_values_id' => $value_id,
                                                                                                   'products_options_name' => $Qattributes->value('products_options_name'),
                                                                                                   'products_options_values_name' => $Qattributes->value('products_options_values_name'),
                                                                                                   'options_values_price' => $Qattributes->value('options_values_price'),
                                                                                                   'price_prefix' => $Qattributes->value('price_prefix'));

              if ($Qattributes->value('price_prefix') == '+') {
                $this->_contents[$Qproducts->value('products_id')]['final_price'] += $Qattributes->value('options_values_price');
              } else {
                $this->_contents[$Qproducts->value('products_id')]['final_price'] -= $Qattributes->value('options_values_price');
              }
            } else {
              unset($this->_contents[$Qproducts->value('products_id')]);
              continue 2; // skip product
            }
          }
        }
      }

      $this->_cleanUp();
      $this->_calculate();
    }

    function reset($reset_database = false) {
      global $osC_Database, $osC_Customer;

      if (($reset_database === true) && $osC_Customer->isLoggedOn()) {
        $Qdelete = $osC_Database->query('delete from :table_customers_basket where customers_id = :customers_id');
        $Qdelete->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
        $Qdelete->bindInt(':customers_id', $osC_Customer->getID());
        $Qdelete->execute();
      }

      $this->_contents = array();
      $this->_sub_total = 0;
      $this->_total = 0;
      $this->_weight = 0;
      $this->_tax = 0;
      $this->_tax_groups = array();
      $this->_cartID = $this->generateCartID();
      $this->_content_type = null;

      $this->resetShippingAddress();
      $this->resetShippingMethod();
      $this->resetBillingAddress();
      $this->resetBillingMethod();
    }

    function add($products_id_string, $attributes = null, $quantity = null) {
      global $osC_Database, $osC_Services, $osC_Language, $osC_Customer, $osC_Image;

      $products_id_string = osc_get_product_id_string($products_id_string, $attributes);
      $products_id = osc_get_product_id($products_id_string);

      if (is_numeric($products_id)) {
        $Qcheck = $osC_Database->query('select p.products_price, p.products_tax_class_id, p.products_weight, p.products_weight_class, p.products_status, i.image from :table_products p left join :table_products_images i on (p.products_id = i.products_id and i.default_flag = :default_flag) where p.products_id = :products_id');
        $Qcheck->bindTable(':table_products', TABLE_PRODUCTS);
        $Qcheck->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
        $Qcheck->bindInt(':default_flag', 1);
        $Qcheck->bindInt(':products_id', $products_id);
        $Qcheck->execute();

        if ($Qcheck->valueInt('products_status') === 1) {
          if ($this->exists($products_id_string)) {
            if (!is_numeric($quantity)) {
              $quantity = $this->getQuantity($products_id_string) + 1;
            }

            $this->_contents[$products_id_string]['quantity'] = $quantity;

// update database
            if ($osC_Customer->isLoggedOn()) {
              $Qupdate = $osC_Database->query('update :table_customers_basket set customers_basket_quantity = :customers_basket_quantity where customers_id = :customers_id and products_id = :products_id');
              $Qupdate->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
              $Qupdate->bindInt(':customers_basket_quantity', $quantity);
              $Qupdate->bindInt(':customers_id', $osC_Customer->getID());
              $Qupdate->bindValue(':products_id', $products_id_string);
              $Qupdate->execute();
            }
          } else {
            if (!is_numeric($quantity)) {
              $quantity = 1;
            }

            $Qproduct = $osC_Database->query('select products_name, products_keyword from :table_products_description where products_id = :products_id and language_id = :language_id');
            $Qproduct->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
            $Qproduct->bindInt(':products_id', $products_id);
            $Qproduct->bindInt(':language_id', $osC_Language->getID());
            $Qproduct->execute();

            $price = $Qcheck->value('products_price');

            if ($osC_Services->isStarted('specials')) {
              global $osC_Specials;

              if ($new_price = $osC_Specials->getPrice($products_id)) {
                $price = $new_price;
              }
            }

            $this->_contents[$products_id_string] = array('id' => $products_id_string,
                                                          'name' => $Qproduct->value('products_name'),
                                                          'keyword' => $Qproduct->value('products_keyword'),
                                                          'image' => $Qcheck->value('image'),
                                                          'price' => $price,
                                                          'final_price' => $price,
                                                          'quantity' => $quantity,
                                                          'weight' => $Qcheck->value('products_weight'),
                                                          'tax_class_id' => $Qcheck->valueInt('products_tax_class_id'),
                                                          'date_added' => osC_DateTime::getShort(osC_DateTime::getNow()),
                                                          'weight_class_id' => $Qcheck->valueInt('products_weight_class'));

// insert into database
            if ($osC_Customer->isLoggedOn()) {
              $Qnew = $osC_Database->query('insert into :table_customers_basket (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) values (:customers_id, :products_id, :customers_basket_quantity, now())');
              $Qnew->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
              $Qnew->bindInt(':customers_id', $osC_Customer->getID());
              $Qnew->bindValue(':products_id', $products_id_string);
              $Qnew->bindInt(':customers_basket_quantity', $quantity);
              $Qnew->execute();
            }

            if (is_array($attributes) && !empty($attributes)) {
              foreach ($attributes as $option => $value) {
                $Qattributes = $osC_Database->query('select pa.options_values_price, pa.price_prefix, po.products_options_name, pov.products_options_values_name from :table_products_attributes pa, :table_products_options po, :table_products_options_values pov where pa.products_id = :products_id and pa.options_id = :options_id and pa.options_values_id = :options_values_id and pa.options_id = po.products_options_id and po.language_id = :language_id and pa.options_values_id = pov.products_options_values_id and pov.language_id = :language_id');
                $Qattributes->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
                $Qattributes->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
                $Qattributes->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
                $Qattributes->bindValue(':products_id', $products_id);
                $Qattributes->bindInt(':options_id', $option);
                $Qattributes->bindInt(':options_values_id', $value);
                $Qattributes->bindInt(':language_id', $osC_Language->getID());
                $Qattributes->bindInt(':language_id', $osC_Language->getID());
                $Qattributes->execute();

                $this->_contents[$products_id_string]['attributes'][$option] = array('options_id' => $option,
                                                                                     'options_values_id' => $value,
                                                                                     'products_options_name' => $Qattributes->value('products_options_name'),
                                                                                     'products_options_values_name' => $Qattributes->value('products_options_values_name'),
                                                                                     'options_values_price' => $Qattributes->value('options_values_price'),
                                                                                     'price_prefix' => $Qattributes->value('price_prefix'));

                if ($Qattributes->value('price_prefix') == '+') {
                  $this->_contents[$products_id_string]['final_price'] += $Qattributes->value('options_values_price');
                } else {
                  $this->_contents[$products_id_string]['final_price'] -= $Qattributes->value('options_values_price');
                }
              }
            }
          }

          $this->_cleanUp();
          $this->_calculate();
        }
      }
    }

    function numberOfItems() {
      $total = 0;

      if ($this->hasContents()) {
        foreach (array_keys($this->_contents) as $products_id) {
          $total += $this->getQuantity($products_id);
        }
      }

      return $total;
    }

    function getQuantity($products_id) {
      if (isset($this->_contents[$products_id])) {
        return $this->_contents[$products_id]['quantity'];
      }

      return 0;
    }

    function exists($products_id) {
      return isset($this->_contents[$products_id]);
    }

    function remove($products_id) {
      global $osC_Database, $osC_Customer;

      unset($this->_contents[$products_id]);

// remove from database
      if ($osC_Customer->isLoggedOn()) {
        $Qdelete = $osC_Database->query('delete from :table_customers_basket where customers_id = :customers_id and products_id = :products_id');
        $Qdelete->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
        $Qdelete->bindInt(':customers_id', $osC_Customer->getID());
        $Qdelete->bindValue(':products_id', $products_id);
        $Qdelete->execute();
      }

      $this->_calculate();
    }

    function getProducts() {
      static $_is_sorted = false;

      if ($_is_sorted === false) {
        $_is_sorted = true;

        uasort($this->_contents, array('osC_ShoppingCart', '_uasortProductsByDateAdded'));
      }

      return $this->_contents;
    }

    function getSubTotal() {
      return $this->_sub_total;
    }

    function getTotal() {
      return $this->_total;
    }

    function getWeight() {
      return $this->_weight;
    }

    function generateCartID($length = 5) {
      return osc_create_random_string($length, 'digits');
    }

    function hasCartID() {
      return isset($this->_cartID);
    }

    function getCartID() {
      return $this->_cartID;
    }

    function getContentType() {
      global $osC_Database;

      $this->_content_type = 'physical';

      if ( (DOWNLOAD_ENABLED == '1') && $this->hasContents() ) {
        foreach ($this->_contents as $products_id => $data) {
          if (isset($data['attributes'])) {
            foreach ($data['attributes'] as $value) {
              $Qcheck = $osC_Database->query('select count(*) as total from :table_products_attributes pa, :table_products_attributes_download pad where pa.products_id = :products_id and pa.options_values_id = :options_values_id and pa.products_attributes_id = pad.products_attributes_id');
              $Qcheck->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
              $Qcheck->bindTable(':table_products_attributes_download', TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD);
              $Qcheck->bindInt(':products_id', $products_id);
              $Qcheck->bindInt(':options_values_id', $value['options_values_id']);
              $Qcheck->execute();

              if ($Qcheck->valueInt('total') > 0) {
                switch ($this->_content_type) {
                  case 'physical':
                    $this->_content_type = 'mixed';

                    return $this->_content_type;
                    break;
                  default:
                    $this->_content_type = 'virtual';
                    break;
                }
              } else {
                switch ($this->_content_type) {
                  case 'virtual':
                    $this->_content_type = 'mixed';

                    return $this->_content_type;
                    break;
                  default:
                    $this->_content_type = 'physical';
                    break;
                }
              }
            }
          } else {
            switch ($this->_content_type) {
              case 'virtual':
                $this->_content_type = 'mixed';

                return $this->_content_type;
                break;
              default:
                $this->_content_type = 'physical';
                break;
            }
          }
        }
      }

      return $this->_content_type;
    }

    function hasAttributes($products_id) {
      return isset($this->_contents[$products_id]['attributes']) && !empty($this->_contents[$products_id]['attributes']);
    }

    function getAttributes($products_id) {
      if (isset($this->_contents[$products_id]['attributes']) && !empty($this->_contents[$products_id]['attributes'])) {
        return $this->_contents[$products_id]['attributes'];
      }
    }

    function isInStock($products_id) {
      global $osC_Database;

      $Qstock = $osC_Database->query('select products_quantity from :table_products where products_id = :products_id');
      $Qstock->bindTable(':table_products', TABLE_PRODUCTS);
      $Qstock->bindInt(':products_id', osc_get_product_id($products_id));
      $Qstock->execute();

      if (($Qstock->valueInt('products_quantity') - $this->_contents[$products_id]['quantity']) > 0) {
        return true;
      } elseif ($this->_products_in_stock === true) {
        $this->_products_in_stock = false;
      }

      return false;
    }

    function hasStock() {
      return $this->_products_in_stock;
    }

    function hasShippingAddress() {
      return isset($this->_shipping_address) && isset($this->_shipping_address['id']);
    }

    function setShippingAddress($address_id) {
      global $osC_Database, $osC_Customer;

      $previous_address = false;

      if (isset($this->_shipping_address['id'])) {
        $previous_address = $this->getShippingAddress();
      }

      $Qaddress = $osC_Database->query('select ab.entry_firstname, ab.entry_lastname, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, ab.entry_telephone, z.zone_code, z.zone_name, ab.entry_country_id, c.countries_name, c.countries_iso_code_2, c.countries_iso_code_3, c.address_format, ab.entry_state from :table_address_book ab left join :table_zones z on (ab.entry_zone_id = z.zone_id) left join :table_countries c on (ab.entry_country_id = c.countries_id) where ab.customers_id = :customers_id and ab.address_book_id = :address_book_id');
      $Qaddress->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
      $Qaddress->bindTable(':table_zones', TABLE_ZONES);
      $Qaddress->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qaddress->bindInt(':customers_id', $osC_Customer->getID());
      $Qaddress->bindInt(':address_book_id', $address_id);
      $Qaddress->execute();

      $this->_shipping_address = array('id' => $address_id,
                                       'firstname' => $Qaddress->valueProtected('entry_firstname'),
                                       'lastname' => $Qaddress->valueProtected('entry_lastname'),
                                       'company' => $Qaddress->valueProtected('entry_company'),
                                       'street_address' => $Qaddress->valueProtected('entry_street_address'),
                                       'suburb' => $Qaddress->valueProtected('entry_suburb'),
                                       'city' => $Qaddress->valueProtected('entry_city'),
                                       'postcode' => $Qaddress->valueProtected('entry_postcode'),
                                       'state' => (!osc_empty($Qaddress->valueProtected('entry_state'))) ? $Qaddress->valueProtected('entry_state') : $Qaddress->valueProtected('zone_name'),
                                       'zone_id' => $Qaddress->valueInt('entry_zone_id'),
                                       'zone_code' => $Qaddress->value('zone_code'),
                                       'country_id' => $Qaddress->valueInt('entry_country_id'),
                                       'country_title' => $Qaddress->value('countries_name'),
                                       'country_iso_code_2' => $Qaddress->value('countries_iso_code_2'),
                                       'country_iso_code_3' => $Qaddress->value('countries_iso_code_3'),
                                       'format' => $Qaddress->value('address_format'),
                                       'telephone_number' => $Qaddress->value('entry_telephone'));

      if ( is_array($previous_address) && ( ($previous_address['id'] != $this->_shipping_address['id']) || ($previous_address['country_id'] != $this->_shipping_address['country_id']) || ($previous_address['zone_id'] != $this->_shipping_address['zone_id']) || ($previous_address['state'] != $this->_shipping_address['state']) || ($previous_address['postcode'] != $this->_shipping_address['postcode']) ) ) {
        $this->_calculate();
      }
    }

    function getShippingAddress($key = '') {
      if (empty($key)) {
        return $this->_shipping_address;
      }

      return $this->_shipping_address[$key];
    }

    function resetShippingAddress() {
      global $osC_Customer;

      $this->_shipping_address = array('zone_id' => STORE_ZONE, 'country_id' => STORE_COUNTRY);

      if ($osC_Customer->isLoggedOn() && $osC_Customer->hasDefaultAddress()) {
        $this->setShippingAddress($osC_Customer->getDefaultAddressID());
      }
    }

    function setShippingMethod($shipping_array, $calculate_total = true) {
      $this->_shipping_method = $shipping_array;

      if ($calculate_total === true) {
        $this->_calculate(false);
      }
    }

    function getShippingMethod($key = '') {
      if (empty($key)) {
        return $this->_shipping_method;
      }

      return $this->_shipping_method[$key];
    }

    function resetShippingMethod() {
      $this->_shipping_method = array();

      $this->_calculate();
    }

    function hasShippingMethod() {
      return !empty($this->_shipping_method);
    }

    function hasBillingAddress() {
      return isset($this->_billing_address) && isset($this->_billing_address['id']);
    }

    function setBillingAddress($address_id) {
      global $osC_Database, $osC_Customer;

      $previous_address = false;

      if (isset($this->_billing_address['id'])) {
        $previous_address = $this->getBillingAddress();
      }

      $Qaddress = $osC_Database->query('select ab.entry_firstname, ab.entry_lastname, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, ab.entry_telephone, z.zone_code, z.zone_name, ab.entry_country_id, c.countries_name, c.countries_iso_code_2, c.countries_iso_code_3, c.address_format, ab.entry_state from :table_address_book ab left join :table_zones z on (ab.entry_zone_id = z.zone_id) left join :table_countries c on (ab.entry_country_id = c.countries_id) where ab.customers_id = :customers_id and ab.address_book_id = :address_book_id');
      $Qaddress->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
      $Qaddress->bindTable(':table_zones', TABLE_ZONES);
      $Qaddress->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qaddress->bindInt(':customers_id', $osC_Customer->getID());
      $Qaddress->bindInt(':address_book_id', $address_id);
      $Qaddress->execute();

      $this->_billing_address = array('id' => $address_id,
                                      'firstname' => $Qaddress->valueProtected('entry_firstname'),
                                      'lastname' => $Qaddress->valueProtected('entry_lastname'),
                                      'company' => $Qaddress->valueProtected('entry_company'),
                                      'street_address' => $Qaddress->valueProtected('entry_street_address'),
                                      'suburb' => $Qaddress->valueProtected('entry_suburb'),
                                      'city' => $Qaddress->valueProtected('entry_city'),
                                      'postcode' => $Qaddress->valueProtected('entry_postcode'),
                                      'state' => (!osc_empty($Qaddress->valueProtected('entry_state'))) ? $Qaddress->valueProtected('entry_state') : $Qaddress->valueProtected('zone_name'),
                                      'zone_id' => $Qaddress->valueInt('entry_zone_id'),
                                      'zone_code' => $Qaddress->value('zone_code'),
                                      'country_id' => $Qaddress->valueInt('entry_country_id'),
                                      'country_title' => $Qaddress->value('countries_name'),
                                      'country_iso_code_2' => $Qaddress->value('countries_iso_code_2'),
                                      'country_iso_code_3' => $Qaddress->value('countries_iso_code_3'),
                                      'format' => $Qaddress->value('address_format'),
                                      'telephone_number' => $Qaddress->value('entry_telephone'));

      if ( is_array($previous_address) && ( ($previous_address['id'] != $this->_billing_address['id']) || ($previous_address['country_id'] != $this->_billing_address['country_id']) || ($previous_address['zone_id'] != $this->_billing_address['zone_id']) || ($previous_address['state'] != $this->_billing_address['state']) || ($previous_address['postcode'] != $this->_billing_address['postcode']) ) ) {
        $this->_calculate();
      }
    }

    function getBillingAddress($key = '') {
      if (empty($key)) {
        return $this->_billing_address;
      }

      return $this->_billing_address[$key];
    }

    function resetBillingAddress() {
      global $osC_Customer;

      $this->_billing_address = array('zone_id' => STORE_ZONE, 'country_id' => STORE_COUNTRY);

      if ($osC_Customer->isLoggedOn() && $osC_Customer->hasDefaultAddress()) {
        $this->setBillingAddress($osC_Customer->getDefaultAddressID());
      }
    }

    function setBillingMethod($billing_array) {
      $this->_billing_method = $billing_array;

      $this->_calculate();
    }

    function getBillingMethod($key = '') {
      if (empty($key)) {
        return $this->_billing_method;
      }

      return $this->_billing_method[$key];
    }

    function resetBillingMethod() {
      $this->_billing_method = array();

      $this->_calculate();
    }

    function hasBillingMethod() {
      return !empty($this->_billing_method);
    }

    function getTaxingAddress($id = '') {
      if ($this->getContentType() == 'virtual') {
        return $this->getBillingAddress($id);
      }

      return $this->getShippingAddress($id);
    }

    function addTaxAmount($amount) {
      $this->_tax += $amount;
    }

    function addTaxGroup($group, $amount) {
      if (isset($this->_tax_groups[$group])) {
        $this->_tax_groups[$group] += $amount;
      } else {
        $this->_tax_groups[$group] = $amount;
      }
    }

    function addToTotal($amount) {
      $this->_total += $amount;
    }

    function getOrderTotals() {
      return $this->_order_totals;
    }

    function getShippingBoxesWeight() {
      return $this->_shipping_boxes_weight;
    }

    function numberOfShippingBoxes() {
      return $this->_shipping_boxes;
    }

    function _cleanUp() {
      global $osC_Database, $osC_Customer;

      foreach ($this->_contents as $products_id => $data) {
        if ($data['quantity'] < 1) {
          unset($this->_contents[$products_id]);

// remove from database
          if ($osC_Customer->isLoggedOn()) {
            $Qdelete = $osC_Database->query('delete from :table_customers_basket where customers_id = :customers_id and products_id = :products_id');
            $Qdelete->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
            $Qdelete->bindInt(':customers_id', $osC_Customer->getID());
            $Qdelete->bindValue(':products_id', $products_id);
            $Qdelete->execute();
          }
        }
      }
    }

    function _calculate($set_shipping = true) {
      global $osC_Currencies, $osC_Tax, $osC_Weight, $osC_Shipping, $osC_OrderTotal;

      $this->_sub_total = 0;
      $this->_total = 0;
      $this->_weight = 0;
      $this->_tax = 0;
      $this->_tax_groups = array();
      $this->_cartID = $this->generateCartID();

      if ($this->hasContents()) {
        foreach ($this->_contents as $data) {
          $products_weight = $osC_Weight->convert($data['weight'], $data['weight_class_id'], SHIPPING_WEIGHT_UNIT);
          $this->_weight += $products_weight * $data['quantity'];

          $tax = $osC_Tax->getTaxRate($data['tax_class_id'], $this->getTaxingAddress('country_id'), $this->getTaxingAddress('zone_id'));
          $tax_description = $osC_Tax->getTaxRateDescription($data['tax_class_id'], $this->getTaxingAddress('country_id'), $this->getTaxingAddress('zone_id'));

          $shown_price = $osC_Currencies->addTaxRateToPrice($data['final_price'], $tax, $data['quantity']);
          $this->_sub_total += $shown_price;
          $this->_total += $shown_price;

          if (DISPLAY_PRICE_WITH_TAX == '1') {
            $tax_amount = $shown_price - ($shown_price / (($tax < 10) ? '1.0' . str_replace('.', '', $tax) : '1.' . str_replace('.', '', $tax)));
          } else {
            $tax_amount = ($tax / 100) * $shown_price;
          }

          $this->_tax += $tax_amount;

          if (isset($this->_tax_groups[$tax_description])) {
            $this->_tax_groups[$tax_description] += $tax_amount;
          } else {
            $this->_tax_groups[$tax_description] = $tax_amount;
          }
        }
      }

      $this->_shipping_boxes_weight = $this->_weight;
      $this->_shipping_boxes = 1;

      if (SHIPPING_BOX_WEIGHT >= ($this->_shipping_boxes_weight * SHIPPING_BOX_PADDING/100)) {
        $this->_shipping_boxes_weight = $this->_shipping_boxes_weight + SHIPPING_BOX_WEIGHT;
      } else {
        $this->_shipping_boxes_weight = $this->_shipping_boxes_weight + ($this->_shipping_boxes_weight * SHIPPING_BOX_PADDING/100);
      }

      if ($this->_shipping_boxes_weight > SHIPPING_MAX_WEIGHT) { // Split into many boxes
        $this->_shipping_boxes = ceil($this->_shipping_boxes_weight / SHIPPING_MAX_WEIGHT);
        $this->_shipping_boxes_weight = $this->_shipping_boxes_weight / $this->_shipping_boxes;
      }

      if ($set_shipping === true) {
        if (!class_exists('osC_Shipping')) {
          include('includes/classes/shipping.php');
        }

        if (!$this->hasShippingMethod() || ($this->getShippingMethod('is_cheapest') === true)) {
          $osC_Shipping = new osC_Shipping();
          $this->setShippingMethod($osC_Shipping->getCheapestQuote(), false);
        } else {
          $osC_Shipping = new osC_Shipping($this->getShippingMethod('id'));
          $this->setShippingMethod($osC_Shipping->getQuote(), false);
        }
      }

      if (!class_exists('osC_OrderTotal')) {
        include('includes/classes/order_total.php');
      }

      $osC_OrderTotal = new osC_OrderTotal();
      $this->_order_totals = $osC_OrderTotal->getResult();
    }

    function _uasortProductsByDateAdded($a, $b) {
      if ($a['date_added'] == $b['date_added']) {
        return strnatcasecmp($a['name'], $b['name']);
      }

      return ($a['date_added'] > $b['date_added']) ? -1 : 1;
    }
  }
?>

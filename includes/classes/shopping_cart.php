<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class shoppingCart {
    var $contents, $total, $weight, $cartID, $content_type;

    function shoppingCart() {
      $this->reset();
    }

    function restore_contents() {
      global $osC_Database, $osC_Customer;

      if ($osC_Customer->isLoggedOn() === false) return false;

// insert current cart contents in database
      if (is_array($this->contents)) {
        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
          $qty = $this->contents[$products_id]['qty'];

          $Qproduct = $osC_Database->query('select products_id from :table_customers_basket where customers_id = :customers_id and products_id = :products_id');
          $Qproduct->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
          $Qproduct->bindInt(':customers_id', $osC_Customer->getID());
          $Qproduct->bindValue(':products_id', $products_id);
          $Qproduct->execute();

          if ($Qproduct->numberOfRows() < 1) {
            $Qnew = $osC_Database->query('insert into :table_customers_basket (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) values (:customers_id, :products_id, :customers_basket_quantity, :customers_basket_date_added)');
            $Qnew->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
            $Qnew->bindInt(':customers_id', $osC_Customer->getID());
            $Qnew->bindValue(':products_id', $products_id);
            $Qnew->bindInt(':customers_basket_quantity', $qty);
            $Qnew->bindValue(':customers_basket_date_added', date('Ymd'));
            $Qnew->execute();

            if (isset($this->contents[$products_id]['attributes'])) {
              reset($this->contents[$products_id]['attributes']);
              while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
                $Qnew = $osC_Database->query('insert into :table_customers_basket_attributes (customers_id, products_id, products_options_id, products_options_value_id) values (:customers_id, :products_id, :products_options_id, :products_options_value_id)');
                $Qnew->bindTable(':table_customers_basket_attributes', TABLE_CUSTOMERS_BASKET_ATTRIBUTES);
                $Qnew->bindInt(':customers_id', $osC_Customer->getID());
                $Qnew->bindValue(':products_id', $products_id);
                $Qnew->bindInt(':products_options_id', $option);
                $Qnew->bindInt(':products_options_value_id', $value);
                $Qnew->execute();
              }
            }
          } else {
            $Qupdate = $osC_Database->query('update :table_customers_basket set customers_basket_quantity = :customers_basket_quantity where customers_id = :customers_id and products_id = :products_id');
            $Qupdate->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
            $Qupdate->bindInt(':customers_basket_quantity', $qty);
            $Qupdate->bindInt(':customers_id', $osC_Customer->getID());
            $Qupdate->bindValue(':products_id', $products_id);
            $Qupdate->execute();
          }
        }
      }

// reset per-session cart contents, but not the database contents
      $this->reset(false);

      $Qproducts = $osC_Database->query('select products_id, customers_basket_quantity from :table_customers_basket where customers_id = :customers_id');
      $Qproducts->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
      $Qproducts->bindInt(':customers_id', $osC_Customer->getID());
      $Qproducts->execute();

      while ($Qproducts->next()) {
        $this->contents[$Qproducts->value('products_id')] = array('qty' => $Qproducts->valueInt('customers_basket_quantity'));
// attributes
        $Qattributes = $osC_Database->query('select products_options_id, products_options_value_id from :table_customers_basket_attributes where customers_id = :customers_id and products_id = :products_id');
        $Qattributes->bindTable(':table_customers_basket_attributes', TABLE_CUSTOMERS_BASKET_ATTRIBUTES);
        $Qattributes->bindInt(':customers_id', $osC_Customer->getID());
        $Qattributes->bindValue(':products_id', $Qproducts->value('products_id'));
        $Qattributes->execute();

        while ($Qattributes->next()) {
          $this->contents[$Qproducts->value('products_id')]['attributes'][$Qattributes->valueInt('products_options_id')] = $Qattributes->valueInt('products_options_value_id');
        }
      }

      $this->cleanup();
    }

    function reset($reset_database = false) {
      global $osC_Database, $osC_Customer;

      $this->contents = array();
      $this->total = 0;
      $this->weight = 0;
      $this->content_type = false;

      if (($reset_database == true) && $osC_Customer->isLoggedOn()) {
        $Qdelete = $osC_Database->query('delete from :table_customers_basket where customers_id = :customers_id');
        $Qdelete->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
        $Qdelete->bindInt(':customers_id', $osC_Customer->getID());
        $Qdelete->execute();

        $Qdelete = $osC_Database->query('delete from :table_customers_basket_attributes where customers_id = :customers_id');
        $Qdelete->bindTable(':table_customers_basket_attributes', TABLE_CUSTOMERS_BASKET_ATTRIBUTES);
        $Qdelete->bindInt(':customers_id', $osC_Customer->getID());
        $Qdelete->execute();
      }

      unset($this->cartID);
      unset($_SESSION['cartID']);
    }

    function add_cart($products_id, $qty = '1', $attributes = '', $notify = true) {
      global $osC_Database, $osC_Customer;

      $products_id_string = tep_get_uprid($products_id, $attributes);
      $products_id = tep_get_prid($products_id_string);

      if (is_numeric($products_id) && is_numeric($qty)) {
        $Qcheck = $osC_Database->query('select products_status from :table_products where products_id = :products_id');
        $Qcheck->bindTable(':table_products', TABLE_PRODUCTS);
        $Qcheck->bindInt(':products_id', $products_id);
        $Qcheck->execute();

        if (($check_product !== false) && ($Qcheck->valueInt('products_status') == '1')) {
          if ($notify == true) {
            $_SESSION['new_products_id_in_cart'] = $products_id_string;
          }

          if ($this->in_cart($products_id_string)) {
            $this->update_quantity($products_id_string, $qty, $attributes);
          } else {
            $this->contents[$products_id_string] = array('qty' => $qty);
// insert into database
            if ($osC_Customer->isLoggedOn()) {
              $Qnew = $osC_Database->query('insert into :table_customers_basket (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) values (:customers_id, :products_id, :customers_basket_quantity, :customers_basket_date_added)');
              $Qnew->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
              $Qnew->bindInt(':customers_id', $osC_Customer->getID());
              $Qnew->bindValue(':products_id', $products_id_string);
              $Qnew->bindInt(':customers_basket_quantity', $qty);
              $Qnew->bindValue(':customers_basket_date_added', date('Ymd'));
              $Qnew->execute();
            }

            if (is_array($attributes)) {
              reset($attributes);
              while (list($option, $value) = each($attributes)) {
                $this->contents[$products_id_string]['attributes'][$option] = $value;
// insert into database
                if ($osC_Customer->isLoggedOn()) {
                  $Qnew = $osC_Database->query('insert into :table_customers_basket_attributes (customers_id, products_id, products_options_id, products_options_value_id) values (:customers_id, :products_id, :products_options_id, :products_options_value_id)');
                  $Qnew->bindTable(':table_customers_basket_attributes', TABLE_CUSTOMERS_BASKET_ATTRIBUTES);
                  $Qnew->bindInt(':customers_id', $osC_Customer->getID());
                  $Qnew->bindValue(':products_id', $products_id_string);
                  $Qnew->bindInt(':products_options_id', $option);
                  $Qnew->bindInt(':products_options_value_id', $value);
                  $Qnew->execute();
                }
              }
            }
          }

          $this->cleanup();

// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
          $this->cartID = $this->generate_cart_id();
        }
      }
    }

    function update_quantity($products_id, $quantity = '', $attributes = '') {
      global $osC_Database, $osC_Customer;

      $products_id_string = tep_get_uprid($products_id, $attributes);
      $products_id = tep_get_prid($products_id_string);

      if (is_numeric($products_id) && isset($this->contents[$products_id_string]) && is_numeric($quantity)) {
        $this->contents[$products_id_string] = array('qty' => $quantity);
// update database
        if ($osC_Customer->isLoggedOn()) {
          $Qupdate = $osC_Database->query('update :table_customers_basket set customers_basket_quantity = :customers_basket_quantity where customers_id = :customers_id and products_id = :products_id');
          $Qupdate->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
          $Qupdate->bindInt(':customers_basket_quantity', $quantity);
          $Qupdate->bindInt(':customers_id', $osC_Customer->getID());
          $Qupdate->bindValue(':products_id', $products_id_string);
          $Qupdate->execute();
        }

        if (is_array($attributes)) {
          reset($attributes);
          while (list($option, $value) = each($attributes)) {
            $this->contents[$products_id_string]['attributes'][$option] = $value;
// update database
            if ($osC_Customer->isLoggedOn()) {
              $Qupdate = $osC_Database->query('update :table_customers_basket_attributes set products_options_value_id = :products_options_value_id where customers_id = :customers_id and products_id = :products_id and products_options_id = :products_options_id');
              $Qupdate->bindTable(':table_customers_basket_attributes', TABLE_CUSTOMERS_BASKET_ATTRIBUTES);
              $Qupdate->bindInt(':products_options_value_id', $value);
              $Qupdate->bindInt(':customers_id', $osC_Customer->getID());
              $Qupdate->bindValue(':products_id', $products_id_string);
              $Qupdate->bindInt(':products_options_id', $option);
              $Qupdate->execute();
            }
          }
        }
      }
    }

    function cleanup() {
      global $osC_Database, $osC_Customer;

      reset($this->contents);
      while (list($key,) = each($this->contents)) {
        if ($this->contents[$key]['qty'] < 1) {
          unset($this->contents[$key]);
// remove from database
          if ($osC_Customer->isLoggedOn()) {
            $Qdelete = $osC_Database->query('delete from :table_customers_basket where customers_id = :customers_id and products_id = :products_id');
            $Qdelete->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
            $Qdelete->bindInt(':customers_id', $osC_Customer->getID());
            $Qdelete->bindValue(':products_id', $key);
            $Qdelete->execute();

            $Qdelete = $osC_Database->query('delete from :table_customers_basket_attributes where customers_id = :customers_id and products_id = :products_id');
            $Qdelete->bindTable(':table_customers_basket_attributes', TABLE_CUSTOMERS_BASKET_ATTRIBUTES);
            $Qdelete->bindInt(':customers_id', $osC_Customer->getID());
            $Qdelete->bindValue(':products_id', $key);
            $Qdelete->execute();
          }
        }
      }
    }

    function count_contents() {  // get total number of items in cart
      $total_items = 0;
      if (is_array($this->contents)) {
        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
          $total_items += $this->get_quantity($products_id);
        }
      }

      return $total_items;
    }

    function get_quantity($products_id) {
      if (isset($this->contents[$products_id])) {
        return $this->contents[$products_id]['qty'];
      } else {
        return 0;
      }
    }

    function in_cart($products_id) {
      if (isset($this->contents[$products_id])) {
        return true;
      } else {
        return false;
      }
    }

    function remove($products_id) {
      global $osC_Database, $osC_Customer;

      unset($this->contents[$products_id]);
// remove from database
      if ($osC_Customer->isLoggedOn()) {
        $Qdelete = $osC_Database->query('delete from :table_customers_basket where customers_id = :customers_id and products_id = :products_id');
        $Qdelete->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
        $Qdelete->bindInt(':customers_id', $osC_Customer->getID());
        $Qdelete->bindValue(':products_id', $products_id);
        $Qdelete->execute();

        $Qdelete = $osC_Database->query('delete from :table_customers_basket_attributes where customers_id = :customers_id and products_id = :products_id');
        $Qdelete->bindTable(':table_customers_basket_attributes', TABLE_CUSTOMERS_BASKET_ATTRIBUTES);
        $Qdelete->bindInt(':customers_id', $osC_Customer->getID());
        $Qdelete->bindValue(':products_id', $products_id);
        $Qdelete->execute();
      }

// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
      $this->cartID = $this->generate_cart_id();
    }

    function remove_all() {
      $this->reset();
    }

    function get_product_id_list() {
      $product_id_list = '';
      if (is_array($this->contents)) {
        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
          $product_id_list .= ', ' . $products_id;
        }
      }

      return substr($product_id_list, 2);
    }

    function calculate() {
      global $osC_Database, $osC_Tax, $osC_Weight;

      $this->total = 0;
      $this->weight = 0;
      if (!is_array($this->contents)) return 0;

      reset($this->contents);
      while (list($products_id, ) = each($this->contents)) {
        $qty = $this->contents[$products_id]['qty'];

// products price
        $Qproduct = $osC_Database->query('select products_id, products_price, products_tax_class_id, products_weight, products_weight_class from :table_products where products_id = :products_id');
        $Qproduct->bindTable(':table_products', TABLE_PRODUCTS);
        $Qproduct->bindInt(':products_id', $products_id);
        $Qproduct->execute();

        if ($Qproduct->numberOfRows()) {
          $prid = $Qproduct->valueInt('products_id');
          $products_tax = $osC_Tax->getTaxRate($Qproduct->valueInt('products_tax_class_id'));
          $products_price = $Qproduct->value('products_price');

          $products_weight = $osC_Weight->convert($Qproduct->value('products_weight'), $Qproduct->valueInt('products_weight_class'), SHIPPING_WEIGHT_UNIT);

          $Qspecials = $osC_Database->query('select specials_new_products_price from :table_specials where products_id = :products_id and status = :status');
          $Qspecials->bindTable(':table_specials', TABLE_SPECIALS);
          $Qspecials->bindInt(':products_id', $prid);
          $Qspecials->bindInt(':status', 1);
          $Qspecials->execute();

          if ($Qspecials->numberOfRows()) {
            $products_price = $Qspecials->value('specials_new_products_price');
          }

          $this->total += tep_add_tax($products_price, $products_tax) * $qty;
          $this->weight += ($qty * $products_weight);
        }

// attributes price
        if (isset($this->contents[$products_id]['attributes'])) {
          reset($this->contents[$products_id]['attributes']);
          while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
            $Qattributes = $osC_Database->query('select options_values_price, price_prefix from :table_products_attributes where products_id = :products_id and options_id = :options_id and options_values_id = :options_values_id');
            $Qattributes->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
            $Qattributes->bindInt(':products_id', $prid);
            $Qattributes->bindInt(':options_id', $option);
            $Qattributes->bindInt(':options_values_id', $value);
            $Qattributes->execute();

            if ($Qattributes->value('price_prefix') == '+') {
              $this->total += $qty * tep_add_tax($Qattributes->value('options_values_price'), $products_tax);
            } else {
              $this->total -= $qty * tep_add_tax($Qattributes->value('options_values_price'), $products_tax);
            }
          }
        }
      }
    }

    function attributes_price($products_id) {
      global $osC_Database;

      $attributes_price = 0;

      if (isset($this->contents[$products_id]['attributes'])) {
        reset($this->contents[$products_id]['attributes']);
        while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
          $Qattributes = $osC_Database->query('select options_values_price, price_prefix from :table_products_attributes where products_id = :products_id and options_id = :options_id and options_values_id = :options_values_id');
          $Qattributes->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
          $Qattributes->bindInt(':products_id', $products_id);
          $Qattributes->bindInt(':options_id', $option);
          $Qattributes->bindInt(':options_values_id', $value);
          $Qattributes->execute();

          if ($Qattributes->value('price_prefix') == '+') {
            $attributes_price += $Qattributes->value('options_values_price');
          } else {
            $attributes_price -= $Qattributes->value('options_values_price');
          }
        }
      }

      return $attributes_price;
    }

    function get_products() {
      global $osC_Database;

      if (!is_array($this->contents)) return false;

      $products_array = array();
      reset($this->contents);
      while (list($products_id, ) = each($this->contents)) {
        $Qproducts = $osC_Database->query('select p.products_id, p.products_image, p.products_price, p.products_weight, p.products_tax_class_id, pd.products_name, pd.products_model, pd.products_keyword from :table_products p, :table_products_description pd where p.products_id = :products_id and pd.products_id = p.products_id and pd.language_id = :language_id');
        $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
        $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
        $Qproducts->bindInt(':products_id', $products_id);
        $Qproducts->bindInt(':language_id', $_SESSION['languages_id']);
        $Qproducts->execute();

        if ($Qproducts->numberOfRows()) {
          $prid = $Qproducts->valueInt('products_id');
          $products_price = $Qproducts->value('products_price');

          $Qspecials = $osC_Database->query('select specials_new_products_price from :table_specials where products_id = :products_id and status = :status');
          $Qspecials->bindTable(':table_specials', TABLE_SPECIALS);
          $Qspecials->bindInt(':products_id', $prid);
          $Qspecials->bindInt(':status', 1);
          $Qspecials->execute();

          if ($Qspecials->numberOfRows()) {
            $products_price = $Qspecials->value('specials_new_products_price');
          }

          $products_array[] = array('id' => $products_id,
                                    'name' => $Qproducts->value('products_name'),
                                    'model' => $Qproducts->value('products_model'),
                                    'keyword' => $Qproducts->value('products_keyword'),
                                    'image' => $Qproducts->value('products_image'),
                                    'price' => $products_price,
                                    'quantity' => $this->contents[$products_id]['qty'],
                                    'weight' => $Qproducts->value('products_weight'),
                                    'final_price' => ($products_price + $this->attributes_price($products_id)),
                                    'tax_class_id' => $Qproducts->valueInt('products_tax_class_id'),
                                    'attributes' => (isset($this->contents[$products_id]['attributes']) ? $this->contents[$products_id]['attributes'] : ''));
        }
      }

      return $products_array;
    }

    function show_total() {
      $this->calculate();

      return $this->total;
    }

    function show_weight() {
      $this->calculate();

      return $this->weight;
    }

    function generate_cart_id($length = 5) {
      return tep_create_random_value($length, 'digits');
    }

    function get_content_type() {
      global $osC_Database;

      $this->content_type = false;

      if ( (DOWNLOAD_ENABLED == 'true') && ($this->count_contents() > 0) ) {
        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
          if (isset($this->contents[$products_id]['attributes'])) {
            reset($this->contents[$products_id]['attributes']);
            while (list(, $value) = each($this->contents[$products_id]['attributes'])) {
              $Qcheck = $osC_Database->query('select count(*) as total from :table_products_attributes pa, :table_products_attributes_download pad where pa.products_id = :products_id and pa.options_values_id = :options_values_id and pa.products_attributes_id = pad.products_attributes_id');
              $Qcheck->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
              $Qcheck->bindTable(':table_products_attributes_download', TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD);
              $Qcheck->bindInt(':products_id', $products_id);
              $Qcheck->bindInt(':options_values_id', $value);
              $Qcheck->execute();

              if ($Qcheck->valueInt('total') > 0) {
                switch ($this->content_type) {
                  case 'physical':
                    $this->content_type = 'mixed';

                    return $this->content_type;
                    break;
                  default:
                    $this->content_type = 'virtual';
                    break;
                }
              } else {
                switch ($this->content_type) {
                  case 'virtual':
                    $this->content_type = 'mixed';

                    return $this->content_type;
                    break;
                  default:
                    $this->content_type = 'physical';
                    break;
                }
              }
            }
          } else {
            switch ($this->content_type) {
              case 'virtual':
                $this->content_type = 'mixed';

                return $this->content_type;
                break;
              default:
                $this->content_type = 'physical';
                break;
            }
          }
        }
      } else {
        $this->content_type = 'physical';
      }

      return $this->content_type;
    }
  }
?>

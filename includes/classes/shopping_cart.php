<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_ShoppingCart {
    private $_contents = array();
    private $_sub_total = 0;
    private $_total = 0;
    private $_weight = 0;
    private $_tax = 0;
    private $_tax_groups = array();
    private $_content_type;
    private $_products_in_stock = true;

    public function __construct() {
      if ( !isset($_SESSION['osC_ShoppingCart_data']) ) {
        $_SESSION['osC_ShoppingCart_data'] = array('contents' => array(),
                                                   'sub_total_cost' => 0,
                                                   'total_cost' => 0,
                                                   'total_weight' => 0,
                                                   'tax' => 0,
                                                   'tax_groups' => array(),
                                                   'shipping_boxes_weight' => 0,
                                                   'shipping_boxes' => 1,
                                                   'shipping_address' => array('zone_id' => STORE_ZONE,
                                                                               'country_id' => STORE_COUNTRY),
                                                   'shipping_method' => array(),
                                                   'billing_address' => array('zone_id' => STORE_ZONE,
                                                                              'country_id' => STORE_COUNTRY),
                                                   'billing_method' => array(),
                                                   'shipping_quotes' => array(),
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
      $this->_shipping_address =& $_SESSION['osC_ShoppingCart_data']['shipping_address'];
      $this->_shipping_method =& $_SESSION['osC_ShoppingCart_data']['shipping_method'];
      $this->_billing_address =& $_SESSION['osC_ShoppingCart_data']['billing_address'];
      $this->_billing_method =& $_SESSION['osC_ShoppingCart_data']['billing_method'];
      $this->_shipping_quotes =& $_SESSION['osC_ShoppingCart_data']['shipping_quotes'];
      $this->_order_totals =& $_SESSION['osC_ShoppingCart_data']['order_totals'];
    }

    public function refresh() {
      if ( !isset($_SESSION['cartID']) ) {
        $this->_calculate();
      }
    }

    public function hasContents() {
      return !empty($this->_contents);
    }

    public function synchronizeWithDatabase() {
      global $osC_Database, $osC_Services, $osC_Language, $osC_Customer, $osC_Specials;

      if ( !$osC_Customer->isLoggedOn() ) {
        return false;
      }

      foreach ( $this->_contents as $item_id => $data ) {
        $db_action = 'check';

        if ( isset($data['variants']) ) {
          foreach ( $data['variants'] as $variant ) {
            if ( $variant['has_custom_value'] === true ) {
              $db_action = 'insert';

              break;
            }
          }
        }

        if ( $db_action == 'check' ) {
          $Qproduct = $osC_Database->query('select item_id, quantity from :table_shopping_carts where customers_id = :customers_id and products_id = :products_id');
          $Qproduct->bindTable(':table_shopping_carts', TABLE_SHOPPING_CARTS);
          $Qproduct->bindInt(':customers_id', $osC_Customer->getID());
          $Qproduct->bindInt(':products_id', $data['id']);
          $Qproduct->execute();

          if ( $Qproduct->numberOfRows() > 0 ) {
            $Qupdate = $osC_Database->query('update :table_shopping_carts set quantity = :quantity where customers_id = :customers_id and item_id = :item_id');
            $Qupdate->bindTable(':table_shopping_carts', TABLE_SHOPPING_CARTS);
            $Qupdate->bindInt(':quantity', $data['quantity'] + $Qproduct->valueInt('quantity'));
            $Qupdate->bindInt(':customers_id', $osC_Customer->getID());
            $Qupdate->bindInt(':item_id', $Qproduct->valueInt('item_id'));
            $Qupdate->execute();
          } else {
            $db_action = 'insert';
          }
        }

        if ( $db_action == 'insert') {
          $Qid = $osC_Database->query('select max(item_id) as item_id from :table_shopping_carts where customers_id = :customers_id');
          $Qid->bindTable(':table_shopping_carts', TABLE_SHOPPING_CARTS);
          $Qid->bindInt(':customers_id', $osC_Customer->getID());
          $Qid->execute();

          $db_item_id = $Qid->valueInt('item_id') + 1;

          $Qnew = $osC_Database->query('insert into :table_shopping_carts (customers_id, item_id, products_id, quantity, date_added) values (:customers_id, :item_id, :products_id, :quantity, :date_added)');
          $Qnew->bindTable(':table_shopping_carts', TABLE_SHOPPING_CARTS);
          $Qnew->bindInt(':customers_id', $osC_Customer->getID());
          $Qnew->bindInt(':item_id', $db_item_id);
          $Qnew->bindInt(':products_id', $data['id']);
          $Qnew->bindInt(':quantity', $data['quantity']);
          $Qnew->bindRaw(':date_added', 'now()');
          $Qnew->execute();

          if ( isset($data['variants']) ) {
            foreach ( $data['variants'] as $variant ) {
              if ( $variant['has_custom_value'] === true ) {
                $Qnew = $osC_Database->query('insert into :table_shopping_carts_custom_variants_values (shopping_carts_item_id, customers_id, products_id, products_variants_values_id, products_variants_values_text) values (:shopping_carts_item_id, :customers_id, :products_id, :products_variants_values_id, :products_variants_values_text)');
                $Qnew->bindTable(':table_shopping_carts_custom_variants_values', TABLE_SHOPPING_CARTS_CUSTOM_VARIANTS_VALUES);
                $Qnew->bindInt(':shopping_carts_item_id', $db_item_id);
                $Qnew->bindInt(':customers_id', $osC_Customer->getID());
                $Qnew->bindInt(':products_id', $data['id']);
                $Qnew->bindInt(':products_variants_values_id', $variant['value_id']);
                $Qnew->bindValue(':products_variants_values_text', $variant['value_title']);
                $Qnew->execute();
              }
            }
          }
        }
      }

// reset per-session cart contents, but not the database contents
      $this->reset();

      $_delete_array = array();

      $Qproducts = $osC_Database->query('select sc.item_id, sc.products_id, sc.quantity, sc.date_added, p.parent_id, p.products_price, p.products_model, p.products_tax_class_id, p.products_weight, p.products_weight_class, p.products_status from :table_shopping_carts sc, :table_products p where sc.customers_id = :customers_id and sc.products_id = p.products_id order by sc.date_added desc');
      $Qproducts->bindTable(':table_shopping_carts', TABLE_SHOPPING_CARTS);
      $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
      $Qproducts->bindInt(':customers_id', $osC_Customer->getID());
      $Qproducts->execute();

      while ( $Qproducts->next() ) {
        if ( $Qproducts->valueInt('products_status') === 1 ) {
          $Qdesc = $osC_Database->query('select products_name, products_keyword from :table_products_description where products_id = :products_id and language_id = :language_id');
          $Qdesc->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
          $Qdesc->bindInt(':products_id', ($Qproducts->valueInt('parent_id') > 0) ? $Qproducts->valueInt('parent_id') : $Qproducts->valueInt('products_id'));
          $Qdesc->bindInt(':language_id', $osC_Language->getID());
          $Qdesc->execute();

          $Qimage = $osC_Database->query('select image from :table_products_images where products_id = :products_id and default_flag = :default_flag');
          $Qimage->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
          $Qimage->bindInt(':products_id', ($Qproducts->valueInt('parent_id') > 0) ? $Qproducts->valueInt('parent_id') : $Qproducts->valueInt('products_id'));
          $Qimage->bindInt(':default_flag', 1);
          $Qimage->execute();

          $price = $Qproducts->value('products_price');

          if ( $osC_Services->isStarted('specials') ) {
            if ( $new_price = $osC_Specials->getPrice($Qproducts->valueInt('products_id')) ) {
              $price = $new_price;
            }
          }

          $this->_contents[$Qproducts->valueInt('item_id')] = array('item_id' => $Qproducts->valueInt('item_id'),
                                                                    'id' => $Qproducts->valueInt('products_id'),
                                                                    'parent_id' => $Qproducts->valueInt('parent_id'),
                                                                    'model' => $Qproducts->value('products_model'),
                                                                    'name' => $Qdesc->value('products_name'),
                                                                    'keyword' => $Qdesc->value('products_keyword'),
                                                                    'image' => ($Qimage->numberOfRows() === 1) ? $Qimage->value('image') : '',
                                                                    'price' => $price,
                                                                    'quantity' => $Qproducts->valueInt('quantity'),
                                                                    'weight' => $Qproducts->value('products_weight'),
                                                                    'tax_class_id' => $Qproducts->valueInt('products_tax_class_id'),
                                                                    'date_added' => osC_DateTime::getShort($Qproducts->value('date_added')),
                                                                    'weight_class_id' => $Qproducts->valueInt('products_weight_class'));

          if ( $Qproducts->valueInt('parent_id') > 0 ) {
            $Qcheck = $osC_Database->query('select products_status from :table_products where products_id = :products_id');
            $Qcheck->bindTable(':table_products', TABLE_PRODUCTS);
            $Qcheck->bindInt(':products_id', $Qproducts->valueInt('parent_id'));
            $Qcheck->execute();

            if ( $Qcheck->valueInt('products_status') === 1 ) {
              $Qvariant = $osC_Database->query('select pvg.id as group_id, pvg.title as group_title, pvg.module, pvv.id as value_id, pvv.title as value_title from :table_products_variants pv, :table_products_variants_values pvv, :table_products_variants_groups pvg where pv.products_id = :products_id and pv.products_variants_values_id = pvv.id and pvv.languages_id = :languages_id and pvv.products_variants_groups_id = pvg.id and pvg.languages_id = :languages_id');
              $Qvariant->bindTable(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
              $Qvariant->bindTable(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
              $Qvariant->bindTable(':table_products_variants_groups', TABLE_PRODUCTS_VARIANTS_GROUPS);
              $Qvariant->bindInt(':products_id', $Qproducts->valueInt('products_id'));
              $Qvariant->bindInt(':languages_id', $osC_Language->getID());
              $Qvariant->bindInt(':languages_id', $osC_Language->getID());
              $Qvariant->execute();

              if ( $Qvariant->numberOfRows() > 0 ) {
                while ( $Qvariant->next() ) {
                  $group_title = osC_Variants::getGroupTitle($Qvariant->value('module'), $Qvariant->toArray());
                  $value_title = $Qvariant->value('value_title');
                  $has_custom_value = false;

                  $Qcvv = $osC_Database->query('select products_variants_values_text from :table_shopping_carts_custom_variants_values where customers_id = :customers_id and shopping_carts_item_id = :shopping_carts_item_id and products_id = :products_id and products_variants_values_id = :products_variants_values_id');
                  $Qcvv->bindTable(':table_shopping_carts_custom_variants_values', TABLE_SHOPPING_CARTS_CUSTOM_VARIANTS_VALUES);
                  $Qcvv->bindInt(':customers_id', $osC_Customer->getID());
                  $Qcvv->bindInt(':shopping_carts_item_id', $Qproducts->valueInt('item_id'));
                  $Qcvv->bindInt(':products_id', $Qproducts->valueInt('products_id'));
                  $Qcvv->bindInt(':products_variants_values_id', $Qvariant->valueInt('value_id'));
                  $Qcvv->execute();

                  if ( $Qcvv->numberOfRows() === 1 ) {
                    $value_title = $Qcvv->value('products_variants_values_text');
                    $has_custom_value = true;
                  }

                  $this->_contents[$Qproducts->valueInt('item_id')]['variants'][] = array('group_id' => $Qvariant->valueInt('group_id'),
                                                                                          'value_id' => $Qvariant->valueInt('value_id'),
                                                                                          'group_title' => $group_title,
                                                                                          'value_title' => $value_title,
                                                                                          'has_custom_value' => $has_custom_value);
                }
              } else {
                $_delete_array[] = $Qproducts->valueInt('item_id');
              }
            } else {
              $_delete_array[] = $Qproducts->valueInt('item_id');
            }
          }
        } else {
          $_delete_array[] = $Qproducts->valueInt('item_id');
        }
      }

      if ( !empty($_delete_array) ) {
        foreach ( $_delete_array as $id ) {
          unset($this->_contents[$id]);
        }

        $Qdelete = $osC_Database->query('delete from :table_shopping_carts where customers_id = :customers_id and item_id in (":item_id")');
        $Qdelete->bindTable(':table_shopping_carts', TABLE_SHOPPING_CARTS);
        $Qdelete->bindInt(':customers_id', $osC_Customer->getID());
        $Qdelete->bindRaw(':item_id', implode('", "', $_delete_array));
        $Qdelete->execute();

        $Qdelete = $osC_Database->query('delete from :table_shopping_carts_custom_variants_values where customers_id = :customers_id and shopping_carts_item_id in (":shopping_carts_item_id")');
        $Qdelete->bindTable(':table_shopping_carts_custom_variants_values', TABLE_SHOPPING_CARTS_CUSTOM_VARIANTS_VALUES);
        $Qdelete->bindInt(':customers_id', $osC_Customer->getID());
        $Qdelete->bindRaw(':shopping_carts_item_id', implode('", "', $_delete_array));
        $Qdelete->execute();
      }

      $this->_cleanUp();
      $this->_calculate();
    }

    public function reset($reset_database = false) {
      global $osC_Database, $osC_Customer;

      if ( ($reset_database === true) && $osC_Customer->isLoggedOn() ) {
        $Qdelete = $osC_Database->query('delete from :table_shopping_carts where customers_id = :customers_id');
        $Qdelete->bindTable(':table_shopping_carts', TABLE_SHOPPING_CARTS);
        $Qdelete->bindInt(':customers_id', $osC_Customer->getID());
        $Qdelete->execute();

        $Qdelete = $osC_Database->query('delete from :table_shopping_carts_custom_variants_values where customers_id = :customers_id');
        $Qdelete->bindTable(':table_shopping_carts_custom_variants_values', TABLE_SHOPPING_CARTS_CUSTOM_VARIANTS_VALUES);
        $Qdelete->bindInt(':customers_id', $osC_Customer->getID());
        $Qdelete->execute();
      }

      $this->_contents = array();
      $this->_sub_total = 0;
      $this->_total = 0;
      $this->_weight = 0;
      $this->_tax = 0;
      $this->_tax_groups = array();
      $this->_content_type = null;

      $this->resetShippingAddress();
      $this->resetShippingMethod();
      $this->resetBillingAddress();
      $this->resetBillingMethod();

      if ( isset($_SESSION['cartID']) ) {
        unset($_SESSION['cartID']);
      }
    }

    public function add($product_id, $quantity = null) {
      global $osC_Database, $osC_Services, $osC_Language, $osC_Customer;

      if ( !is_numeric($product_id) ) {
        return false;
      }

      $Qproduct = $osC_Database->query('select p.parent_id, p.products_price, p.products_tax_class_id, p.products_model, p.products_weight, p.products_weight_class, p.products_status, i.image from :table_products p left join :table_products_images i on (p.products_id = i.products_id and i.default_flag = :default_flag) where p.products_id = :products_id');
      $Qproduct->bindTable(':table_products', TABLE_PRODUCTS);
      $Qproduct->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
      $Qproduct->bindInt(':default_flag', 1);
      $Qproduct->bindInt(':products_id', $product_id);
      $Qproduct->execute();

      if ( $Qproduct->valueInt('products_status') === 1 ) {
        if ( $this->exists($product_id) ) {
          $item_id = $this->getBasketID($product_id);

          if ( !is_numeric($quantity) ) {
            $quantity = $this->getQuantity($item_id) + 1;
          }

          $this->_contents[$item_id]['quantity'] = $quantity;

          if ( $osC_Customer->isLoggedOn() ) {
            $Qupdate = $osC_Database->query('update :table_shopping_carts set quantity = :quantity where customers_id = :customers_id and item_id = :item_id');
            $Qupdate->bindTable(':table_shopping_carts', TABLE_SHOPPING_CARTS);
            $Qupdate->bindInt(':quantity', $quantity);
            $Qupdate->bindInt(':customers_id', $osC_Customer->getID());
            $Qupdate->bindInt(':item_id', $item_id);
            $Qupdate->execute();
          }
        } else {
          if ( !is_numeric($quantity) ) {
            $quantity = 1;
          }

          $Qdescription = $osC_Database->query('select products_name, products_keyword from :table_products_description where products_id = :products_id and language_id = :language_id');
          $Qdescription->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
          $Qdescription->bindInt(':products_id', ($Qproduct->valueInt('parent_id') > 0) ? $Qproduct->valueInt('parent_id') : $product_id);
          $Qdescription->bindInt(':language_id', $osC_Language->getID());
          $Qdescription->execute();

          $price = $Qproduct->value('products_price');

          if ( $osC_Services->isStarted('specials') ) {
            global $osC_Specials;

            if ( $new_price = $osC_Specials->getPrice($product_id) ) {
              $price = $new_price;
            }
          }

          if ( $osC_Customer->isLoggedOn() ) {
            $Qid = $osC_Database->query('select max(item_id) as item_id from :table_shopping_carts where customers_id = :customers_id');
            $Qid->bindTable(':table_shopping_carts', TABLE_SHOPPING_CARTS);
            $Qid->bindInt(':customers_id', $osC_Customer->getID());
            $Qid->execute();

            $item_id = $Qid->valueInt('item_id') + 1;
          } else {
            if ( empty($this->_contents) ) {
              $item_id = 1;
            } else {
              $item_id = max(array_keys($this->_contents)) + 1;
            }
          }

          $this->_contents[$item_id] = array('item_id' => $item_id,
                                             'id' => $product_id,
                                             'parent_id' => $Qproduct->valueInt('parent_id'),
                                             'name' => $Qdescription->value('products_name'),
                                             'model' => $Qproduct->value('products_model'),
                                             'keyword' => $Qdescription->value('products_keyword'),
                                             'image' => $Qproduct->value('image'),
                                             'price' => $price,
                                             'quantity' => $quantity,
                                             'weight' => $Qproduct->value('products_weight'),
                                             'tax_class_id' => $Qproduct->valueInt('products_tax_class_id'),
                                             'date_added' => osC_DateTime::getShort(osC_DateTime::getNow()),
                                             'weight_class_id' => $Qproduct->valueInt('products_weight_class'));

          if ( $osC_Customer->isLoggedOn() ) {
            $Qnew = $osC_Database->query('insert into :table_shopping_carts (customers_id, item_id, products_id, quantity, date_added) values (:customers_id, :item_id, :products_id, :quantity, :date_added)');
            $Qnew->bindTable(':table_shopping_carts', TABLE_SHOPPING_CARTS);
            $Qnew->bindInt(':customers_id', $osC_Customer->getID());
            $Qnew->bindInt(':item_id', $item_id);
            $Qnew->bindInt(':products_id', $product_id);
            $Qnew->bindInt(':quantity', $quantity);
            $Qnew->bindRaw(':date_added', 'now()');
            $Qnew->execute();
          }

          if ( $Qproduct->valueInt('parent_id') > 0 ) {
            $Qvariant = $osC_Database->query('select pvg.id as group_id, pvg.title as group_title, pvg.module, pvv.id as value_id, pvv.title as value_title from :table_products_variants pv, :table_products_variants_values pvv, :table_products_variants_groups pvg where pv.products_id = :products_id and pv.products_variants_values_id = pvv.id and pvv.languages_id = :languages_id and pvv.products_variants_groups_id = pvg.id and pvg.languages_id = :languages_id');
            $Qvariant->bindTable(':table_products_variants', TABLE_PRODUCTS_VARIANTS);
            $Qvariant->bindTable(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
            $Qvariant->bindTable(':table_products_variants_groups', TABLE_PRODUCTS_VARIANTS_GROUPS);
            $Qvariant->bindInt(':products_id', $product_id);
            $Qvariant->bindInt(':languages_id', $osC_Language->getID());
            $Qvariant->bindInt(':languages_id', $osC_Language->getID());
            $Qvariant->execute();

            while ( $Qvariant->next() ) {
              $group_title = osC_Variants::getGroupTitle($Qvariant->value('module'), $Qvariant->toArray());
              $value_title = osC_Variants::getValueTitle($Qvariant->value('module'), $Qvariant->toArray());
              $has_custom_value = osC_Variants::hasCustomValue($Qvariant->value('module'));

              $this->_contents[$item_id]['variants'][] = array('group_id' => $Qvariant->valueInt('group_id'),
                                                               'value_id' => $Qvariant->valueInt('value_id'),
                                                               'group_title' => $group_title,
                                                               'value_title' => $value_title,
                                                               'has_custom_value' => $has_custom_value);

              if ( $osC_Customer->isLoggedOn() && ($has_custom_value === true) ) {
                $Qnew = $osC_Database->query('insert into :table_shopping_carts_custom_variants_values (shopping_carts_item_id, customers_id, products_id, products_variants_values_id, products_variants_values_text) values (:shopping_carts_item_id, :customers_id, :products_id, :products_variants_values_id, :products_variants_values_text)');
                $Qnew->bindTable(':table_shopping_carts_custom_variants_values', TABLE_SHOPPING_CARTS_CUSTOM_VARIANTS_VALUES);
                $Qnew->bindInt(':shopping_carts_item_id', $item_id);
                $Qnew->bindInt(':customers_id', $osC_Customer->getID());
                $Qnew->bindInt(':products_id', $product_id);
                $Qnew->bindInt(':products_variants_values_id', $Qvariant->valueInt('value_id'));
                $Qnew->bindValue(':products_variants_values_text', $value_title);
                $Qnew->execute();
              }
            }
          }
        }

        $this->_cleanUp();
        $this->_calculate();
      }
    }

    public function numberOfItems() {
      $total = 0;

      foreach ( $this->_contents as $product ) {
        $total += $product['quantity'];
      }

      return $total;
    }

    public function getBasketID($product_id) {
      foreach ( $this->_contents as $item_id => $product ) {
        if ( $product['id'] === $product_id ) {
          return $item_id;
        }
      }
    }

    public function getQuantity($item_id) {
      return ( isset($this->_contents[$item_id]) ) ? $this->_contents[$item_id]['quantity'] : 0;
    }

    public function exists($product_id) {
      foreach ( $this->_contents as $product ) {
        if ( $product['id'] === $product_id ) {
          if ( isset($product['variants']) ) {
            foreach ( $product['variants'] as $variant ) {
              if ( $variant['has_custom_value'] === true ) {
                return false;
              }
            }
          }

          return true;
        }
      }

      return false;
    }

    public function update($item_id, $quantity) {
      global $osC_Database, $osC_Customer;

      if ( !is_numeric($quantity) ) {
        $quantity = $this->getQuantity($item_id) + 1;
      }

      $this->_contents[$item_id]['quantity'] = $quantity;

      if ( $osC_Customer->isLoggedOn() ) {
        $Qupdate = $osC_Database->query('update :table_shopping_carts set quantity = :quantity where customers_id = :customers_id and item_id = :item_id');
        $Qupdate->bindTable(':table_shopping_carts', TABLE_SHOPPING_CARTS);
        $Qupdate->bindInt(':quantity', $quantity);
        $Qupdate->bindInt(':customers_id', $osC_Customer->getID());
        $Qupdate->bindInt(':item_id', $item_id);
        $Qupdate->execute();
      }

      $this->_cleanUp();
      $this->_calculate();
    }

    public function remove($item_id) {
      global $osC_Database, $osC_Customer;

      unset($this->_contents[$item_id]);

      if ( $osC_Customer->isLoggedOn() ) {
        $Qdelete = $osC_Database->query('delete from :table_shopping_carts where customers_id = :customers_id and item_id = :item_id');
        $Qdelete->bindTable(':table_shopping_carts', TABLE_SHOPPING_CARTS);
        $Qdelete->bindInt(':customers_id', $osC_Customer->getID());
        $Qdelete->bindInt(':item_id', $item_id);
        $Qdelete->execute();

        $Qdelete = $osC_Database->query('delete from :table_shopping_carts_custom_variants_values where customers_id = :customers_id and shopping_carts_item_id = :shopping_carts_item_id');
        $Qdelete->bindTable(':table_shopping_carts_custom_variants_values', TABLE_SHOPPING_CARTS_CUSTOM_VARIANTS_VALUES);
        $Qdelete->bindInt(':customers_id', $osC_Customer->getID());
        $Qdelete->bindInt(':shopping_carts_item_id', $item_id);
        $Qdelete->execute();
      }

      $this->_calculate();
    }

    public function getProducts() {
      static $_is_sorted = false;

      if ( $_is_sorted === false ) {
        $_is_sorted = true;

        uasort($this->_contents, array('osC_ShoppingCart', '_uasortProductsByDateAdded'));
      }

      return $this->_contents;
    }

    public function getSubTotal() {
      return $this->_sub_total;
    }

    public function getTotal() {
      return $this->_total;
    }

    public function getWeight() {
      return $this->_weight;
    }

    public function generateCartID($length = 5) {
      return osc_create_random_string($length, 'digits');
    }

    public function getCartID() {
      return $_SESSION['cartID'];
    }

    public function getContentType() {
      global $osC_Database;

      $this->_content_type = 'physical';

      if ( (DOWNLOAD_ENABLED == '1') && $this->hasContents() ) {
        foreach ( $this->_contents as $product_id => $data ) {
/* HPDL
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
*/
            switch ( $this->_content_type ) {
              case 'virtual':
                $this->_content_type = 'mixed';

                break 2;

              default:
                $this->_content_type = 'physical';

                break;
            }
//          }
        }
      }

      return $this->_content_type;
    }

    public function isVariant($item_id) {
      return isset($this->_contents[$item_id]['variants']) && !empty($this->_contents[$item_id]['variants']);
    }

    public function getVariant($item_id) {
      if ( isset($this->_contents[$item_id]['variants']) && !empty($this->_contents[$item_id]['variants']) ) {
        return $this->_contents[$item_id]['variants'];
      }
    }

    public function isInStock($item_id) {
      global $osC_Database;

      $Qstock = $osC_Database->query('select products_quantity from :table_products where products_id = :products_id');
      $Qstock->bindTable(':table_products', TABLE_PRODUCTS);
      $Qstock->bindInt(':products_id', $this->_contents[$item_id]['id']);
      $Qstock->execute();

      if ( ($Qstock->valueInt('products_quantity') - $this->_contents[$item_id]['quantity']) > 0 ) {
        return true;
      } elseif ( $this->_products_in_stock === true ) {
        $this->_products_in_stock = false;
      }

      return false;
    }

    public function hasStock() {
      return $this->_products_in_stock;
    }

    public function hasShippingAddress() {
      return isset($this->_shipping_address['id']);
    }

    public function setShippingAddress($address_id) {
      global $osC_Database, $osC_Customer;

      $previous_address = null;

      if ( isset($this->_shipping_address['id']) ) {
        $previous_address = $this->getShippingAddress();
      }

      $Qaddress = $osC_Database->query('select ab.entry_firstname, ab.entry_lastname, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, ab.entry_telephone, z.zone_code, z.zone_name, ab.entry_country_id, c.countries_name, c.countries_iso_code_2, c.countries_iso_code_3, c.address_format, ab.entry_state from :table_address_book ab left join :table_zones z on (ab.entry_zone_id = z.zone_id) left join :table_countries c on (ab.entry_country_id = c.countries_id) where ab.customers_id = :customers_id and ab.address_book_id = :address_book_id');
      $Qaddress->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
      $Qaddress->bindTable(':table_zones', TABLE_ZONES);
      $Qaddress->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qaddress->bindInt(':customers_id', $osC_Customer->getID());
      $Qaddress->bindInt(':address_book_id', $address_id);
      $Qaddress->execute();

      if ( $Qaddress->numberOfRows() === 1 ) {
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
    }

    public function getShippingAddress($key = null) {
      if ( empty($key) ) {
        return $this->_shipping_address;
      }

      return $this->_shipping_address[$key];
    }

    public function resetShippingAddress() {
      global $osC_Customer;

      $this->_shipping_address = array('zone_id' => STORE_ZONE,
                                       'country_id' => STORE_COUNTRY);

      if ( $osC_Customer->isLoggedOn() && $osC_Customer->hasDefaultAddress() ) {
        $this->setShippingAddress($osC_Customer->getDefaultAddressID());
      }
    }

    public function setShippingMethod($shipping_array, $calculate_total = true) {
      $this->_shipping_method = $shipping_array;

      if ( $calculate_total === true ) {
        $this->_calculate(false);
      }
    }

    public function getShippingMethod($key = null) {
      if ( empty($key) ) {
        return $this->_shipping_method;
      }

      return $this->_shipping_method[$key];
    }

    public function resetShippingMethod() {
      $this->_shipping_method = array();

      $this->_calculate();
    }

    public function hasShippingMethod() {
      return !empty($this->_shipping_method);
    }

    public function hasBillingAddress() {
      return isset($this->_billing_address['id']);
    }

    public function setBillingAddress($address_id) {
      global $osC_Database, $osC_Customer;

      $previous_address = false;

      if ( isset($this->_billing_address['id']) ) {
        $previous_address = $this->getBillingAddress();
      }

      $Qaddress = $osC_Database->query('select ab.entry_firstname, ab.entry_lastname, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, ab.entry_telephone, z.zone_code, z.zone_name, ab.entry_country_id, c.countries_name, c.countries_iso_code_2, c.countries_iso_code_3, c.address_format, ab.entry_state from :table_address_book ab left join :table_zones z on (ab.entry_zone_id = z.zone_id) left join :table_countries c on (ab.entry_country_id = c.countries_id) where ab.customers_id = :customers_id and ab.address_book_id = :address_book_id');
      $Qaddress->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
      $Qaddress->bindTable(':table_zones', TABLE_ZONES);
      $Qaddress->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qaddress->bindInt(':customers_id', $osC_Customer->getID());
      $Qaddress->bindInt(':address_book_id', $address_id);
      $Qaddress->execute();

      if ( $Qaddress->numberOfRows() === 1 ) {
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
    }

    public function getBillingAddress($key = null) {
      if ( empty($key) ) {
        return $this->_billing_address;
      }

      return $this->_billing_address[$key];
    }

    public function resetBillingAddress() {
      global $osC_Customer;

      $this->_billing_address = array('zone_id' => STORE_ZONE,
                                      'country_id' => STORE_COUNTRY);

      if ( $osC_Customer->isLoggedOn() && $osC_Customer->hasDefaultAddress() ) {
        $this->setBillingAddress($osC_Customer->getDefaultAddressID());
      }
    }

    public function setBillingMethod($billing_array) {
      $this->_billing_method = $billing_array;

      $this->_calculate();
    }

    public function getBillingMethod($key = null) {
      if ( empty($key) ) {
        return $this->_billing_method;
      }

      return $this->_billing_method[$key];
    }

    public function resetBillingMethod() {
      $this->_billing_method = array();

      $this->_calculate();
    }

    public function hasBillingMethod() {
      return !empty($this->_billing_method);
    }

    public function getTaxingAddress($id = null) {
      if ( $this->getContentType() == 'virtual' ) {
        return $this->getBillingAddress($id);
      }

      return $this->getShippingAddress($id);
    }

    public function addTaxAmount($amount) {
      $this->_tax += $amount;
    }

    public function numberOfTaxGroups() {
      return sizeof($this->_tax_groups);
    }

    public function addTaxGroup($group, $amount) {
      if ( isset($this->_tax_groups[$group]) ) {
        $this->_tax_groups[$group] += $amount;
      } else {
        $this->_tax_groups[$group] = $amount;
      }
    }

    public function getTaxGroups() {
      return $this->_tax_groups;
    }

    public function addToTotal($amount) {
      $this->_total += $amount;
    }

    public function getOrderTotals() {
      return $this->_order_totals;
    }

    public function getShippingBoxesWeight() {
      return $this->_shipping_boxes_weight;
    }

    public function numberOfShippingBoxes() {
      return $this->_shipping_boxes;
    }

    private function _cleanUp() {
      global $osC_Database, $osC_Customer;

      foreach ( $this->_contents as $item_id => $data ) {
        if ( $data['quantity'] < 1 ) {
          unset($this->_contents[$item_id]);

          if ( $osC_Customer->isLoggedOn() ) {
            $Qdelete = $osC_Database->query('delete from :table_shopping_carts where customers_id = :customers_id and item_id = :item_id');
            $Qdelete->bindTable(':table_shopping_carts', TABLE_SHOPPING_CARTS);
            $Qdelete->bindInt(':customers_id', $osC_Customer->getID());
            $Qdelete->bindInt(':item_id', $item_id);
            $Qdelete->execute();

            $Qdelete = $osC_Database->query('delete from :table_shopping_carts_custom_variants_values where customers_id = :customers_id and shopping_carts_item_id = :shopping_carts_item_id');
            $Qdelete->bindTable(':table_shopping_carts_custom_variants_values', TABLE_SHOPPING_CARTS_CUSTOM_VARIANTS_VALUES);
            $Qdelete->bindInt(':customers_id', $osC_Customer->getID());
            $Qdelete->bindInt(':shopping_carts_item_id', $item_id);
            $Qdelete->execute();
          }
        }
      }
    }

    private function _calculate($set_shipping = true) {
      global $osC_Currencies, $osC_Tax, $osC_Weight, $osC_Shipping, $osC_OrderTotal;

      $this->_sub_total = 0;
      $this->_total = 0;
      $this->_weight = 0;
      $this->_tax = 0;
      $this->_tax_groups = array();
      $this->_shipping_boxes_weight = 0;
      $this->_shipping_boxes = 0;
      $this->_shipping_quotes = array();
      $this->_order_totals = array();

      $_SESSION['cartID'] = $this->generateCartID();

      if ( $this->hasContents() ) {
        foreach ( $this->_contents as $data ) {
          $products_weight = $osC_Weight->convert($data['weight'], $data['weight_class_id'], SHIPPING_WEIGHT_UNIT);
          $this->_weight += $products_weight * $data['quantity'];

          $tax = $osC_Tax->getTaxRate($data['tax_class_id'], $this->getTaxingAddress('country_id'), $this->getTaxingAddress('zone_id'));
          $tax_description = $osC_Tax->getTaxRateDescription($data['tax_class_id'], $this->getTaxingAddress('country_id'), $this->getTaxingAddress('zone_id'));

          $shown_price = $osC_Currencies->addTaxRateToPrice($data['price'], $tax, $data['quantity']);

          $this->_sub_total += $shown_price;
          $this->_total += $shown_price;

          if ( DISPLAY_PRICE_WITH_TAX == '1' ) {
            $tax_amount = $shown_price - ($shown_price / (($tax < 10) ? '1.0' . str_replace('.', '', $tax) : '1.' . str_replace('.', '', $tax)));
          } else {
            $tax_amount = ($tax / 100) * $shown_price;

            $this->_total += $tax_amount;
          }

          $this->_tax += $tax_amount;

          if ( isset($this->_tax_groups[$tax_description]) ) {
            $this->_tax_groups[$tax_description] += $tax_amount;
          } else {
            $this->_tax_groups[$tax_description] = $tax_amount;
          }
        }

        $this->_shipping_boxes_weight = $this->_weight;
        $this->_shipping_boxes = 1;

        if ( SHIPPING_BOX_WEIGHT >= ($this->_shipping_boxes_weight * SHIPPING_BOX_PADDING/100) ) {
          $this->_shipping_boxes_weight = $this->_shipping_boxes_weight + SHIPPING_BOX_WEIGHT;
        } else {
          $this->_shipping_boxes_weight = $this->_shipping_boxes_weight + ($this->_shipping_boxes_weight * SHIPPING_BOX_PADDING/100);
        }

        if ( $this->_shipping_boxes_weight > SHIPPING_MAX_WEIGHT ) { // Split into many boxes
          $this->_shipping_boxes = ceil($this->_shipping_boxes_weight / SHIPPING_MAX_WEIGHT);
          $this->_shipping_boxes_weight = $this->_shipping_boxes_weight / $this->_shipping_boxes;
        }

        if ( $set_shipping === true ) {
          if ( !class_exists('osC_Shipping') ) {
            include('includes/classes/shipping.php');
          }

          if ( !$this->hasShippingMethod() || ($this->getShippingMethod('is_cheapest') === true) ) {
            $osC_Shipping = new osC_Shipping();
            $this->setShippingMethod($osC_Shipping->getCheapestQuote(), false);
          } else {
            $osC_Shipping = new osC_Shipping($this->getShippingMethod('id'));
            $this->setShippingMethod($osC_Shipping->getQuote(), false);
          }
        }

        if ( !class_exists('osC_OrderTotal') ) {
          include('includes/classes/order_total.php');
        }

        $osC_OrderTotal = new osC_OrderTotal();
        $this->_order_totals = $osC_OrderTotal->getResult();
      }
    }

    static private function _uasortProductsByDateAdded($a, $b) {
      if ($a['date_added'] == $b['date_added']) {
        return strnatcasecmp($a['name'], $b['name']);
      }

      return ($a['date_added'] > $b['date_added']) ? -1 : 1;
    }
  }
?>

<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  include('../includes/classes/products.php');

  class osC_Products_Admin extends osC_Products {
    function getData($id) {
      global $osC_Database, $osC_Language;

      $Qproducts = $osC_Database->query('select p.*, pd.* from :table_products p, :table_products_description pd where p.products_id = :products_id and p.products_id = pd.products_id and pd.language_id = :language_id');
      $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
      $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
      $Qproducts->bindInt(':products_id', $id);
      $Qproducts->bindInt(':language_id', $osC_Language->getID());
      $Qproducts->execute();

      $data = $Qproducts->toArray();

      $Qproducts->freeResult();

      return $data;
    }

    function save($id = null, $data) {
      global $osC_Database, $osC_Language, $osC_Image;

      $error = false;

      $osC_Database->startTransaction();

      if ( is_numeric($id) ) {
        $Qproduct = $osC_Database->query('update :table_products set products_quantity = :products_quantity, products_price = :products_price, products_date_available = :products_date_available, products_weight = :products_weight, products_weight_class = :products_weight_class, products_status = :products_status, products_tax_class_id = :products_tax_class_id, manufacturers_id = :manufacturers_id, products_last_modified = now() where products_id = :products_id');
        $Qproduct->bindInt(':products_id', $id);
      } else {
        $Qproduct = $osC_Database->query('insert into :table_products (products_quantity, products_price, products_date_available, products_weight, products_weight_class, products_status, products_tax_class_id, manufacturers_id, products_date_added) values (:products_quantity, :products_price, :products_date_available, :products_weight, :products_weight_class, :products_status, :products_tax_class_id, :manufacturers_id, :products_date_added)');
        $Qproduct->bindRaw(':products_date_added', 'now()');
      }

      $Qproduct->bindTable(':table_products', TABLE_PRODUCTS);
      $Qproduct->bindInt(':products_quantity', $data['quantity']);
      $Qproduct->bindValue(':products_price', $data['price']);

      if ( date('Y-m-d') < $data['date_available'] ) {
        $Qproduct->bindValue(':products_date_available', $data['date_available']);
      } else {
        $Qproduct->bindRaw(':products_date_available', 'null');
      }

      $Qproduct->bindValue(':products_weight', $data['weight']);
      $Qproduct->bindInt(':products_weight_class', $data['weight_class']);
      $Qproduct->bindInt(':products_status', $data['status']);
      $Qproduct->bindInt(':products_tax_class_id', $data['tax_class_id']);
      $Qproduct->bindInt(':manufacturers_id', $data['manufacturers_id']);
      $Qproduct->setLogging($_SESSION['module'], $id);
      $Qproduct->execute();

      if ( $osC_Database->isError() ) {
        $error = true;
      } else {
        if ( is_numeric($id) ) {
          $products_id = $id;
        } else {
          $products_id = $osC_Database->nextID();
        }

        $Qcategories = $osC_Database->query('delete from :table_products_to_categories where products_id = :products_id');
        $Qcategories->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
        $Qcategories->bindInt(':products_id', $products_id);
        $Qcategories->setLogging($_SESSION['module'], $products_id);
        $Qcategories->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
        } else {
          if ( isset($data['categories']) && !empty($data['categories']) ) {
            foreach ($_POST['categories'] as $category_id) {
              $Qp2c = $osC_Database->query('insert into :table_products_to_categories (products_id, categories_id) values (:products_id, :categories_id)');
              $Qp2c->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
              $Qp2c->bindInt(':products_id', $products_id);
              $Qp2c->bindInt(':categories_id', $category_id);
              $Qp2c->setLogging($_SESSION['module'], $products_id);
              $Qp2c->execute();

              if ( $osC_Database->isError() ) {
                $error = true;
                break;
              }
            }
          }
        }
      }

      if ( $error === false ) {
        $images = array();

        $products_image = new upload('products_image');

        if ( $products_image->exists() ) {
          $products_image->set_destination(realpath('../images/products/originals'));

          if ( $products_image->parse() && $products_image->save() ) {
            $images[] = $products_image->filename;
          }
        }

        if ( isset($data['localimages']) ) {
          foreach ($data['localimages'] as $image) {
            $image = basename($image);

            if (file_exists('../images/products/_upload/' . $image)) {
              copy('../images/products/_upload/' . $image, '../images/products/originals/' . $image);
              @unlink('../images/products/_upload/' . $image);

              $images[] = $image;
            }
          }
        }

        $default_flag = 1;

        foreach ($images as $image) {
          $Qimage = $osC_Database->query('insert into :table_products_images (products_id, image, default_flag, sort_order, date_added) values (:products_id, :image, :default_flag, :sort_order, :date_added)');
          $Qimage->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
          $Qimage->bindInt(':products_id', $products_id);
          $Qimage->bindValue(':image', $image);
          $Qimage->bindInt(':default_flag', $default_flag);
          $Qimage->bindInt(':sort_order', 0);
          $Qimage->bindRaw(':date_added', 'now()');
          $Qimage->setLogging($_SESSION['module'], $products_id);
          $Qimage->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          } else {
            foreach ($osC_Image->getGroups() as $group) {
              if ($group['id'] != '1') {
                $osC_Image->resize($image, $group['id']);
              }
            }
          }

          $default_flag = 0;
        }
      }

      if ( $error === false ) {
        foreach ($osC_Language->getAll() as $l) {
          if ( is_numeric($id) ) {
            $Qpd = $osC_Database->query('update :table_products_description set products_name = :products_name, products_description = :products_description, products_model = :products_model, products_keyword = :products_keyword, products_tags = :products_tags, products_url = :products_url where products_id = :products_id and language_id = :language_id');
          } else {
            $Qpd = $osC_Database->query('insert into :table_products_description (products_id, language_id, products_name, products_description, products_model, products_keyword, products_tags, products_url) values (:products_id, :language_id, :products_name, :products_description, :products_model, :products_keyword, :products_tags, :products_url)');
          }

          $Qpd->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
          $Qpd->bindInt(':products_id', $products_id);
          $Qpd->bindInt(':language_id', $l['id']);
          $Qpd->bindValue(':products_name', $data['products_name'][$l['id']]);
          $Qpd->bindValue(':products_description', $data['products_description'][$l['id']]);
          $Qpd->bindValue(':products_model', $data['products_model'][$l['id']]);
          $Qpd->bindValue(':products_keyword', $data['products_keyword'][$l['id']]);
          $Qpd->bindValue(':products_tags', $data['products_tags'][$l['id']]);
          $Qpd->bindValue(':products_url', $data['products_url'][$l['id']]);
          $Qpd->setLogging($_SESSION['module'], $products_id);
          $Qpd->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
            break;
          }
        }
      }

      if ( $error === false ) {
        $attributes_array = array();

        if (isset($data['attribute_prefix']) && !empty($data['attribute_prefix']) && isset($data['attribute_price']) && !empty($data['attribute_price'])) {
          foreach ($data['attribute_prefix'] as $groups => $attributes) {
            foreach ($data['attribute_prefix'][$groups] as $key => $value) {
              if ( isset($data['attribute_price'][$groups][$key]) && !empty($data['attribute_price'][$groups][$key]) ) {
                $attributes_array[] = $groups . '-' . $key;

                $Qcheck = $osC_Database->query('select products_attributes_id from :table_products_attributes where products_id = :products_id and options_id = :options_id and options_values_id = :options_values_id');
                $Qcheck->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
                $Qcheck->bindInt(':products_id', $products_id);
                $Qcheck->bindInt(':options_id', $groups);
                $Qcheck->bindInt(':options_values_id', $key);
                $Qcheck->execute();

                if ( $Qcheck->numberOfRows() ) {
                  $Qattribute = $osC_Database->query('update :table_products_attributes set options_values_price = :options_values_price, price_prefix = :price_prefix where products_id = :products_id and options_id = :options_id and options_values_id = :options_values_id');
                } else {
                  $Qattribute = $osC_Database->query('insert into :table_products_attributes (products_id, options_id, options_values_id, options_values_price, price_prefix) values (:products_id, :options_id, :options_values_id, :options_values_price, :price_prefix)');
                }

                $Qattribute->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
                $Qattribute->bindInt(':products_id', $products_id);
                $Qattribute->bindInt(':options_id', $groups);
                $Qattribute->bindInt(':options_values_id', $key);
                $Qattribute->bindValue(':options_values_price', $data['attribute_price'][$groups][$key]);
                $Qattribute->bindValue(':price_prefix', $value);
                $Qattribute->setLogging($_SESSION['module'], $products_id);
                $Qattribute->execute();

                if ( $osC_Database->isError() ) {
                  $error = true;
                  break;
                }
              }
            }
          }
        }

        $Qcheck = $osC_Database->query('select products_attributes_id from :table_products_attributes where products_id = :products_id and concat(options_id, "-", options_values_id) not in (":attributes")');
        $Qcheck->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
        $Qcheck->bindInt(':products_id', $products_id);
        $Qcheck->bindRaw(':attributes', implode('", "', $attributes_array));
        $Qcheck->execute();

        if ( $Qcheck->numberOfRows() ) {
          while ($Qcheck->next()) {
            $Qdelete = $osC_Database->query('delete from :table_products_attributes where products_attributes_id = :products_attributes_id');
            $Qdelete->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
            $Qdelete->bindInt(':products_attributes_id', $Qcheck->valueInt('products_attributes_id'));
            $Qdelete->setLogging($_SESSION['module'], $products_id);
            $Qdelete->execute();

            if ( !$osC_Database->isError() ) {
              $Qdelete = $osC_Database->query('delete from :table_products_attributes_download where products_attributes_id = :products_attributes_id');
              $Qdelete->bindTable(':table_products_attributes_download', TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD);
              $Qdelete->bindInt(':products_attributes_id', $Qcheck->valueInt('products_attributes_id'));
              $Qdelete->setLogging($_SESSION['module'], $products_id);
              $Qdelete->execute();

              if ( $osC_Database->isError() ) {
                $error = true;
                break;
              }
            } else {
              $error = true;
              break;
            }
          }
        }
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        osC_Cache::clear('categories');
        osC_Cache::clear('category_tree');
        osC_Cache::clear('also_purchased');

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    function copy($id, $category_id, $type) {
      global $osC_Database;

      $category_array = explode('_', $category_id);

      if ( $type == 'link' ) {
        $Qcheck = $osC_Database->query('select count(*) as total from :table_products_to_categories where products_id = :products_id and categories_id = :categories_id');
        $Qcheck->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
        $Qcheck->bindInt(':products_id', $id);
        $Qcheck->bindInt(':categories_id', end($category_array));
        $Qcheck->execute();

        if ( $Qcheck->valueInt('total') < 1 ) {
          $Qcat = $osC_Database->query('insert into :table_products_to_categories (products_id, categories_id) values (:products_id, :categories_id)');
          $Qcat->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
          $Qcat->bindInt(':products_id', $id);
          $Qcat->bindInt(':categories_id', end($category_array));
          $Qcat->setLogging($_SESSION['module'], $id);
          $Qcat->execute();

          if ( $Qcat->affectedRows() ) {
            return true;
          }
        }
      } elseif ( $type == 'duplicate' ) {
        $Qproduct = $osC_Database->query('select products_quantity, products_price, products_date_available, products_weight, products_weight_class, products_tax_class_id, manufacturers_id from :table_products where products_id = :products_id');
        $Qproduct->bindTable(':table_products', TABLE_PRODUCTS);
        $Qproduct->bindInt(':products_id', $id);
        $Qproduct->execute();

        if ( $Qproduct->numberOfRows() === 1 ) {
          $error = false;

          $osC_Database->startTransaction();

          $Qnew = $osC_Database->query('insert into :table_products (products_quantity, products_price, products_date_added, products_date_available, products_weight, products_weight_class, products_status, products_tax_class_id, manufacturers_id) values (:products_quantity, :products_price, now(), :products_date_available, :products_weight, :products_weight_class, 0, :products_tax_class_id, :manufacturers_id)');
          $Qnew->bindTable(':table_products', TABLE_PRODUCTS);
          $Qnew->bindInt(':products_quantity', $Qproduct->valueInt('products_quantity'));
          $Qnew->bindValue(':products_price', $Qproduct->value('products_price'));

          if ( !osc_empty($Qproduct->value('products_date_available')) ) {
            $Qnew->bindValue(':products_date_available', $Qproduct->value('products_date_available'));
          } else {
            $Qnew->bindRaw(':products_date_available', 'null');
          }

          $Qnew->bindValue(':products_weight', $Qproduct->value('products_weight'));
          $Qnew->bindInt(':products_weight_class', $Qproduct->valueInt('products_weight_class'));
          $Qnew->bindInt(':products_tax_class_id', $Qproduct->valueInt('products_tax_class_id'));
          $Qnew->bindInt(':manufacturers_id', $Qproduct->valueInt('manufacturers_id'));
          $Qnew->setLogging($_SESSION['module']);
          $Qnew->execute();

          if ( $Qnew->affectedRows() ) {
            $new_product_id = $osC_Database->nextID();

            $Qdesc = $osC_Database->query('select language_id, products_name, products_description, products_model, products_tags, products_url from :table_products_description where products_id = :products_id');
            $Qdesc->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
            $Qdesc->bindInt(':products_id', $id);
            $Qdesc->execute();

            while ($Qdesc->next()) {
              $Qnewdesc = $osC_Database->query('insert into :table_products_description (products_id, language_id, products_name, products_description, products_model, products_tags, products_url, products_viewed) values (:products_id, :language_id, :products_name, :products_description, :products_model, :products_tags, :products_url, 0)');
              $Qnewdesc->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
              $Qnewdesc->bindInt(':products_id', $new_product_id);
              $Qnewdesc->bindInt(':language_id', $Qdesc->valueInt('language_id'));
              $Qnewdesc->bindValue(':products_name', $Qdesc->value('products_name'));
              $Qnewdesc->bindValue(':products_model', $Qdesc->value('products_model'));
              $Qnewdesc->bindValue(':products_tags', $Qdesc->value('products_tags'));
              $Qnewdesc->bindValue(':products_description', $Qdesc->value('products_description'));
              $Qnewdesc->bindValue(':products_url', $Qdesc->value('products_url'));
              $Qnewdesc->setLogging($_SESSION['module'], $new_product_id);
              $Qnewdesc->execute();

              if ($osC_Database->isError()) {
                $error = true;
                break;
              }
            }

            if ( $error === false ) {
              $Qp2c = $osC_Database->query('insert into :table_products_to_categories (products_id, categories_id) values (:products_id, :categories_id)');
              $Qp2c->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
              $Qp2c->bindInt(':products_id', $new_product_id);
              $Qp2c->bindInt(':categories_id', end($category_array));
              $Qp2c->setLogging($_SESSION['module'], $new_product_id);
              $Qp2c->execute();

              if ( $osC_Database->isError() ) {
                $error = true;
              }
            }
          } else {
            $error = true;
          }

          if ( $error === false ) {
            $osC_Database->commitTransaction();

            osC_Cache::clear('categories');
            osC_Cache::clear('category_tree');
            osC_Cache::clear('also_purchased');

            return true;
          } else {
            $osC_Database->rollbackTransaction();
          }
        }
      }

      return false;
    }

    function delete($id, $categories = null) {
      global $osC_Database, $osC_Image;

      $delete_product = true;
      $error = false;

      $osC_Database->startTransaction();

      if ( is_array($categories) && !empty($categories) ) {
        $Qpc = $osC_Database->query('delete from :table_products_to_categories where products_id = :products_id and categories_id in :categories_id');
        $Qpc->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
        $Qpc->bindInt(':products_id', $id);
        $Qpc->bindRaw(':categories_id', '("' . implode('", "', $categories) . '")');
        $Qpc->setLogging($_SESSION['module'], $id);
        $Qpc->execute();

        if ( !$osC_Database->isError() ) {
          $Qcheck = $osC_Database->query('select products_id from :table_products_to_categories where products_id = :products_id limit 1');
          $Qcheck->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
          $Qcheck->bindInt(':products_id', $id);
          $Qcheck->execute();

          if ( $Qcheck->numberOfRows() > 0 ) {
            $delete_product = false;
          }
        } else {
          $error = true;
        }
      }

      if ( ($error === false) && ($delete_product === true) ) {
        $Qr = $osC_Database->query('delete from :table_reviews where products_id = :products_id');
        $Qr->bindTable(':table_reviews', TABLE_REVIEWS);
        $Qr->bindInt(':products_id', $id);
        $Qr->setLogging($_SESSION['module'], $id);
        $Qr->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
        }

        if ( $error === false ) {
          $Qcb = $osC_Database->query('delete from :table_customers_basket where products_id = :products_id or products_id like :products_id');
          $Qcb->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
          $Qcb->bindInt(':products_id', $id);
          $Qcb->bindValue(':products_id', (int)$id . '#%');
          $Qcb->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }

        if ( $error === false ) {
          $Qp2c = $osC_Database->query('delete from :table_products_to_categories where products_id = :products_id');
          $Qp2c->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
          $Qp2c->bindInt(':products_id', $id);
          $Qp2c->setLogging($_SESSION['module'], $id);
          $Qp2c->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }

        if ( $error === false ) {
          $Qs = $osC_Database->query('delete from :table_specials where products_id = :products_id');
          $Qs->bindTable(':table_specials', TABLE_SPECIALS);
          $Qs->bindInt(':products_id', $id);
          $Qs->setLogging($_SESSION['module'], $id);
          $Qs->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }

        if ( $error === false ) {
          $Qpa = $osC_Database->query('delete from :table_products_attributes where products_id = :products_id');
          $Qpa->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
          $Qpa->bindInt(':products_id', $id);
          $Qpa->setLogging($_SESSION['module'], $id);
          $Qpa->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }

        if ( $error === false ) {
          $Qpd = $osC_Database->query('delete from :table_products_description where products_id = :products_id');
          $Qpd->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
          $Qpd->bindInt(':products_id', $id);
          $Qpd->setLogging($_SESSION['module'], $id);
          $Qpd->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }

        if ( $error === false ) {
          $Qp = $osC_Database->query('delete from :table_products where products_id = :products_id');
          $Qp->bindTable(':table_products', TABLE_PRODUCTS);
          $Qp->bindInt(':products_id', $id);
          $Qp->setLogging($_SESSION['module'], $id);
          $Qp->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }

        if ( $error === false ) {
          $Qim = $osC_Database->query('select id from :table_products_images where products_id = :products_id');
          $Qim->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
          $Qim->bindInt(':products_id', $id);
          $Qim->execute();

          while ($Qim->next()) {
            $osC_Image->delete($Qim->valueInt('id'));
          }
        }
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        osC_Cache::clear('categories');
        osC_Cache::clear('category_tree');
        osC_Cache::clear('also_purchased');

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    function setDateAvailable($id, $data) {
      global $osC_Database;

      $Qproduct = $osC_Database->query('update :table_products set products_date_available = :products_date_available, products_last_modified = now() where products_id = :products_id');
      $Qproduct->bindTable(':table_products', TABLE_PRODUCTS);

      if ( date('Y-m-d') < $data['date_available'] ) {
        $Qproduct->bindValue(':products_date_available', $data['date_available']);
      } else {
        $Qproduct->bindRaw(':products_date_available', 'null');
      }

      $Qproduct->bindInt(':products_id', $id);
      $Qproduct->setLogging($_SESSION['module'], $id);
      $Qproduct->execute();

      if ( !$osC_Database->isError() ) {
        return true;
      }

      return false;
    }

    function getKeywordCount($keyword, $id = null) {
      global $osC_Database;

      $Qkeywords = $osC_Database->query('select count(*) as total from :table_products_description where products_keyword = :products_keyword');

      if ( is_numeric($id) ) {
        $Qkeywords->appendQuery('and products_id != :products_id');
        $Qkeywords->bindInt(':products_id', $id);
      }

      $Qkeywords->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
      $Qkeywords->bindValue(':products_keyword', $keyword);
      $Qkeywords->execute();

      return $Qkeywords->valueInt('total');
    }
  }
?>

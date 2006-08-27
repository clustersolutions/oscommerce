<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  include('../includes/classes/product.php');

  class osC_Product_Admin extends osC_Product {

// class methods
    function remove($id, $categories = null) {
      global $osC_Database, $osC_Image;

      if (!empty($categories) && !is_array($categories)) {
        return false;
      }

      $delete_product = true;
      $error = false;

      $osC_Database->startTransaction();

      if (!empty($categories)) {
        $Qpc = $osC_Database->query('delete from :table_products_to_categories where products_id = :products_id and categories_id in :categories_id');
        $Qpc->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
        $Qpc->bindInt(':products_id', $id);
        $Qpc->bindRaw(':categories_id', '("' . implode('", "', $categories) . '")');
        $Qpc->execute();

        if (!$osC_Database->isError()) {
          $Qcheck = $osC_Database->query('select products_id from :table_products_to_categories where products_id = :products_id limit 1');
          $Qcheck->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
          $Qcheck->bindInt(':products_id', $id);
          $Qcheck->execute();

          if ($Qcheck->numberOfRows() > 0) {
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
        $Qr->execute();

        if ($osC_Database->isError()) {
          $error = true;
        }

        if ($error === false) {
          $Qcb = $osC_Database->query('delete from :table_customers_basket where products_id = :products_id or products_id like :products_id');
          $Qcb->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
          $Qcb->bindInt(':products_id', $id);
          $Qcb->bindValue(':products_id', (int)$id . '#%');
          $Qcb->execute();

          if ($osC_Database->isError()) {
            $error = true;
          }
        }

        if ($error === false) {
          $Qp2c = $osC_Database->query('delete from :table_products_to_categories where products_id = :products_id');
          $Qp2c->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
          $Qp2c->bindInt(':products_id', $id);
          $Qp2c->execute();

          if ($osC_Database->isError()) {
            $error = true;
          }
        }

        if ($error === false) {
          $Qs = $osC_Database->query('delete from :table_specials where products_id = :products_id');
          $Qs->bindTable(':table_specials', TABLE_SPECIALS);
          $Qs->bindInt(':products_id', $id);
          $Qs->execute();

          if ($osC_Database->isError()) {
            $error = true;
          }
        }

        if ($error === false) {
          $Qpa = $osC_Database->query('delete from :table_products_attributes where products_id = :products_id');
          $Qpa->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
          $Qpa->bindInt(':products_id', $id);
          $Qpa->execute();

          if ($osC_Database->isError()) {
            $error = true;
          }
        }

        if ($error === false) {
          $Qpd = $osC_Database->query('delete from :table_products_description where products_id = :products_id');
          $Qpd->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
          $Qpd->bindInt(':products_id', $id);
          $Qpd->execute();

          if ($osC_Database->isError()) {
            $error = true;
          }
        }

        if ($error === false) {
          $Qp = $osC_Database->query('delete from :table_products where products_id = :products_id');
          $Qp->bindTable(':table_products', TABLE_PRODUCTS);
          $Qp->bindInt(':products_id', $id);
          $Qp->execute();

          if ($osC_Database->isError()) {
            $error = true;
          }
        }

        if ($error === false) {
          $Qim = $osC_Database->query('select id from :table_products_images where products_id = :products_id');
          $Qim->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
          $Qim->bindInt(':products_id', $id);
          $Qim->execute();

          while ($Qim->next()) {
            $osC_Image->delete($Qim->valueInt('id'));
          }
        }

        if ($error === false) {
          $osC_Database->commitTransaction();

          osC_Cache::clear('categories');
          osC_Cache::clear('category_tree');
          osC_Cache::clear('also_purchased');

          return true;
        } else {
          $osC_Database->rollbackTransaction();
        }
      }

      return false;
    }
  }
?>

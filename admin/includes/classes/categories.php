<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Categories_Admin {
    function getData($id, $language_id = null) {
      global $osC_Database, $osC_Language, $osC_CategoryTree;

      if ( empty($language_id) ) {
        $language_id = $osC_Language->getID();
      }

      $Qcategories = $osC_Database->query('select c.*, cd.* from :table_categories c, :table_categories_description cd where c.categories_id = :categories_id and c.categories_id = cd.categories_id and cd.language_id = :language_id');
      $Qcategories->bindTable(':table_categories', TABLE_CATEGORIES);
      $Qcategories->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
      $Qcategories->bindInt(':categories_id', $id);
      $Qcategories->bindInt(':language_id', $language_id);
      $Qcategories->execute();

      $data = $Qcategories->toArray();

      $data['childs_count'] = sizeof($osC_CategoryTree->getChildren($Qcategories->valueInt('categories_id'), $dummy = array()));
      $data['products_count'] = $osC_CategoryTree->getNumberOfProducts($Qcategories->valueInt('categories_id'));

      $Qcategories->freeResult();

      return $data;
    }

    function save($id = null, $data) {
      global $osC_Database, $osC_Language;

      $category_id = '';
      $error = false;

      $osC_Database->startTransaction();

      if ( is_numeric($id) ) {
        $Qcat = $osC_Database->query('update :table_categories set sort_order = :sort_order, last_modified = now() where categories_id = :categories_id');
        $Qcat->bindInt(':categories_id', $id);
      } else {
        $Qcat = $osC_Database->query('insert into :table_categories (parent_id, sort_order, date_added) values (:parent_id, :sort_order, now())');
        $Qcat->bindInt(':parent_id', $data['parent_id']);
      }

      $Qcat->bindTable(':table_categories', TABLE_CATEGORIES);
      $Qcat->bindInt(':sort_order', $data['sort_order']);
      $Qcat->setLogging($_SESSION['module'], $id);
      $Qcat->execute();

      if ( !$osC_Database->isError() ) {
        $category_id = (is_numeric($id)) ? $id : $osC_Database->nextID();

        foreach ($osC_Language->getAll() as $l) {
          if ( is_numeric($id) ) {
            $Qcd = $osC_Database->query('update :table_categories_description set categories_name = :categories_name where categories_id = :categories_id and language_id = :language_id');
          } else {
            $Qcd = $osC_Database->query('insert into :table_categories_description (categories_id, language_id, categories_name) values (:categories_id, :language_id, :categories_name)');
          }

          $Qcd->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
          $Qcd->bindInt(':categories_id', $category_id);
          $Qcd->bindInt(':language_id', $l['id']);
          $Qcd->bindValue(':categories_name', $data['name'][$l['id']]);
          $Qcd->setLogging($_SESSION['module'], $category_id);
          $Qcd->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
            break;
          }
        }

        if ( $error === false ) {
          $categories_image = new upload($data['image'], realpath('../' . DIR_WS_IMAGES . 'categories'));

          if ( $categories_image->exists() && $categories_image->parse() && $categories_image->save() ) {
            $Qcf = $osC_Database->query('update :table_categories set categories_image = :categories_image where categories_id = :categories_id');
            $Qcf->bindTable(':table_categories', TABLE_CATEGORIES);
            $Qcf->bindValue(':categories_image', $categories_image->filename);
            $Qcf->bindInt(':categories_id', $category_id);
            $Qcf->setLogging($_SESSION['module'], $category_id);
            $Qcf->execute();

            if ( $osC_Database->isError() ) {
              $error = true;
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

    function delete($id) {
      global $osC_Database, $osC_CategoryTree;

      if ( is_numeric($id) ) {
        $osC_CategoryTree->setBreadcrumbUsage(false);

        $categories = array_merge(array(array('id' => $id, 'text' => '')), $osC_CategoryTree->getTree($id));
        $products = array();
        $products_delete = array();

        foreach ($categories as $c_entry) {
          $Qproducts = $osC_Database->query('select products_id from :table_products_to_categories where categories_id = :categories_id');
          $Qproducts->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
          $Qproducts->bindInt(':categories_id', $c_entry['id']);
          $Qproducts->execute();

          while ($Qproducts->next()) {
            $products[$Qproducts->valueInt('products_id')]['categories'][] = $c_entry['id'];
          }
        }

        foreach ($products as $key => $value) {
          $Qcheck = $osC_Database->query('select count(*) as total from :table_products_to_categories where products_id = :products_id and categories_id not in :categories_id');
          $Qcheck->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
          $Qcheck->bindInt(':products_id', $key);
          $Qcheck->bindRaw(':categories_id', '("' . implode('", "', $value['categories']) . '")');
          $Qcheck->execute();

          if ($Qcheck->valueInt('total') < 1) {
            $products_delete[$key] = $key;
          }
        }

        osc_set_time_limit(0);

        foreach ($categories as $c_entry) {
          $osC_Database->startTransaction();

          $Qimage = $osC_Database->query('select categories_image from :table_categories where categories_id = :categories_id');
          $Qimage->bindTable(':table_categories', TABLE_CATEGORIES);
          $Qimage->bindInt(':categories_id', $c_entry['id']);
          $Qimage->execute();

          $Qc = $osC_Database->query('delete from :table_categories where categories_id = :categories_id');
          $Qc->bindTable(':table_categories', TABLE_CATEGORIES);
          $Qc->bindInt(':categories_id', $c_entry['id']);
          $Qc->setLogging($_SESSION['module'], $id);
          $Qc->execute();

          if ( !$osC_Database->isError() ) {
            $Qcd = $osC_Database->query('delete from :table_categories_description where categories_id = :categories_id');
            $Qcd->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
            $Qcd->bindInt(':categories_id', $c_entry['id']);
            $Qcd->setLogging($_SESSION['module'], $id);
            $Qcd->execute();

            if ( !$osC_Database->isError() ) {
              $Qp2c = $osC_Database->query('delete from :table_products_to_categories where categories_id = :categories_id');
              $Qp2c->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
              $Qp2c->bindInt(':categories_id', $c_entry['id']);
              $Qp2c->setLogging($_SESSION['module'], $id);
              $Qp2c->execute();

              if ( !$osC_Database->isError() ) {
                $osC_Database->commitTransaction();

                osC_Cache::clear('categories');
                osC_Cache::clear('category_tree');
                osC_Cache::clear('also_purchased');

                if ( !osc_empty($Qimage->value('categories_image')) ) {
                  $Qcheck = $osC_Database->query('select count(*) as total from :table_categories where categories_image = :categories_image');
                  $Qcheck->bindTable(':table_categories', TABLE_CATEGORIES);
                  $Qcheck->bindValue(':categories_image', $Qimage->value('categories_image'));
                  $Qcheck->execute();

                  if ( $Qcheck->numberOfRows() === 0 ) {
                    if (file_exists(realpath('../' . DIR_WS_IMAGES . 'categories/' . $Qimage->value('categories_image')))) {
                      @unlink(realpath('../' . DIR_WS_IMAGES . 'categories/' . $Qimage->value('categories_image')));
                    }
                  }
                }
              } else {
                $osC_Database->rollbackTransaction();
              }
            } else {
              $osC_Database->rollbackTransaction();
            }
          } else {
            $osC_Database->rollbackTransaction();
          }
        }

        foreach ($products_delete as $id) {
          osC_Products_Admin::remove($id);
        }

        osC_Cache::clear('categories');
        osC_Cache::clear('category_tree');
        osC_Cache::clear('also_purchased');

        return true;
      }

      return false;
    }

    function move($id, $new_id) {
      global $osC_Database;

      $category_array = explode('_', $new_id);

      if ( in_array($id, $category_array)) {
        return false;
      }

      $Qupdate = $osC_Database->query('update :table_categories set parent_id = :parent_id, last_modified = now() where categories_id = :categories_id');
      $Qupdate->bindTable(':table_categories', TABLE_CATEGORIES);
      $Qupdate->bindInt(':parent_id', end($category_array));
      $Qupdate->bindInt(':categories_id', $id);
      $Qupdate->setLogging($_SESSION['module'], $id);
      $Qupdate->execute();

      osC_Cache::clear('categories');
      osC_Cache::clear('category_tree');
      osC_Cache::clear('also_purchased');

      return true;
    }
  }
?>

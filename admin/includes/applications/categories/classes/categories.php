<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Categories_Admin {
    public static function get($id, $language_id = null, $key = null) {
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

      if ( !empty($key) && isset($data[$key]) ) {
        $data = $data[$key];
      }

      return $data;
    }

    public static function getAll($id = null) {
      global $osC_Database, $osC_Language, $current_category_id;

      if ( !is_numeric($id) ) {
        if ( isset($current_category_id) && is_numeric($current_category_id) ) {
          $id = $current_category_id;
        } else {
          $id = 0;
        }
      }

      $result = array('entries' => array());

      $Qcategories = $osC_Database->query('select c.*, cd.categories_name from :table_categories c, :table_categories_description cd where c.categories_id = cd.categories_id and cd.language_id = :language_id and');

      if ( $id > 0 ) {
        $Qcategories->appendQuery('c.parent_id = :parent_id');
        $Qcategories->bindInt(':parent_id', $id);
      } else {
        $Qcategories->appendQuery('c.parent_id is null');
      }

      $Qcategories->appendQuery('order by c.sort_order, cd.categories_name');
      $Qcategories->bindTable(':table_categories', TABLE_CATEGORIES);
      $Qcategories->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
      $Qcategories->bindInt(':language_id', $osC_Language->getID());
      $Qcategories->execute();

      while ( $Qcategories->next() ) {
        $result['entries'][] = $Qcategories->toArray();
      }

      $result['total'] = $Qcategories->numberOfRows();

      $Qcategories->freeResult();

      return $result;
    }

    public static function find($search, $id = null) {
      global $osC_Database, $osC_Language, $current_category_id;

      if ( !is_numeric($id) ) {
        if ( isset($current_category_id) && is_numeric($current_category_id) ) {
          $id = $current_category_id;
        } else {
          $id = 0;
        }
      }

      $osC_CategoryTree = new osC_CategoryTree_Admin();
      $osC_CategoryTree->setRootCategoryID($id);

      $categories = array();

      $Qcategories = $osC_Database->query('select c.categories_id from :table_categories c, :table_categories_description cd where c.categories_id = cd.categories_id and cd.language_id = :language_id and (cd.categories_name like :categories_name)');
      $Qcategories->bindTable(':table_categories', TABLE_CATEGORIES);
      $Qcategories->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
      $Qcategories->bindInt(':language_id', $osC_Language->getID());
      $Qcategories->bindValue(':categories_name', '%' . $search . '%');
      $Qcategories->execute();

      while ( $Qcategories->next() ) {
        if ( $Qcategories->valueInt('categories_id') != $id ) {
          $category_path = $osC_CategoryTree->getPathArray($Qcategories->valueInt('categories_id'));
          $top_category_id = $category_path[0]['id'];

          if ( !in_array($top_category_id, $categories) ) {
            $categories[] = $top_category_id;
          }
        }
      }

      $result = array('entries' => array());

      $Qcategories = $osC_Database->query('select c.*, cd.categories_name from :table_categories c, :table_categories_description cd where c.categories_id = cd.categories_id and cd.language_id = :language_id and c.categories_id in :categories_id order by c.sort_order, cd.categories_name');
      $Qcategories->bindTable(':table_categories', TABLE_CATEGORIES);
      $Qcategories->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
      $Qcategories->bindInt(':language_id', $osC_Language->getID());
      $Qcategories->bindRaw(':categories_id', '("' . implode('", "', $categories) . '")');
      $Qcategories->execute();

      while ( $Qcategories->next() ) {
        $result['entries'][] = $Qcategories->toArray();
      }

      $result['total'] = $Qcategories->numberOfRows();

      $Qcategories->freeResult();

      return $result;
    }

    public static function save($id = null, $data) {
      global $osC_Database, $osC_Language;

      $category_id = '';
      $error = false;

      $osC_Database->startTransaction();

      if ( is_numeric($id) ) {
        $Qcat = $osC_Database->query('update :table_categories set sort_order = :sort_order, last_modified = now() where categories_id = :categories_id');
        $Qcat->bindInt(':categories_id', $id);
      } else {
        $Qcat = $osC_Database->query('insert into :table_categories (parent_id, sort_order, date_added) values (:parent_id, :sort_order, now())');

        if ( $data['parent_id'] > 0 ) {
          $Qcat->bindInt(':parent_id', $data['parent_id']);
        } else {
          $Qcat->bindRaw(':parent_id', 'null');
        }
      }

      $Qcat->bindTable(':table_categories', TABLE_CATEGORIES);
      $Qcat->bindInt(':sort_order', $data['sort_order']);
      $Qcat->setLogging($_SESSION['module'], $id);
      $Qcat->execute();

      if ( !$osC_Database->isError() ) {
        $category_id = (is_numeric($id)) ? $id : $osC_Database->nextID();

        foreach ( $osC_Language->getAll() as $l ) {
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

    public static function delete($id) {
      global $osC_Database;

      $Qc = $osC_Database->query('delete from :table_categories where categories_id = :categories_id');
      $Qc->bindTable(':table_categories', TABLE_CATEGORIES);
      $Qc->bindInt(':categories_id', $id);
      $Qc->setLogging($_SESSION['module'], $id);
      $Qc->execute();

      if ( !$osC_Database->isError() ) {
        osC_Cache::clear('categories');
        osC_Cache::clear('category_tree');
        osC_Cache::clear('also_purchased');

        return true;
      }

      return false;
    }

    public static function move($id, $new_id) {
      global $osC_Database;

      $category_array = explode('_', $new_id);

      if ( in_array($id, $category_array)) {
        return false;
      }

      $parent_id = end($category_array);

      $Qupdate = $osC_Database->query('update :table_categories set parent_id = :parent_id, last_modified = now() where categories_id = :categories_id');
      $Qupdate->bindTable(':table_categories', TABLE_CATEGORIES);

      if ( $parent_id > 0 ) {
        $Qupdate->bindInt(':parent_id', $parent_id);
      } else {
        $Qupdate->bindRaw(':parent_id', 'null');
      }

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

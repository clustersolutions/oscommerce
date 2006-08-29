<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

// calculate category path
  $cPath = (isset($_GET['cPath']) ? $_GET['cPath'] : '');

  if (!empty($cPath)) {
    $cPath_array = osc_parse_category_path($cPath);
    $cPath = implode('_', $cPath_array);
    $current_category_id = end($cPath_array);
  } else {
    $current_category_id = 0;
  }

  require('includes/classes/category_tree.php');
  $osC_CategoryTree = new osC_CategoryTree_Admin();
  $osC_CategoryTree->setSpacerString('&nbsp;', 2);

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
    $_GET['page'] = 1;
  }

  if (!isset($_GET['search'])) {
    $_GET['search'] = '';
  }

  if (!empty($action)) {
    switch ($action) {
      case 'save_category':
        $category_id = '';
        $error = false;

        $osC_Database->startTransaction();

        if (isset($_GET['cID']) && is_numeric($_GET['cID'])) {
          $Qcat = $osC_Database->query('update :table_categories set sort_order = :sort_order, last_modified = now() where categories_id = :categories_id');
          $Qcat->bindInt(':categories_id', $_GET['cID']);
        } else {
          $Qcat = $osC_Database->query('insert into :table_categories (parent_id, sort_order, date_added) values (:parent_id, :sort_order, now())');
          $Qcat->bindInt(':parent_id', $current_category_id);
        }
        $Qcat->bindTable(':table_categories', TABLE_CATEGORIES);
        $Qcat->bindInt(':sort_order', $_POST['sort_order']);
        $Qcat->execute();

        if ($osC_Database->isError() === false) {
          $category_id = (isset($_GET['cID']) && is_numeric($_GET['cID'])) ? $_GET['cID'] : $osC_Database->nextID();

          foreach ($osC_Language->getAll() as $l) {
            if (isset($_GET['cID']) && is_numeric($_GET['cID'])) {
              $Qcd = $osC_Database->query('update :table_categories_description set categories_name = :categories_name where categories_id = :categories_id and language_id = :language_id');
            } else {
              $Qcd = $osC_Database->query('insert into :table_categories_description (categories_id, language_id, categories_name) values (:categories_id, :language_id, :categories_name)');
            }
            $Qcd->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
            $Qcd->bindInt(':categories_id', $category_id);
            $Qcd->bindInt(':language_id', $l['id']);
            $Qcd->bindValue(':categories_name', $_POST['categories_name'][$l['id']]);
            $Qcd->execute();

            if ($osC_Database->isError()) {
              $error = true;
              break;
            }
          }

          if (($error === false) && ($categories_image = new upload('categories_image', realpath('../' . DIR_WS_IMAGES . 'categories')))) {
            $Qcf = $osC_Database->query('update :table_categories set categories_image = :categories_image where categories_id = :categories_id');
            $Qcf->bindTable(':table_categories', TABLE_CATEGORIES);
            $Qcf->bindValue(':categories_image', $categories_image->filename);
            $Qcf->bindInt(':categories_id', $category_id);
            $Qcf->execute();

            if ($osC_Database->isError()) {
              $error = true;
            }
          }
        } else {
          $error = true;
        }

        if ($error === false) {
          $osC_Database->commitTransaction();

          osC_Cache::clear('categories');
          osC_Cache::clear('category_tree');
          osC_Cache::clear('also_purchased');

          $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_Database->rollbackTransaction();

          $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        osc_redirect(osc_href_link_admin(FILENAME_CATEGORIES, 'page=' . $_GET['page'] . '&cPath=' . $cPath . '&cID=' . $category_id));
        break;
      case 'delete_category_confirm':
        if (isset($_GET['cID']) && is_numeric($_GET['cID'])) {
          include('includes/classes/product.php');

          $osC_CategoryTree->setBreadcrumbUsage(false);

          $categories = array_merge(array(array('id' => $_GET['cID'], 'text' => '')), $osC_CategoryTree->getTree($_GET['cID']));
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
            osc_remove_category($c_entry['id']);
          }

          foreach ($products_delete as $id) {
            osC_Product_Admin::remove($id);
          }

          osC_Cache::clear('categories');
          osC_Cache::clear('category_tree');
          osC_Cache::clear('also_purchased');

          $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
        }

        osc_redirect(osc_href_link_admin(FILENAME_CATEGORIES, 'page=' . $_GET['page'] . '&cPath=' . $cPath . '&search=' . $_GET['search']));
        break;
      case 'move_category_confirm':
        $category_array = explode('_', $_POST['move_to_category_id']);

        if (isset($_GET['cID']) && ($_GET['cID'] != end($category_array))) {
          $path = explode('_', $_POST['move_to_category_id']);

          if (in_array($_GET['cID'], $path)) {
            $osC_MessageStack->add_session('header', ERROR_CANNOT_MOVE_CATEGORY_TO_PARENT, 'error');
          } else {
            $Qupdate = $osC_Database->query('update :table_categories set parent_id = :parent_id, last_modified = now() where categories_id = :categories_id');
            $Qupdate->bindTable(':table_categories', TABLE_CATEGORIES);
            $Qupdate->bindInt(':parent_id', end($category_array));
            $Qupdate->bindInt(':categories_id', $_GET['cID']);
            $Qupdate->execute();

            if ($Qupdate->affectedRows()) {
              osC_Cache::clear('categories');
              osC_Cache::clear('category_tree');
              osC_Cache::clear('also_purchased');

              $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');

              osc_redirect(osc_href_link_admin(FILENAME_CATEGORIES, 'page=' . $_GET['page'] . '&cPath=' . $cPath . '&search=' . $_GET['search']));
            }
          }
        }

        osc_redirect(osc_href_link_admin(FILENAME_CATEGORIES, 'page=' . $_GET['page'] . '&cPath=' . $cPath . '&search=' . $_GET['search'] . '&cID=' . $_GET['cID']));
        break;
    }
  }

  $page_contents = 'categories.php';

  require('templates/default.php');

  require('includes/application_bottom.php');
?>

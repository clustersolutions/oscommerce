<?php
/*
  $Id: products.php,v 1.18 2004/11/20 02:08:19 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

// calculate category path
  $cPath = (isset($_GET['cPath']) ? $_GET['cPath'] : '');

  if (!empty($cPath)) {
    $cPath_array = tep_parse_category_path($cPath);
    $cPath = implode('_', $cPath_array);
    $current_category_id = end($cPath_array);
  } else {
    $current_category_id = 0;
  }

  require('../includes/classes/category_tree.php');
  $osC_CategoryTree = new osC_CategoryTree();
  $osC_CategoryTree->setSpacerString('&nbsp;', 2);

  $categories_array = array();
  foreach ($osC_CategoryTree->getTree() as $value) {
    $categories_array[] = array('id' => $value['id'], 'text' => $value['title']);
  }

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
    $_GET['page'] = 1;
  }

  if (!isset($_GET['search'])) {
    $_GET['search'] = '';
  }

  if (!empty($action)) {
    switch ($action) {
      case 'delete_product_confirm':
        if (isset($_GET['pID']) && isset($_POST['product_categories']) && is_array($_POST['product_categories']) && !empty($_POST['product_categories'])) {
          $Qpc = $osC_Database->query('delete from :table_products_to_categories where products_id = :products_id and categories_id in :categories_id');
          $Qpc->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
          $Qpc->bindInt(':products_id', $_GET['pID']);
          $Qpc->bindRaw(':categories_id', '("' . implode('", "', $_POST['product_categories']) . '")');
          $Qpc->execute();

          if ($osC_Database->isError() === false) {
            $Qcheck = $osC_Database->query('select count(*) as total from :table_products_to_categories where products_id = :products_id');
            $Qcheck->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
            $Qcheck->bindInt(':products_id', $_GET['pID']);
            $Qcheck->execute();

            if ($Qcheck->valueInt('total') < 1) {
              tep_remove_product($_GET['pID']);
            }

            osC_Cache::clear('categories');
            osC_Cache::clear('category_tree');
            osC_Cache::clear('also_purchased');

            $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
          }
        }

        tep_redirect(tep_href_link(FILENAME_PRODUCTS, 'page=' . $_GET['page'] . '&cPath=' . $cPath . '&search=' . $_GET['search']));
        break;
      case 'move_product_confirm':
        if (isset($_GET['pID']) && is_numeric($_GET['pID'])) {
          $Qcheck = $osC_Database->query('select count(*) as total from :table_products_to_categories where products_id = :products_id and categories_id = :categories_id');
          $Qcheck->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
          $Qcheck->bindInt(':products_id', $_GET['pID']);
          $Qcheck->bindInt(':categories_id', end(explode('_', $_POST['move_to_category_id'])));
          $Qcheck->execute();

          if ($Qcheck->valueInt('total') < 1) {
            $Qupdate = $osC_Database->query('update :table_products_to_categories set categories_id = :categories_id where products_id = :products_id and categories_id = :current_categories_id');
            $Qupdate->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
            $Qupdate->bindInt(':categories_id', end(explode('_', $_POST['move_to_category_id'])));
            $Qupdate->bindInt(':products_id', $_GET['pID']);
            $Qupdate->bindInt(':current_categories_id', $current_category_id);
            $Qupdate->execute();

            if ($Qupdate->affectedRows()) {
              osC_Cache::clear('categories');
              osC_Cache::clear('category_tree');
              osC_Cache::clear('also_purchased');

              $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');

              tep_redirect(tep_href_link(FILENAME_PRODUCTS, 'page=' . $_GET['page'] . '&cPath=' . $_POST['move_to_category_id'] . '&search=' . $_GET['search'] . '&pID=' . $_GET['pID']));
            } else {
              $osC_MessageStack->add_session('header', WARNING_DB_ROWS_NOT_UPDATED, 'warning');
            }
          }
        }

        tep_redirect(tep_href_link(FILENAME_PRODUCTS, 'page=' . $_GET['page'] . '&cPath=' . $cPath . '&search=' . $_GET['search'] . '&pID=' . $_GET['pID']));
        break;
      case 'save_product':
        if (isset($_POST['product_edit'])) {
          $action = 'new_product';
        } else {
          $error = false;

          $osC_Database->startTransaction();

          if (isset($_GET['pID']) && is_numeric($_GET['pID'])) {
            $Qproduct = $osC_Database->query('update :table_products set products_quantity = :products_quantity, products_model = :products_model, products_price = :products_price, products_date_available = :products_date_available, products_weight = :products_weight, products_weight_class = :products_weight_class, products_status = :products_status, products_tax_class_id = :products_tax_class_id, manufacturers_id = :manufacturers_id, products_last_modified = now() where products_id = :products_id');
            $Qproduct->bindInt(':products_id', $_GET['pID']);
          } else {
            $Qproduct = $osC_Database->query('insert into :table_products (products_quantity, products_model, products_price, products_date_available, products_weight, products_weight_class, products_status, products_tax_class_id, manufacturers_id, products_date_added) values (:products_quantity, :products_model, :products_price, :products_date_available, :products_weight, :products_weight_class, :products_status, :products_tax_class_id, :manufacturers_id, now())');
          }
          $Qproduct->bindTable(':table_products', TABLE_PRODUCTS);
          $Qproduct->bindInt(':products_quantity', $_POST['products_quantity']);
          $Qproduct->bindValue(':products_model', $_POST['products_model']);
          $Qproduct->bindValue(':products_price', $_POST['products_price']);
          if (date('Y-m-d') < $_POST['products_date_available']) {
            $Qproduct->bindValue(':products_date_available', $_POST['products_date_available']);
          } else {
            $Qproduct->bindRaw(':products_date_available', 'null');
          }
          $Qproduct->bindValue(':products_weight', $_POST['products_weight']);
          $Qproduct->bindInt(':products_weight_class', $_POST['products_weight_class']);
          $Qproduct->bindInt(':products_status', $_POST['products_status']);
          $Qproduct->bindInt(':products_tax_class_id', $_POST['products_tax_class_id']);
          $Qproduct->bindInt(':manufacturers_id', $_POST['manufacturers_id']);
          $Qproduct->execute();

          if ($osC_Database->isError()) {
            $error = true;
          } else {
            if (isset($_GET['pID']) && is_numeric($_GET['pID'])) {
              $products_id = $_GET['pID'];
            } else {
              $products_id = $osC_Database->nextID();
            }

            $Qcategories = $osC_Database->query('delete from :table_products_to_categories where products_id = :products_id');
            $Qcategories->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
            $Qcategories->bindInt(':products_id', $products_id);
            $Qcategories->execute();

            if ($osC_Database->isError()) {
              $error = true;
            } else {
              if (isset($_POST['categories']) && !empty($_POST['categories'])) {
                foreach ($_POST['categories'] as $category_id) {
                  $Qp2c = $osC_Database->query('insert into :table_products_to_categories (products_id, categories_id) values (:products_id, :categories_id)');
                  $Qp2c->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
                  $Qp2c->bindInt(':products_id', $products_id);
                  $Qp2c->bindInt(':categories_id', $category_id);
                  $Qp2c->execute();

                  if ($osC_Database->isError()) {
                    $error = true;
                    break;
                  }
                }
              }
            }
          }

          if ($error === false) {
            if (isset($_POST['products_image']) && !empty($_POST['products_image']) && ($_POST['products_image'] != 'none')) {
              $Qimage =  $osC_Database->query('update :table_products set products_image = :products_image where products_id = :products_id');
              $Qimage->bindTable(':table_products', TABLE_PRODUCTS);
              $Qimage->bindValue(':products_image', $_POST['products_image']);
              $Qimage->bindInt(':products_id', $products_id);
              $Qimage->execute();

              if ($osC_Database->isError()) {
                $error = true;
              }
            }
          }

          if ($error === false) {
            foreach ($osC_Language->getAll() as $language) {
              if (isset($_GET['pID']) && is_numeric($_GET['pID'])) {
                $Qpd = $osC_Database->query('update :table_products_description set products_name = :products_name, products_description = :products_description, products_url = :products_url where products_id = :products_id and language_id = :language_id');
              } else {
                $Qpd = $osC_Database->query('insert into :table_products_description (products_id, language_id, products_name, products_description, products_url) values (:products_id, :language_id, :products_name, :products_description, :products_url)');
              }
              $Qpd->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
              $Qpd->bindInt(':products_id', $products_id);
              $Qpd->bindInt(':language_id', $language['id']);
              $Qpd->bindValue(':products_name', $_POST['products_name'][$language['id']]);
              $Qpd->bindValue(':products_description', $_POST['products_description'][$language['id']]);
              $Qpd->bindValue(':products_url', $_POST['products_url'][$language['id']]);
              $Qpd->execute();

              if ($osC_Database->isError()) {
                $error = true;
                break;
              }
            }
          }

          if ($error === false) {
            $attributes_array = array();

            if (isset($_POST['attribute_prefix']) && !empty($_POST['attribute_prefix']) && isset($_POST['attribute_price']) && !empty($_POST['attribute_price'])) {
              foreach ($_POST['attribute_prefix'] as $groups => $attributes) {
                foreach ($_POST['attribute_prefix'][$groups] as $key => $value) {
                  if (isset($_POST['attribute_price'][$groups][$key]) && !empty($_POST['attribute_price'][$groups][$key])) {
                    $attributes_array[] = $groups . '-' . $key;

                    $Qcheck = $osC_Database->query('select products_attributes_id from :table_products_attributes where products_id = :products_id and options_id = :options_id and options_values_id = :options_values_id');
                    $Qcheck->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
                    $Qcheck->bindInt(':products_id', $products_id);
                    $Qcheck->bindInt(':options_id', $groups);
                    $Qcheck->bindInt(':options_values_id', $key);
                    $Qcheck->execute();

                    if ($Qcheck->numberOfRows()) {
                      $Qattribute = $osC_Database->query('update :table_products_attributes set options_values_price = :options_values_price, price_prefix = :price_prefix where products_id = :products_id and options_id = :options_id and options_values_id = :options_values_id');
                    } else {
                      $Qattribute = $osC_Database->query('insert into :table_products_attributes (products_id, options_id, options_values_id, options_values_price, price_prefix) values (:products_id, :options_id, :options_values_id, :options_values_price, :price_prefix)');
                    }
                    $Qattribute->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
                    $Qattribute->bindInt(':products_id', $products_id);
                    $Qattribute->bindInt(':options_id', $groups);
                    $Qattribute->bindInt(':options_values_id', $key);
                    $Qattribute->bindValue(':options_values_price', $_POST['attribute_price'][$groups][$key]);
                    $Qattribute->bindValue(':price_prefix', $value);
                    $Qattribute->execute();

                    if ($osC_Database->isError()) {
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

            if ($Qcheck->numberOfRows()) {
              while ($Qcheck->next()) {
                $Qdelete = $osC_Database->query('delete from :table_products_attributes where products_attributes_id = :products_attributes_id');
                $Qdelete->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
                $Qdelete->bindInt(':products_attributes_id', $Qcheck->valueInt('products_attributes_id'));
                $Qdelete->execute();

                if ($osC_Database->isError() === false) {
                  $Qdelete = $osC_Database->query('delete from :table_products_attributes_download where products_attributes_id = :products_attributes_id');
                  $Qdelete->bindTable(':table_products_attributes_download', TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD);
                  $Qdelete->bindInt(':products_attributes_id', $Qcheck->valueInt('products_attributes_id'));
                  $Qdelete->execute();

                  if ($osC_Database->isError()) {
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

          tep_redirect(tep_href_link(FILENAME_PRODUCTS, 'page=' . $_GET['page'] . '&cPath=' . $cPath . '&search=' . $_GET['search'] . '&pID=' . $products_id));
        }
        break;
      case 'copy_to_confirm':
        if (isset($_GET['pID']) && isset($_POST['categories_id'])) {
          if ($_POST['copy_as'] == 'link') {
            if (end(explode('_', $_POST['categories_id'])) != $current_category_id) {
              $Qcheck = $osC_Database->query('select count(*) as total from :table_products_to_categories where products_id = :products_id and categories_id = :categories_id');
              $Qcheck->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
              $Qcheck->bindInt(':products_id', $_GET['pID']);
              $Qcheck->bindInt(':categories_id', end(explode('_', $_POST['categories_id'])));
              $Qcheck->execute();

              if ($Qcheck->valueInt('total') < 1) {
                $Qcat = $osC_Database->query('insert into :table_products_to_categories (products_id, categories_id) values (:products_id, :categories_id)');
                $Qcat->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
                $Qcat->bindInt(':products_id', $_GET['pID']);
                $Qcat->bindInt(':categories_id', end(explode('_', $_POST['categories_id'])));
                $Qcat->execute();

                if ($Qcat->affectedRows()) {
                  $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');

                  tep_redirect(tep_href_link(FILENAME_PRODUCTS, 'page=' . $_GET['page'] . '&cPath=' . $_POST['categories_id'] . '&search=' . $_GET['search'] . '&pID=' . $_GET['pID']));
                }
              }
            } else {
              $osC_MessageStack->add_session('header', ERROR_CANNOT_LINK_TO_SAME_CATEGORY, 'error');
            }
          } elseif ($_POST['copy_as'] == 'duplicate') {
            $Qproduct = $osC_Database->query('select products_quantity, products_model, products_image, products_price, products_date_available, products_weight, products_tax_class_id, manufacturers_id from :table_products where products_id = :products_id');
            $Qproduct->bindTable(':table_products', TABLE_PRODUCTS);
            $Qproduct->bindInt(':products_id', $_GET['pID']);
            $Qproduct->execute();

            if ($Qproduct->numberOfRows() === 1) {
              $error = false;

              $osC_Database->startTransaction();

              $Qnew = $osC_Database->query('insert into :table_products (products_quantity, products_model, products_image, products_price, products_date_added, products_date_available, products_weight, products_status, products_tax_class_id, manufacturers_id) values (:products_quantity, :products_model, :products_image, :products_price, now(), :products_date_available, :products_weight, 0, :products_tax_class_id, :manufacturers_id)');
              $Qnew->bindTable(':table_products', TABLE_PRODUCTS);
              $Qnew->bindInt(':products_quantity', $Qproduct->valueInt('products_quantity'));
              $Qnew->bindValue(':products_model', $Qproduct->value('products_model'));
              $Qnew->bindValue(':products_image', $Qproduct->value('products_image'));
              $Qnew->bindValue(':products_price', $Qproduct->value('products_price'));
              $Qnew->bindValue(':products_date_available', $Qproduct->value('products_date_available'));
              $Qnew->bindValue(':products_weight', $Qproduct->value('products_weight'));
              $Qnew->bindInt(':products_tax_class_id', $Qproduct->valueInt('products_tax_class_id'));
              $Qnew->bindInt(':manufacturers_id', $Qproduct->valueInt('manufacturers_id'));
              $Qnew->execute();

              if ($Qnew->affectedRows()) {
                $new_product_id = $osC_Database->nextID();

                $Qdesc = $osC_Database->query('select language_id, products_name, products_description, products_url from :table_products_description where products_id = :products_id');
                $Qdesc->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
                $Qdesc->bindInt(':products_id', $_GET['pID']);
                $Qdesc->execute();

                while ($Qdesc->next()) {
                  $Qnewdesc = $osC_Database->query('insert into :table_products_description (products_id, language_id, products_name, products_description, products_url, products_viewed) values (:products_id, :language_id, :products_name, :products_description, :products_url, 0)');
                  $Qnewdesc->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
                  $Qnewdesc->bindInt(':products_id', $new_product_id);
                  $Qnewdesc->bindInt(':language_id', $Qdesc->valueInt('language_id'));
                  $Qnewdesc->bindValue(':products_name', $Qdesc->value('products_name'));
                  $Qnewdesc->bindValue(':products_description', $Qdesc->value('products_description'));
                  $Qnewdesc->bindValue(':products_url', $Qdesc->value('products_url'));
                  $Qnewdesc->execute();

                  if ($osC_Database->isError()) {
                    $error = true;

                    break;
                  }
                }

                if ($error === false) {
                  $Qp2c = $osC_Database->query('insert into :table_products_to_categories (products_id, categories_id) values (:products_id, :categories_id)');
                  $Qp2c->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
                  $Qp2c->bindInt(':products_id', $new_product_id);
                  $Qp2c->bindInt(':categories_id', end(explode('_', $_POST['categories_id'])));
                  $Qp2c->execute();

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

                tep_redirect(tep_href_link(FILENAME_PRODUCTS, 'page=' . $_GET['page'] . '&cPath=' . $_POST['categories_id'] . '&search=' . $_GET['search'] . '&pID=' . $new_product_id));
              } else {
                $osC_Database->rollbackTransaction();

                $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
              }
            }
          }
        }

        tep_redirect(tep_href_link(FILENAME_PRODUCTS, 'page=' . $_GET['page'] . '&cPath=' . $cPath . '&search=' . $_GET['search'] . '&pID=' . $_GET['pID']));
        break;
    }
  }

  require('../includes/classes/currencies.php');
  $osC_Currencies = new osC_Currencies();

// check if the catalog image directory exists
  if (is_dir(realpath('../images'))) {
    if (!is_writeable(realpath('../images'))) {
      $osC_MessageStack->add('header', ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE, 'error');
    }
  } else {
    $osC_MessageStack->add('header', ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST, 'error');
  }

  switch ($action) {
    case 'new_product': $page_contents = 'products_edit.php'; break;
    case 'new_product_preview': $page_contents = 'products_preview.php'; break;
    default: $page_contents = 'products.php';
  }

  require('templates/default.php');

  require('includes/application_bottom.php');
?>

<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require('includes/classes/tax.php');
  $osC_Tax = new osC_Tax_Admin();

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

  $categories_array = array();
  foreach ($osC_CategoryTree->getTree() as $value) {
    $categories_array[] = array('id' => $value['id'], 'text' => $value['title']);
  }

  require('includes/classes/image.php');
  $osC_Image = new osC_Image_Admin();

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
    $_GET['page'] = 1;
  }

  if (!isset($_GET['search'])) {
    $_GET['search'] = '';
  }

  if (!empty($action)) {
    switch ($action) {
      case 'fileUpload':
        if (isset($_GET['pID'])) {
          $products_image = new upload('products_image');

          if ($products_image->exists()) {
            $products_image->set_destination(realpath('../images/products/originals'));

            if ($products_image->parse() && $products_image->save()) {
              $default_flag = 1;

              $Qcheck = $osC_Database->query('select id from :table_products_images where products_id = :products_id and default_flag = :default_flag limit 1');
              $Qcheck->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
              $Qcheck->bindInt(':products_id', $_GET['pID']);
              $Qcheck->bindInt(':default_flag', 1);
              $Qcheck->execute();

              if ($Qcheck->numberOfRows() === 1) {
                $default_flag = 0;
              }

              $Qimage = $osC_Database->query('insert into :table_products_images (products_id, image, default_flag, sort_order, date_added) values (:products_id, :image, :default_flag, :sort_order, :date_added)');
              $Qimage->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
              $Qimage->bindInt(':products_id', $_GET['pID']);
              $Qimage->bindValue(':image', $products_image->filename);
              $Qimage->bindInt(':default_flag', $default_flag);
              $Qimage->bindInt(':sort_order', 0);
              $Qimage->bindRaw(':date_added', 'now()');
              $Qimage->execute();

              foreach ($osC_Image->getGroups() as $group) {
                if ($group['id'] != '1') {
                  $osC_Image->resize($products_image->filename, $group['id']);
                }
              }
            }
          }
        }

        echo '<script language="javascript" type="text/javascript">window.parent.setFileUploadField(); window.parent.document.getElementById(\'showProgress\').style.display = \'none\'; window.parent.getImages();</script>';

        exit;
        break;
      case 'assignLocalImages':
        if (isset($_GET['pID']) && isset($_POST['localimages'])) {
          $default_flag = 1;

          $Qcheck = $osC_Database->query('select id from :table_products_images where products_id = :products_id and default_flag = :default_flag limit 1');
          $Qcheck->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
          $Qcheck->bindInt(':products_id', $_GET['pID']);
          $Qcheck->bindInt(':default_flag', 1);
          $Qcheck->execute();

          if ($Qcheck->numberOfRows() === 1) {
            $default_flag = 0;
          }

          foreach ($_POST['localimages'] as $image) {
            $image = basename($image);

            if (file_exists('../images/products/_upload/' . $image)) {
              copy('../images/products/_upload/' . $image, '../images/products/originals/' . $image);
              @unlink('../images/products/_upload/' . $image);

              if (isset($_GET['pID'])) {
                $Qimage = $osC_Database->query('insert into :table_products_images (products_id, image, default_flag, sort_order, date_added) values (:products_id, :image, :default_flag, :sort_order, :date_added)');
                $Qimage->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
                $Qimage->bindInt(':products_id', $_GET['pID']);
                $Qimage->bindValue(':image', $image);
                $Qimage->bindInt(':default_flag', $default_flag);
                $Qimage->bindInt(':sort_order', 0);
                $Qimage->bindRaw(':date_added', 'now()');
                $Qimage->execute();

                foreach ($osC_Image->getGroups() as $group) {
                  if ($group['id'] != '1') {
                    $osC_Image->resize($image, $group['id']);
                  }
                }
              }
            }
          }
        }

        echo '<script language="javascript" type="text/javascript">window.parent.getLocalImages(); window.parent.document.getElementById(\'showProgressAssigningLocalImages\').style.display = \'none\'; window.parent.getImages();</script>';

        exit;
        break;
      case 'delete_product_confirm':
        if (isset($_GET['pID'])) {
          include('includes/classes/product.php');

          if (osC_Product_Admin::remove($_GET['pID'], (isset($_POST['product_categories']) && is_array($_POST['product_categories']) && !empty($_POST['product_categories'])) ? $_POST['product_categories'] : '')) {
            $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
          }
        }

        osc_redirect(osc_href_link_admin(FILENAME_PRODUCTS, 'page=' . $_GET['page'] . '&cPath=' . $cPath . '&search=' . $_GET['search']));
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

              osc_redirect(osc_href_link_admin(FILENAME_PRODUCTS, 'page=' . $_GET['page'] . '&cPath=' . $_POST['move_to_category_id'] . '&search=' . $_GET['search'] . '&pID=' . $_GET['pID']));
            } else {
              $osC_MessageStack->add_session('header', WARNING_DB_ROWS_NOT_UPDATED, 'warning');
            }
          }
        }

        osc_redirect(osc_href_link_admin(FILENAME_PRODUCTS, 'page=' . $_GET['page'] . '&cPath=' . $cPath . '&search=' . $_GET['search'] . '&pID=' . $_GET['pID']));
        break;
      case 'save_product':
        $error = false;

        $osC_Database->startTransaction();

        if (isset($_GET['pID']) && is_numeric($_GET['pID'])) {
          $Qproduct = $osC_Database->query('update :table_products set products_quantity = :products_quantity, products_price = :products_price, products_date_available = :products_date_available, products_weight = :products_weight, products_weight_class = :products_weight_class, products_status = :products_status, products_tax_class_id = :products_tax_class_id, manufacturers_id = :manufacturers_id, products_last_modified = now() where products_id = :products_id');
          $Qproduct->bindInt(':products_id', $_GET['pID']);
        } else {
          $Qproduct = $osC_Database->query('insert into :table_products (products_quantity, products_price, products_date_available, products_weight, products_weight_class, products_status, products_tax_class_id, manufacturers_id, products_date_added) values (:products_quantity, :products_price, :products_date_available, :products_weight, :products_weight_class, :products_status, :products_tax_class_id, :manufacturers_id, :products_date_added)');
          $Qproduct->bindRaw(':products_date_added', 'now()');
        }
        $Qproduct->bindTable(':table_products', TABLE_PRODUCTS);
        $Qproduct->bindInt(':products_quantity', $_POST['products_quantity']);
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
          $images = array();

          $products_image = new upload('products_image');

          if ($products_image->exists()) {
            $products_image->set_destination(realpath('../images/products/originals'));

            if ($products_image->parse() && $products_image->save()) {
              $images[] = $products_image->filename;
            }
          }

          if (isset($_POST['localimages'])) {
            foreach ($_POST['localimages'] as $image) {
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
            $Qimage->execute();

            if ($osC_Database->isError()) {
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

        if ($error === false) {
          foreach ($osC_Language->getAll() as $l) {
            if (isset($_GET['pID']) && is_numeric($_GET['pID'])) {
              $Qpd = $osC_Database->query('update :table_products_description set products_name = :products_name, products_description = :products_description, products_model = :products_model, products_keyword = :products_keyword, products_tags = :products_tags, products_url = :products_url where products_id = :products_id and language_id = :language_id');
            } else {
              $Qpd = $osC_Database->query('insert into :table_products_description (products_id, language_id, products_name, products_description, products_model, products_keyword, products_tags, products_url) values (:products_id, :language_id, :products_name, :products_description, :products_model, :products_keyword, :products_tags, :products_url)');
            }
            $Qpd->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
            $Qpd->bindInt(':products_id', $products_id);
            $Qpd->bindInt(':language_id', $l['id']);
            $Qpd->bindValue(':products_name', $_POST['products_name'][$l['id']]);
            $Qpd->bindValue(':products_description', $_POST['products_description'][$l['id']]);
            $Qpd->bindValue(':products_model', $_POST['products_model'][$l['id']]);
            $Qpd->bindValue(':products_keyword', $_POST['products_keyword'][$l['id']]);
            $Qpd->bindValue(':products_tags', $_POST['products_tags'][$l['id']]);
            $Qpd->bindValue(':products_url', $_POST['products_url'][$l['id']]);
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

        osc_redirect(osc_href_link_admin(FILENAME_PRODUCTS, 'page=' . $_GET['page'] . '&cPath=' . $cPath . '&search=' . $_GET['search'] . '&pID=' . $products_id));
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

                  osc_redirect(osc_href_link_admin(FILENAME_PRODUCTS, 'page=' . $_GET['page'] . '&cPath=' . $_POST['categories_id'] . '&search=' . $_GET['search'] . '&pID=' . $_GET['pID']));
                }
              }
            } else {
              $osC_MessageStack->add_session('header', ERROR_CANNOT_LINK_TO_SAME_CATEGORY, 'error');
            }
          } elseif ($_POST['copy_as'] == 'duplicate') {
            $Qproduct = $osC_Database->query('select products_quantity, products_price, products_date_available, products_weight, products_weight_class, products_tax_class_id, manufacturers_id from :table_products where products_id = :products_id');
            $Qproduct->bindTable(':table_products', TABLE_PRODUCTS);
            $Qproduct->bindInt(':products_id', $_GET['pID']);
            $Qproduct->execute();

            if ($Qproduct->numberOfRows() === 1) {
              $error = false;

              $osC_Database->startTransaction();

              $Qnew = $osC_Database->query('insert into :table_products (products_quantity, products_price, products_date_added, products_date_available, products_weight, products_weight_class, products_status, products_tax_class_id, manufacturers_id) values (:products_quantity, :products_price, now(), :products_date_available, :products_weight, :products_weight_class, 0, :products_tax_class_id, :manufacturers_id)');
              $Qnew->bindTable(':table_products', TABLE_PRODUCTS);
              $Qnew->bindInt(':products_quantity', $Qproduct->valueInt('products_quantity'));
              $Qnew->bindValue(':products_price', $Qproduct->value('products_price'));

              if (!osc_empty($Qproduct->value('products_date_available'))) {
                $Qnew->bindValue(':products_date_available', $Qproduct->value('products_date_available'));
              } else {
                $Qnew->bindRaw(':products_date_available', 'null');
              }

              $Qnew->bindValue(':products_weight', $Qproduct->value('products_weight'));
              $Qnew->bindInt(':products_weight_class', $Qproduct->valueInt('products_weight_class'));
              $Qnew->bindInt(':products_tax_class_id', $Qproduct->valueInt('products_tax_class_id'));
              $Qnew->bindInt(':manufacturers_id', $Qproduct->valueInt('manufacturers_id'));
              $Qnew->execute();

              if ($Qnew->affectedRows()) {
                $new_product_id = $osC_Database->nextID();

                $Qdesc = $osC_Database->query('select language_id, products_name, products_description, products_model, products_tags, products_url from :table_products_description where products_id = :products_id');
                $Qdesc->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
                $Qdesc->bindInt(':products_id', $_GET['pID']);
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

                osc_redirect(osc_href_link_admin(FILENAME_PRODUCTS, 'page=' . $_GET['page'] . '&cPath=' . $_POST['categories_id'] . '&search=' . $_GET['search'] . '&pID=' . $new_product_id));
              } else {
                $osC_Database->rollbackTransaction();

                $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
              }
            }
          }
        }

        osc_redirect(osc_href_link_admin(FILENAME_PRODUCTS, 'page=' . $_GET['page'] . '&cPath=' . $cPath . '&search=' . $_GET['search'] . '&pID=' . $_GET['pID']));
        break;
    }
  }

  require('../includes/classes/currencies.php');
  $osC_Currencies = new osC_Currencies();

// check if the catalog image directory exists
  if (is_dir(realpath('../images/products'))) {
    if (!is_writeable(realpath('../images/products'))) {
      $osC_MessageStack->add('header', ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE, 'error');
    }
  } else {
    $osC_MessageStack->add('header', ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST, 'error');
  }

  switch ($action) {
    case 'new_product': $page_contents = 'products_edit.php'; break;
    case 'preview': $page_contents = 'products_preview.php'; break;
    default: $page_contents = 'products.php';
  }

  require('templates/default.php');

  require('includes/application_bottom.php');
?>

<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/products.php');

  class osC_Content_Products extends osC_Template {

/* Private variables */

    var $_module = 'products',
        $_page_title,
        $_page_contents = 'main.php';

/* Class constructor */

    function osC_Content_Products() {
      global $osC_Language, $osC_MessageStack, $osC_Currencies, $osC_Tax, $osC_CategoryTree, $osC_Image, $current_category_id;

      $this->_page_title = $osC_Language->get('heading_title');

      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
        $_GET['page'] = 1;
      }

      if (!isset($_GET['cPath'])) {
        $_GET['cPath'] = '';
      }

      if (!isset($_GET['search'])) {
        $_GET['search'] = '';
      }

      if (!empty($_GET['cPath'])) {
        $cPath_array = osc_parse_category_path($_GET['cPath']);
        $_GET['cPath'] = implode('_', $cPath_array);

        $current_category_id = end($cPath_array);
      } else {
        $current_category_id = 0;
      }

      require('../includes/classes/currencies.php');
      $osC_Currencies = new osC_Currencies();

      require('includes/classes/tax.php');
      $osC_Tax = new osC_Tax_Admin();

      require('includes/classes/category_tree.php');
      $osC_CategoryTree = new osC_CategoryTree_Admin();
      $osC_CategoryTree->setSpacerString('&nbsp;', 2);

      require('includes/classes/image.php');
      $osC_Image = new osC_Image_Admin();

// check if the catalog image directory exists
      if (is_dir(realpath('../images/products'))) {
        if (!is_writeable(realpath('../images/products'))) {
          $osC_MessageStack->add('header', sprintf($osC_Language->get('ms_error_image_directory_not_writable'), realpath('../images/products')), 'error');
        }
      } else {
        $osC_MessageStack->add('header', sprintf($osC_Language->get('ms_error_image_directory_non_existant'), realpath('../images/products')), 'error');
      }

      if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
          case 'preview':
            $this->_page_contents = 'preview.php';
            break;

          case 'save':
            $this->_page_contents = 'edit.php';

            if ( ( osc_empty(CFG_APP_IMAGEMAGICK_CONVERT) || !file_exists(CFG_APP_IMAGEMAGICK_CONVERT) ) && !$osC_Image->hasGDSupport() ) {
              $osC_MessageStack->add('header', $osC_Language->get('ms_warning_image_processor_not_available'), 'warning');
            }

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $error = false;

              $data = array('quantity' => $_POST['products_quantity'],
                            'price' => $_POST['products_price'],
                            'date_available' => $_POST['products_date_available'],
                            'weight' => $_POST['products_weight'],
                            'weight_class' => $_POST['products_weight_class'],
                            'status' => $_POST['products_status'],
                            'tax_class_id' => $_POST['products_tax_class_id'],
                            'manufacturers_id' => $_POST['manufacturers_id'],
                            'date_available' => $_POST['products_date_available'],
                            'products_name' => $_POST['products_name'],
                            'products_description' => $_POST['products_description'],
                            'products_model' => $_POST['products_model'],
                            'products_keyword' => $_POST['products_keyword'],
                            'products_tags' => $_POST['products_tags'],
                            'products_url' => $_POST['products_url']);

              if ( isset($_POST['categories']) ) {
                $data['categories'] = $_POST['categories'];
              }

              if ( isset($_POST['localimages']) ) {
                $data['localimages'] = $_POST['localimages'];
              }

              if ( isset($_POST['attribute_prefix']) ) {
                $data['attribute_prefix'] = $_POST['attribute_prefix'];
              }

              if ( isset($_POST['attribute_price']) ) {
                $data['attribute_price'] = $_POST['attribute_price'];
              }

              foreach ( $data['products_keyword'] as $value ) {
                if ( empty($value) ) {
                  $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_product_keyword_empty'), 'error');

                  $error = true;
                } elseif ( preg_match('/^[a-z0-9_-]+$/iD', $value) !== 1 ) {
                  $osC_MessageStack->add($this->_module, sprintf($osC_Language->get('ms_error_product_keyword_invalid'), osc_output_string_protected($value)), 'error');

                  $error = true;
                }

                if ( osC_Products_Admin::getKeywordCount($value, (isset($_GET['pID']) && is_numeric($_GET['pID']) ? $_GET['pID'] : null)) > 0 ) {
                  $osC_MessageStack->add($this->_module, sprintf($osC_Language->get('ms_error_product_keyword_exists'), osc_output_string_protected($value)), 'error');

                  $error = true;
                }
              }

              if ( $error === false ) {
                if ( osC_Products_Admin::save((isset($_GET['pID']) && is_numeric($_GET['pID']) ? $_GET['pID'] : null), $data) ) {
                  $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
                } else {
                  $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
                }

                osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search']));
              }
            }

            break;

          case 'fileUpload':
            $this->_processUploadedFile();
            break;

          case 'assignLocalImages':
            $this->_assignLocalImages();
            break;

          case 'copy':
            $this->_page_contents = 'copy.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_Products_Admin::copy($_GET['pID'], $_POST['new_category_id'], $_POST['copy_as']) ) {
                $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search']));
            }

            break;

          case 'delete':
            $this->_page_contents = 'delete.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              if ( osC_Products_Admin::delete($_GET['pID'], (isset($_POST['product_categories']) && is_array($_POST['product_categories'])) ? $_POST['product_categories'] : null) ) {
                $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search']));
            }

            break;

          case 'batchDelete':
            if ( isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch']) ) {
              $this->_page_contents = 'batch_delete.php';

              if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
                $error = false;

                foreach ($_POST['batch'] as $id) {
                  if ( !osC_Products_Admin::delete($id) ) {
                    $error = true;
                    break;
                  }
                }

                if ( $error === false ) {
                  $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
                } else {
                  $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
                }

                osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search']));
              }
            }

            break;

          case 'batchCopy':
            if ( isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch']) ) {
              $this->_page_contents = 'batch_copy.php';

              if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
                $error = false;

                foreach ($_POST['batch'] as $id) {
                  if ( !osC_Products_Admin::copy($id, $_POST['new_category_id'], $_POST['copy_as']) ) {
                    $error = true;
                    break;
                  }
                }

                if ( $error === false ) {
                  $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
                } else {
                  $osC_MessageStack->add_session($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
                }

                osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page'] . '&cPath=' . $_GET['cPath'] . '&search=' . $_GET['search']));
              }
            }

            break;
        }
      }
    }

/* Private methods */

    function _processUploadedFile() {
      global $osC_Database, $osC_Image;

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
            $Qimage->setLogging($_SESSION['module'], $_GET['pID']);
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
    }

    function _assignLocalImages() {
      global $osC_Database, $osC_Image;

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
              $Qimage->setLogging($_SESSION['module'], $_GET['pID']);
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
    }
  }
?>

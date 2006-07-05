<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
    $_GET['page'] = 1;
  }

  if (!empty($action)) {
    switch ($action) {
      case 'save':
        $error = false;

        $osC_Database->startTransaction();

        if (isset($_GET['mID']) && is_numeric($_GET['mID'])) {
          $Qmanufacturer = $osC_Database->query('update :table_manufacturers set manufacturers_name = :manufacturers_name, last_modified = now() where manufacturers_id = :manufacturers_id');
          $Qmanufacturer->bindInt(':manufacturers_id', $_GET['mID']);
        } else {
          $Qmanufacturer = $osC_Database->query('insert into :table_manufacturers (manufacturers_name, date_added) values (:manufacturers_name, now())');
        }
        $Qmanufacturer->bindTable(':table_manufacturers', TABLE_MANUFACTURERS);
        $Qmanufacturer->bindValue(':manufacturers_name', $_POST['manufacturers_name']);
        $Qmanufacturer->execute();

        if ($osC_Database->isError() === false) {
          if (isset($_GET['mID']) && is_numeric($_GET['mID'])) {
            $manufacturers_id = $_GET['mID'];
          } else {
            $manufacturers_id = $osC_Database->nextID();
          }

          if ($manufacturers_image = new upload('manufacturers_image', realpath('../images'))) {
            $Qimage = $osC_Database->query('update :table_manufacturers set manufacturers_image = :manufacturers_image where manufacturers_id = :manufacturers_id');
            $Qimage->bindTable(':table_manufacturers', TABLE_MANUFACTURERS);
            $Qimage->bindValue(':manufacturers_image', $manufacturers_image->filename);
            $Qimage->bindInt(':manufacturers_id', $manufacturers_id);
            $Qimage->execute();

            if ($osC_Database->isError()) {
              $error = true;
            }
          }
        }

        if ($error === false) {
          foreach ($osC_Language->getAll() as $l) {
            if (isset($_GET['mID']) && is_numeric($_GET['mID'])) {
              $Qurl = $osC_Database->query('update :table_manufacturers_info set manufacturers_url = :manufacturers_url where manufacturers_id = :manufacturers_id and languages_id = :languages_id');
            } else {
              $Qurl = $osC_Database->query('insert into :table_manufacturers_info (manufacturers_id, languages_id, manufacturers_url) values (:manufacturers_id, :languages_id, :manufacturers_url)');
            }
            $Qurl->bindTable(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
            $Qurl->bindInt(':manufacturers_id', $manufacturers_id);
            $Qurl->bindInt(':languages_id', $l['id']);
            $Qurl->bindValue(':manufacturers_url', $_POST['manufacturers_url'][$l['id']]);
            $Qurl->execute();

            if ($osC_Database->isError()) {
              $error = true;
              break;
            }
          }
        }

        if ($error === false) {
          $osC_Database->commitTransaction();

          osC_Cache::clear('manufacturers');

          $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_Database->rollbackTransaction();

          $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        tep_redirect(tep_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $manufacturers_id));
        break;
      case 'deleteconfirm':
        if (isset($_GET['mID']) && is_numeric($_GET['mID'])) {
          include('includes/classes/product.php');

          if (isset($_POST['delete_image']) && ($_POST['delete_image'] == 'on')) {
            $Qimage = $osC_Database->query('select manufacturers_image from :table_manufacturers where manufacturers_id = :manufacturers_id');
            $Qimage->bindTable(':table_manufacturers', TABLE_MANUFACTURERS);
            $Qimage->bindInt(':manufacturers_id', $_GET['mID']);
            $Qimage->execute();

            if ($Qimage->numberOfRows() && tep_not_null($Qimage->value('manufacturers_image'))) {
              if (file_exists(realpath('../images/' . $Qimage->value('manufacturers_image')))) {
                @unlink(realpath('../images/' . $Qimage->value('manufacturers_image')));
              }
            }
          }

          $Qm = $osC_Database->query('delete from :table_manufacturers where manufacturers_id = :manufacturers_id');
          $Qm->bindTable(':table_manufacturers', TABLE_MANUFACTURERS);
          $Qm->bindInt(':manufacturers_id', $_GET['mID']);
          $Qm->execute();

          $Qmi = $osC_Database->query('delete from :table_manufacturers_info where manufacturers_id = :manufacturers_id');
          $Qmi->bindTable(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
          $Qmi->bindInt(':manufacturers_id', $_GET['mID']);
          $Qmi->execute();

          if (isset($_POST['delete_products']) && ($_POST['delete_products'] == 'on')) {
            $Qproducts = $osC_Database->query('select products_id from :table_products where manufacturers_id = :manufacturers_id');
            $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
            $Qproducts->bindInt(':manufacturers_id', $_GET['mID']);
            $Qproducts->execute();

            while ($Qproducts->next()) {
              osC_Product_Admin::remove($Qproducts->valueInt('products_id'));
            }
          } else {
            $Qupdate = $osC_Database->query('update :table_products set manufacturers_id = "" where manufacturers_id = :manufacturers_id');
            $Qupdate->bindTable(':table_products', TABLE_PRODUCTS);
            $Qupdate->bindInt(':manufacturers_id', $_GET['mID']);
            $Qupdate->execute();
          }

          osC_Cache::clear('manufacturers');

          $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
        }

        tep_redirect(tep_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page']));
        break;
    }
  }

  $page_contents = 'manufacturers.php';

  require('templates/default.php');

  require('includes/application_bottom.php');
?>

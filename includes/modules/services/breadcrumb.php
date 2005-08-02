<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  class osC_Services_breadcrumb {
    var $title = 'Breadcrumb',
        $description = 'Breadcrumb builder for easy navigation.',
        $uninstallable = true,
        $depends,
        $preceeds;

    function start() {
      if (PHP_VERSION < 4.1) {
        global $_GET;
      }

      global $breadcrumb, $osC_Session, $osC_Database, $cPath, $cPath_array;

      include('includes/classes/breadcrumb.php');
      $breadcrumb = new breadcrumb;

      $breadcrumb->add(HEADER_TITLE_TOP, HTTP_SERVER);
      $breadcrumb->add(HEADER_TITLE_CATALOG, tep_href_link(FILENAME_DEFAULT));

// add category names or the manufacturer name to the breadcrumb trail
      if (isset($cPath_array) && (sizeof($cPath_array) > 0)) {
        $Qcategories = $osC_Database->query('select categories_id, categories_name from :table_categories_description where categories_id in (:categories_id) and language_id = :language_id');
        $Qcategories->bindRaw(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
        $Qcategories->bindRaw(':categories_id', implode(',', $cPath_array));
        $Qcategories->bindInt(':language_id', $osC_Session->value('languages_id'));
        $Qcategories->execute();

        $categories = array();
        while ($Qcategories->next()) {
          $categories[$Qcategories->value('categories_id')] = $Qcategories->valueProtected('categories_name');
        }

        $Qcategories->freeResult();

        for ($i=0, $n=sizeof($cPath_array); $i<$n; $i++) {
          $breadcrumb->add($categories[$cPath_array[$i]], tep_href_link(FILENAME_DEFAULT, 'cPath=' . implode('_', array_slice($cPath_array, 0, ($i+1)))));
        }
      } elseif (isset($_GET['manufacturers_id']) && is_numeric($_GET['manufacturers_id'])) {
        $Qmanufacturers = $osC_Database->query('select manufacturers_name from :table_manufacturers where manufacturers_id = :manufacturers_id');
        $Qmanufacturers->bindRaw(':table_manufacturers', TABLE_MANUFACTURERS);
        $Qmanufacturers->bindInt(':manufacturers_id', $_GET['manufacturers_id']);
        $Qmanufacturers->execute();

        if ($Qmanufacturers->numberOfRows()) {
          $breadcrumb->add($Qmanufacturers->valueProtected('manufacturers_name'), tep_href_link(FILENAME_DEFAULT, 'manufacturers_id=' . $_GET['manufacturers_id']));
        }

        $Qmanufacturers->freeResult();
      }

// add the products model to the breadcrumb trail
      if (isset($_GET['products_id']) && is_numeric($_GET['products_id'])) {
        $Qmodel = $osC_Database->query('select products_model from :table_products where products_id = :products_id');
        $Qmodel->bindRaw(':table_products', TABLE_PRODUCTS);
        $Qmodel->bindInt(':products_id', $_GET['products_id']);
        $Qmodel->execute();

        if ($Qmodel->numberOfRows() > 0) {
          $breadcrumb->add($Qmodel->valueProtected('products_model'), tep_href_link(FILENAME_PRODUCT_INFO, 'cPath=' . $cPath . '&products_id=' . $_GET['products_id']));
        }

        $Qmodel->freeResult();
      }

      return true;
    }

    function stop() {
      return true;
    }

    function install() {
      return false;
    }

    function remove() {
      return false;
    }

    function keys() {
      return false;
    }
  }
?>

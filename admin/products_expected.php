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
        if (isset($_GET['pID']) && is_numeric($_GET['pID'])) {
          $Qproduct = $osC_Database->query('update :table_products set products_date_available = :products_date_available, products_last_modified = now() where products_id = :products_id');
          $Qproduct->bindTable(':table_products', TABLE_PRODUCTS);
          if (date('Y-m-d') < $_POST['products_date_available']) {
            $Qproduct->bindValue(':products_date_available', $_POST['products_date_available']);
          } else {
            $Qproduct->bindRaw(':products_date_available', 'null');
          }
          $Qproduct->bindInt(':products_id', $_GET['pID']);
          $Qproduct->execute();

          if ($osC_Database->isError() === false) {
            $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
          } else {
            $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
          }
        }

        tep_redirect(osc_href_link_admin(FILENAME_PRODUCTS_EXPECTED, 'page=' . $_GET['page']));
        break;
    }
  }

  $Qcheck = $osC_Database->query('select products_id from :table_products where products_date_available is not null limit 1');
  $Qcheck->bindTable(':table_products', TABLE_PRODUCTS);
  $Qcheck->execute();

  if ($Qcheck->numberOfRows()) {
    $Qupdate = $osC_Database->query('update :table_products set products_date_available = null where unix_timestamp(now()) > unix_timestamp(products_date_available)');
    $Qupdate->bindTable(':table_products', TABLE_PRODUCTS);
    $Qupdate->execute();
  }

  $page_contents = 'products_expected.php';

  require('templates/default.php');

  require('includes/application_bottom.php');
?>

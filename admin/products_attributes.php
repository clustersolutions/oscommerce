<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

/*
      case 'add_product_attributes':
        $products_id = tep_db_prepare_input($HTTP_POST_VARS['products_id']);
        $options_id = tep_db_prepare_input($HTTP_POST_VARS['options_id']);
        $values_id = tep_db_prepare_input($HTTP_POST_VARS['values_id']);
        $value_price = tep_db_prepare_input($HTTP_POST_VARS['value_price']);
        $price_prefix = tep_db_prepare_input($HTTP_POST_VARS['price_prefix']);

        tep_db_query("insert into " . TABLE_PRODUCTS_ATTRIBUTES . " values ('', '" . (int)$products_id . "', '" . (int)$options_id . "', '" . (int)$values_id . "', '" . tep_db_input($value_price) . "', '" . tep_db_input($price_prefix) . "')");

        if (DOWNLOAD_ENABLED == '1') {
          $products_attributes_id = tep_db_insert_id();

          $products_attributes_filename = tep_db_prepare_input($HTTP_POST_VARS['products_attributes_filename']);
          $products_attributes_maxdays = tep_db_prepare_input($HTTP_POST_VARS['products_attributes_maxdays']);
          $products_attributes_maxcount = tep_db_prepare_input($HTTP_POST_VARS['products_attributes_maxcount']);

          if (!empty($products_attributes_filename)) {
            tep_db_query("insert into " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " values (" . (int)$products_attributes_id . ", '" . tep_db_input($products_attributes_filename) . "', '" . tep_db_input($products_attributes_maxdays) . "', '" . tep_db_input($products_attributes_maxcount) . "')");
          }
        }

        osc_redirect(osc_href_link_admin(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
        break;
*/
  require('includes/application_top.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
    $_GET['page'] = 1;
  }

  $entriesAction = (isset($_GET['entriesAction']) ? $_GET['entriesAction'] : '');

  if (!isset($_GET['entriesPage']) || (isset($_GET['entriesPage']) && !is_numeric($_GET['entriesPage']))) {
    $_GET['entriesPage'] = 1;
  }

  if (!empty($action)) {
    switch ($action) {
      case 'saveGroup':
        $error = false;

        if (isset($_GET['paID']) && is_numeric($_GET['paID'])) {
          $group_id = $_GET['paID'];
        } else {
          $Qcheck = $osC_Database->query('select max(products_options_id) as products_options_id from :table_products_options');
          $Qcheck->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
          $Qcheck->execute();

          $group_id = $Qcheck->valueInt('products_options_id') + 1;
        }

        $osC_Database->startTransaction();

        foreach ($osC_Language->getAll() as $l) {
          if (isset($_GET['paID']) && is_numeric($_GET['paID'])) {
            $Qgroup = $osC_Database->query('update :table_products_options set products_options_name = :products_options_name where products_options_id = :products_options_id and language_id = :language_id');
          } else {
            $Qgroup = $osC_Database->query('insert into :table_products_options (products_options_id, language_id, products_options_name) values (:products_options_id, :language_id, :products_options_name)');
          }
          $Qgroup->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
          $Qgroup->bindInt(':products_options_id', $group_id);
          $Qgroup->bindValue(':products_options_name', $_POST['group_name'][$l['id']]);
          $Qgroup->bindInt(':language_id', $l['id']);
          $Qgroup->execute();

          if ($osC_Database->isError()) {
            $error = true;
            break;
          }
        }

        if ($error === false) {
          $osC_Database->commitTransaction();

          $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_Database->rollbackTransaction();

          $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        osc_redirect(osc_href_link_admin(FILENAME_PRODUCTS_ATTRIBUTES, 'page=' . $_GET['page'] . '&paID=' . $group_id));
        break;
      case 'deleteConfirm':
        if (isset($_GET['paID']) && is_numeric($_GET['paID'])) {
          $error = false;

          $osC_Database->startTransaction();

          $Qentries = $osC_Database->query('select products_options_values_id from :table_products_options_values_to_products_options where products_options_id = :products_options_id');
          $Qentries->bindTable(':table_products_options_values_to_products_options', TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS);
          $Qentries->bindInt(':products_options_id', $_GET['paID']);
          $Qentries->execute();

          while ($Qentries->next()) {
            $Qdelete = $osC_Database->query('delete from :table_products_options_values where products_options_values_id = :products_options_values_id');
            $Qdelete->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
            $Qdelete->bindInt(':products_options_values_id', $Qentries->valueInt('products_options_values_id'));
            $Qdelete->execute();

            if ($osC_Database->isError()) {
              $error = true;
              break;
            }
          }

          if ($error === false) {
            $Qdelete = $osC_Database->query('delete from :table_products_options_values_to_products_options where products_options_id = :products_options_id');
            $Qdelete->bindTable(':table_products_options_values_to_products_options', TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS);
            $Qdelete->bindInt(':products_options_id', $_GET['paID']);
            $Qdelete->execute();

            if ($osC_Database->isError()) {
              $error = true;
              break;
            }
          }

          if ($error === false) {
            $Qdelete = $osC_Database->query('delete from :table_products_options where products_options_id = :products_options_id');
            $Qdelete->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
            $Qdelete->bindInt(':products_options_id', $_GET['paID']);
            $Qdelete->execute();

            if ($osC_Database->isError()) {
              $error = true;
              break;
            }
          }

          if ($error === false) {
            $osC_Database->commitTransaction();

            $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
          } else {
            $osC_Database->rollbackTransaction();

            $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
          }
        }

        osc_redirect(osc_href_link_admin(FILENAME_PRODUCTS_ATTRIBUTES, 'page=' . $_GET['page']));
        break;
    }
  }

  if (!empty($entriesAction)) {
    switch ($entriesAction) {
      case 'saveGroupEntry':
        $error = false;

        if (isset($_GET['paeID']) && is_numeric($_GET['paeID'])) {
          $entry_id = $_GET['paeID'];
        } else {
          $Qcheck = $osC_Database->query('select max(products_options_values_id) as products_options_values_id from :table_products_options_values');
          $Qcheck->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
          $Qcheck->execute();

          $entry_id = $Qcheck->valueInt('products_options_values_id') + 1;
        }

        $osC_Database->startTransaction();

        foreach ($osC_Language->getAll() as $l) {
          if (isset($_GET['paeID']) && is_numeric($_GET['paeID'])) {
            $Qentry = $osC_Database->query('update :table_products_options_values set products_options_values_name = :products_options_values_name where products_options_values_id = :products_options_values_id and language_id = :language_id');
          } else {
            $Qentry = $osC_Database->query('insert into :table_products_options_values (products_options_values_id, language_id, products_options_values_name) values (:products_options_values_id, :language_id, :products_options_values_name)');
          }
          $Qentry->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
          $Qentry->bindInt(':products_options_values_id', $entry_id);
          $Qentry->bindValue(':products_options_values_name', $_POST['entry_name'][$l['id']]);
          $Qentry->bindInt(':language_id', $l['id']);
          $Qentry->execute();

          if ($osC_Database->isError()) {
            $error = true;
            break;
          }
        }

        if ($error === false) {
          if (!isset($_GET['paeID'])) {
            $Qlink = $osC_Database->query('insert into :table_products_options_values_to_products_options (products_options_id, products_options_values_id) values (:products_options_id, :products_options_values_id)');
            $Qlink->bindTable(':table_products_options_values_to_products_options', TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS);
            $Qlink->bindInt(':products_options_id', $_GET['paID']);
            $Qlink->bindInt(':products_options_values_id', $entry_id);
            $Qlink->execute();

            if ($osC_Database->isError()) {
              $error = true;
            }
          }
        }

        if ($error === false) {
          $osC_Database->commitTransaction();

          $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_Database->rollbackTransaction();

          $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        osc_redirect(osc_href_link_admin(FILENAME_PRODUCTS_ATTRIBUTES, 'page=' . $_GET['page'] . '&paID=' . $_GET['paID'] . '&action=list&entriesPage=' . $_GET['entriesPage'] . '&paeID=' . $entry_id));
        break;
      case 'deleteConfirm':
        if (isset($_GET['paeID']) && is_numeric($_GET['paeID'])) {
          $error = false;

          $osC_Database->startTransaction();

          $Qentry = $osC_Database->query('delete from :table_products_options_values where products_options_values_id = :products_options_values_id');
          $Qentry->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
          $Qentry->bindInt(':products_options_values_id', $_GET['paeID']);
          $Qentry->execute();

          if ($osC_Database->isError()) {
            $error = true;
          }

          if ($error === false) {
            $Qlink = $osC_Database->query('delete from :table_products_options_values_to_products_options where products_options_id = :products_options_id and products_options_values_id = :products_options_values_id');
            $Qlink->bindTable(':table_products_options_values_to_products_options', TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS);
            $Qlink->bindInt(':products_options_id', $_GET['paID']);
            $Qlink->bindInt(':products_options_values_id', $_GET['paeID']);
            $Qlink->execute();

            if ($osC_Database->isError()) {
              $error = true;
            }
          }

          if ($error === false) {
            $osC_Database->commitTransaction();

            $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
          } else {
            $osC_Database->rollbackTransaction();

            $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
          }
        }

        osc_redirect(osc_href_link_admin(FILENAME_PRODUCTS_ATTRIBUTES, 'page=' . $_GET['page'] . '&paID=' . $_GET['paID'] . '&action=list&entriesPage=' . $_GET['entriesPage']));
        break;
    }
  }

  switch ($action) {
    case 'list': $page_contents = 'products_attributes_listing.php'; break;
    default: $page_contents = 'products_attributes.php';
  }

  require('templates/default.php');

  require('includes/application_bottom.php');
?>

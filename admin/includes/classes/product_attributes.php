<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_ProductAttributes_Admin {
    function getData($id, $language_id = null, $key = null) {
      global $osC_Database, $osC_Language;

      if ( empty($language_id) ) {
        $language_id = $osC_Language->getID();
      }

      $Qgroup = $osC_Database->query('select * from :table_products_options where products_options_id = :products_options_id and language_id = :language_id');
      $Qgroup->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
      $Qgroup->bindInt(':products_options_id', $id);
      $Qgroup->bindInt(':language_id', $language_id);
      $Qgroup->execute();

      $data = $Qgroup->toArray();

      $Qentries = $osC_Database->query('select count(*) as total_entries from :table_products_options_values_to_products_options where products_options_id = :products_options_id');
      $Qentries->bindTable(':table_products_options_values_to_products_options', TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS);
      $Qentries->bindInt(':products_options_id', $id);
      $Qentries->execute();

      $data['total_entries'] = $Qentries->valueInt('total_entries');

      $Qproducts = $osC_Database->query('select count(*) as total_products from :table_products_attributes where options_id = :options_id');
      $Qproducts->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
      $Qproducts->bindInt(':options_id', $id);
      $Qproducts->execute();

      $data['total_products'] = $Qproducts->valueInt('total_products');

      $Qproducts->freeResult();
      $Qentries->freeResult();
      $Qgroup->freeResult();

      if ( empty($key) ) {
        return $data;
      } else {
        return $data[$key];
      }
    }

    function save($id = null, $data) {
      global $osC_Database, $osC_Language;

      $error = false;

      if ( is_numeric($id) ) {
        $group_id = $id;
      } else {
        $Qcheck = $osC_Database->query('select max(products_options_id) as products_options_id from :table_products_options');
        $Qcheck->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
        $Qcheck->execute();

        $group_id = $Qcheck->valueInt('products_options_id') + 1;
      }

      $osC_Database->startTransaction();

      foreach ( $osC_Language->getAll() as $l ) {
        if ( is_numeric($id) ) {
          $Qgroup = $osC_Database->query('update :table_products_options set products_options_name = :products_options_name where products_options_id = :products_options_id and language_id = :language_id');
        } else {
          $Qgroup = $osC_Database->query('insert into :table_products_options (products_options_id, language_id, products_options_name) values (:products_options_id, :language_id, :products_options_name)');
        }

        $Qgroup->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
        $Qgroup->bindInt(':products_options_id', $group_id);
        $Qgroup->bindValue(':products_options_name', $data['name'][$l['id']]);
        $Qgroup->bindInt(':language_id', $l['id']);
        $Qgroup->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
          break;
        }
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    function delete($id) {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();

      $Qentries = $osC_Database->query('select products_options_values_id from :table_products_options_values_to_products_options where products_options_id = :products_options_id');
      $Qentries->bindTable(':table_products_options_values_to_products_options', TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS);
      $Qentries->bindInt(':products_options_id', $id);
      $Qentries->execute();

      while ( $Qentries->next() ) {
        $Qdelete = $osC_Database->query('delete from :table_products_options_values where products_options_values_id = :products_options_values_id');
        $Qdelete->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
        $Qdelete->bindInt(':products_options_values_id', $Qentries->valueInt('products_options_values_id'));
        $Qdelete->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
          break;
        }
      }

      if ( $error === false ) {
        $Qdelete = $osC_Database->query('delete from :table_products_options_values_to_products_options where products_options_id = :products_options_id');
        $Qdelete->bindTable(':table_products_options_values_to_products_options', TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS);
        $Qdelete->bindInt(':products_options_id', $id);
        $Qdelete->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
          break;
        }
      }

      if ( $error === false ) {
        $Qdelete = $osC_Database->query('delete from :table_products_options where products_options_id = :products_options_id');
        $Qdelete->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
        $Qdelete->bindInt(':products_options_id', $id);
        $Qdelete->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
          break;
        }
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    function getEntryData($id, $language_id = null) {
      global $osC_Database, $osC_Language;

      if ( empty($language_id) ) {
        $language_id = $osC_Language->getID();
      }

      $Qentry = $osC_Database->query('select * from :table_products_options_values where products_options_values_id = :products_options_values_id and language_id = :language_id');
      $Qentry->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
      $Qentry->bindInt(':products_options_values_id', $id);
      $Qentry->bindInt(':language_id', $language_id);
      $Qentry->execute();

      $data = $Qentry->toArray();

      $Qproducts = $osC_Database->query('select count(*) as total_products from :table_products_attributes where options_values_id = :options_values_id');
      $Qproducts->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
      $Qproducts->bindInt(':options_values_id', $Qentry->valueInt('products_options_values_id'));
      $Qproducts->execute();

      $data['total_products'] = $Qproducts->valueInt('total_products');

      $Qproducts->freeResult();
      $Qentry->freeResult();

      return $data;
    }

    function saveEntry($id = null, $data) {
      global $osC_Database, $osC_Language;

      $error = false;

      if ( is_numeric($id) ) {
        $entry_id = $id;
      } else {
        $Qcheck = $osC_Database->query('select max(products_options_values_id) as products_options_values_id from :table_products_options_values');
        $Qcheck->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
        $Qcheck->execute();

        $entry_id = $Qcheck->valueInt('products_options_values_id') + 1;
      }

      $osC_Database->startTransaction();

      foreach ( $osC_Language->getAll() as $l ) {
        if ( is_numeric($id) ) {
          $Qentry = $osC_Database->query('update :table_products_options_values set products_options_values_name = :products_options_values_name where products_options_values_id = :products_options_values_id and language_id = :language_id');
        } else {
          $Qentry = $osC_Database->query('insert into :table_products_options_values (products_options_values_id, language_id, products_options_values_name) values (:products_options_values_id, :language_id, :products_options_values_name)');
        }

        $Qentry->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
        $Qentry->bindInt(':products_options_values_id', $entry_id);
        $Qentry->bindValue(':products_options_values_name', $data['name'][$l['id']]);
        $Qentry->bindInt(':language_id', $l['id']);
        $Qentry->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
          break;
        }
      }

      if ( $error === false ) {
        if ( !is_numeric($id) ) {
          $Qlink = $osC_Database->query('insert into :table_products_options_values_to_products_options (products_options_id, products_options_values_id) values (:products_options_id, :products_options_values_id)');
          $Qlink->bindTable(':table_products_options_values_to_products_options', TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS);
          $Qlink->bindInt(':products_options_id', $data['products_options_id']);
          $Qlink->bindInt(':products_options_values_id', $entry_id);
          $Qlink->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    function deleteEntry($id, $group_id) {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();

      $Qentry = $osC_Database->query('delete from :table_products_options_values where products_options_values_id = :products_options_values_id');
      $Qentry->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
      $Qentry->bindInt(':products_options_values_id', $id);
      $Qentry->execute();

      if ( $osC_Database->isError() ) {
        $error = true;
      }

      if ( $error === false ) {
        $Qlink = $osC_Database->query('delete from :table_products_options_values_to_products_options where products_options_id = :products_options_id and products_options_values_id = :products_options_values_id');
        $Qlink->bindTable(':table_products_options_values_to_products_options', TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS);
        $Qlink->bindInt(':products_options_id', $group_id);
        $Qlink->bindInt(':products_options_values_id', $id);
        $Qlink->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
        }
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }
  }
?>

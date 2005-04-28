<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('../includes/classes/language.php');

  class osC_Language_Admin extends osC_Language {

/* Class constructor */

    function osC_Language_Admin() {
      $this->osC_Language();
    }

/* Public methods */

    function load($definition = false) {
      if (is_string($definition) && file_exists('includes/languages/' . $this->getDirectory() . '/' . $definition)) {
        include('includes/languages/' . $this->getDirectory() . '/' . $definition);
      } else {
        include('includes/languages/' . $this->getDirectory() . '.php');
      }
    }

    function insert($language, $default = false) {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();

      $Qlanguage = $osC_Database->query('insert into :table_languages (name, code, image, directory, sort_order) values (:name, :code, :image, :directory, :sort_order)');
      $Qlanguage->bindTable(':table_languages', TABLE_LANGUAGES);
      $Qlanguage->bindValue(':name', $language['name']);
      $Qlanguage->bindValue(':code', $language['code']);
      $Qlanguage->bindValue(':image', $language['image']);
      $Qlanguage->bindValue(':directory', $language['directory']);
      $Qlanguage->bindInt(':sort_order', $language['sort_order']);
      $Qlanguage->execute();

      if ($osC_Database->isError() === false) {
        $language_id = $osC_Database->nextID();
        $default_language = $this->get(DEFAULT_LANGUAGE);

// create additional categories_description records
        $Qcategories = $osC_Database->query('select categories_id, categories_name from :table_categories_description where language_id = :language_id');
        $Qcategories->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
        $Qcategories->bindInt(':language_id', $default_language['id']);
        $Qcategories->execute();

        while ($Qcategories->next()) {
          $Qinsert = $osC_Database->query('insert into :table_categories_description (categories_id, language_id, categories_name) values (:categories_id, :language_id, :categories_name)');
          $Qinsert->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
          $Qinsert->bindInt(':categories_id', $Qcategories->valueInt('categories_id'));
          $Qinsert->bindInt(':language_id', $language_id);
          $Qinsert->bindValue(':categories_name', $Qcategories->value('categories_name'));
          $Qinsert->execute();

          if ($osC_Database->isError()) {
            $error = true;
            break;
          }
        }

        if ($error === false) {
// create additional products_description records
          $Qproducts = $osC_Database->query('select products_id, products_name, products_description, products_url from :table_products_description where language_id = :language_id');
          $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
          $Qproducts->bindInt(':language_id', $default_language['id']);
          $Qproducts->execute();

          while ($Qproducts->next()) {
            $Qinsert = $osC_Database->query('insert into :table_products_description (products_id, language_id, products_name, products_description, products_url) values (:products_id, :language_id, :products_name, :products_description, :products_url)');
            $Qinsert->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
            $Qinsert->bindInt(':products_id', $Qproducts->valueInt('products_id'));
            $Qinsert->bindInt(':language_id', $language_id);
            $Qinsert->bindInt(':products_name', $Qproducts->value('products_name'));
            $Qinsert->bindInt(':products_description', $Qproducts->value('products_description'));
            $Qinsert->bindInt(':products_url', $Qproducts->value('products_url'));
            $Qinsert->execute();

            if ($osC_Database->isError()) {
              $error = true;
              break;
            }
          }
        }

        if ($error === false) {
// create additional products_options records
          $Qoptions = $osC_Database->query('select products_options_id, products_options_name from :table_products_options where language_id = :language_id');
          $Qoptions->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
          $Qoptions->bindInt(':language_id', $default_language['id']);
          $Qoptions->execute();

          while ($Qoptions->next()) {
            $Qinsert = $osC_Database->query('insert into :table_products_options (products_options_id, language_id, products_options_name) values (:products_options_id, :language_id, :products_options_name)');
            $Qinsert->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
            $Qinsert->bindInt(':products_options_id', $Qoptions->valueInt('products_options_id'));
            $Qinsert->bindInt(':language_id', $language_id);
            $Qinsert->bindValue(':products_options_name', $Qoptions->value('products_options_name'));
            $Qinsert->execute();

            if ($osC_Database->isError()) {
              $error = true;
              break;
            }
          }
        }

        if ($error === false) {
// create additional products_options_values records
          $Qvalues = $osC_Database->query('select products_options_values_id, products_options_values_name from :table_products_options_values where language_id = :language_id');
          $Qvalues->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
          $Qvalues->bindInt(':language_id', $default_language['id']);
          $Qvalues->execute();

          while ($Qvalues->next()) {
            $Qinsert = $osC_Database->query('insert into :table_products_options_values (products_options_values_id, language_id, products_options_values_name) values (:products_options_values_id, :language_id, :products_options_values_name)');
            $Qinsert->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
            $Qinsert->bindInt(':products_options_values_id', $Qvalues->valueInt('products_options_values_id'));
            $Qinsert->bindInt(':language_id', $language_id);
            $Qinsert->bindValue(':products_options_values_name', $Qvalues->value('products_options_values_name'));
            $Qinsert->execute();

            if ($osC_Database->isError()) {
              $error = true;
              break;
            }
          }
        }

        if ($error === false) {
// create additional manufacturers_info records
          $Qmanufacturers = $osC_Database->query('select manufacturers_id, manufacturers_url from :table_manufacturers_info where languages_id = :languages_id');
          $Qmanufacturers->bindTable(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
          $Qmanufacturers->bindInt(':languages_id', $default_language['id']);
          $Qmanufacturers->execute();

          while ($Qmanufacturers->next()) {
            $Qinsert = $osC_Database->query('insert into :table_manufacturers_info (manufacturers_id, languages_id, manufacturers_url) values (:manufacturers_id, :languages_id, :manufacturers_url)');
            $Qinsert->bindTable(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
            $Qinsert->bindInt(':manufacturers_id', $Qmanufacturers->valueInt('manufacturers_id'));
            $Qinsert->bindInt(':languages_id', $language_id);
            $Qinsert->bindValue(':manufacturers_url', $Qmanufacturers->value('manufacturers_url'));
            $Qinsert->execute();

            if ($osC_Database->isError()) {
              $error = true;
              break;
            }
          }
        }

        if ($error === false) {
// create additional orders_status records
          $Qstatus = $osC_Database->query('select orders_status_id, orders_status_name from :table_orders_status where language_id = :language_id');
          $Qstatus->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
          $Qstatus->bindInt(':language_id', $default_language['id']);
          $Qstatus->execute();

          while ($Qstatus->next()) {
            $Qinsert = $osC_Database->query('insert into :table_orders_status (orders_status_id, language_id, orders_status_name) values (:orders_status_id, :language_id, :orders_status_name)');
            $Qinsert->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
            $Qinsert->bindInt(':orders_status_id', $Qstatus->valueInt('orders_status_id'));
            $Qinsert->bindInt(':language_id', $language_id);
            $Qinsert->bindValue(':orders_status_name', $Qstatus->value('orders_status_name'));
            $Qinsert->execute();

            if ($osC_Database->isError()) {
              $error = true;
              break;
            }
          }
        }
      } else {
        $error = true;
      }

      if ($error === false) {
        if ($default === true) {
          $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
          $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
          $Qupdate->bindValue(':configuration_value', $language['code']);
          $Qupdate->bindValue(':configuration_key', 'DEFAULT_LANGUAGE');
          $Qupdate->execute();

          if ($osC_Database->isError() === false) {
            if ($Qupdate->affectedRows()) {
              osC_Cache::clear('configuration');
            }
          } else {
            $error = true;
          }
        }
      }

      if ($error === false) {
        $osC_Database->commitTransaction();

        osC_Cache::clear('languages');

        return true;
      } else {
        $osC_Database->rollbackTransaction();
      }

      return false;
    }

    function update($id, $language, $default = false) {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();

      $Qlanguage = $osC_Database->query('update :table_languages set name = :name, code = :code, image = :image, directory = :directory, sort_order = :sort_order where languages_id = :languages_id');
      $Qlanguage->bindTable(':table_languages', TABLE_LANGUAGES);
      $Qlanguage->bindValue(':name', $language['name']);
      $Qlanguage->bindValue(':code', $language['code']);
      $Qlanguage->bindValue(':image', $language['image']);
      $Qlanguage->bindValue(':directory', $language['directory']);
      $Qlanguage->bindInt(':sort_order', $language['sort_order']);
      $Qlanguage->bindInt(':languages_id', $id);
      $Qlanguage->execute();

      if ($osC_Database->isError()) {
        $error = true;
      }

      if ($error === false) {
        if ($default === true) {
          $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
          $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
          $Qupdate->bindValue(':configuration_value', $language['code']);
          $Qupdate->bindValue(':configuration_key', 'DEFAULT_LANGUAGE');
          $Qupdate->execute();

          if ($osC_Database->isError() === false) {
            if ($Qupdate->affectedRows()) {
              osC_Cache::clear('configuration');
            }
          } else {
            $error = true;
          }
        }
      }

      if ($error === false) {
        $osC_Database->commitTransaction();

        osC_Cache::clear('languages');

        return true;
      } else {
        $osC_Database->rollbackTransaction();
      }

      return false;
    }

    function remove($id) {
      global $osC_Database;

      $Qcheck = $osC_Database->query('select code from :table_languages where languages_id = :languages_id');
      $Qcheck->bindTable(':table_languages', TABLE_LANGUAGES);
      $Qcheck->bindInt(':languages_id', $id);
      $Qcheck->execute();

      if ($Qcheck->value('code') != DEFAULT_LANGUAGE) {
        $error = false;

        $osC_Database->startTransaction();

        $Qcategories = $osC_Database->query('delete from :table_categories_description where language_id = :language_id');
        $Qcategories->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
        $Qcategories->bindInt(':language_id', $id);
        $Qcategories->execute();

        if ($osC_Database->isError()) {
          $error = true;
        }

        if ($error === false) {
          $Qproducts = $osC_Database->query('delete from :table_products_description where language_id = :language_id');
          $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
          $Qproducts->bindInt(':language_id', $id);
          $Qproducts->execute();

          if ($osC_Database->isError()) {
            $error = true;
          }
        }

        if ($error === false) {
          $Qproducts = $osC_Database->query('delete from :table_products_options where language_id = :language_id');
          $Qproducts->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
          $Qproducts->bindInt(':language_id', $id);
          $Qproducts->execute();

          if ($osC_Database->isError()) {
            $error = true;
          }
        }

        if ($error === false) {
          $Qproducts = $osC_Database->query('delete from :table_products_options_values where language_id = :language_id');
          $Qproducts->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
          $Qproducts->bindInt(':language_id', $id);
          $Qproducts->execute();

          if ($osC_Database->isError()) {
            $error = true;
          }
        }

        if ($error === false) {
          $Qmanufacturers = $osC_Database->query('delete from :table_manufacturers_info where languages_id = :languages_id');
          $Qmanufacturers->bindTable(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
          $Qmanufacturers->bindInt(':languages_id', $id);
          $Qmanufacturers->execute();

          if ($osC_Database->isError()) {
            $error = true;
          }
        }

        if ($error === false) {
          $Qstatus = $osC_Database->query('delete from :table_orders_status where language_id = :language_id');
          $Qstatus->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
          $Qstatus->bindInt(':language_id', $id);
          $Qstatus->execute();

          if ($osC_Database->isError()) {
            $error = true;
          }
        }

        if ($error === false) {
          $Qlanguages = $osC_Database->query('delete from :table_languages where languages_id = :languages_id');
          $Qlanguages->bindTable(':table_languages', TABLE_LANGUAGES);
          $Qlanguages->bindInt(':languages_id', $id);
          $Qlanguages->execute();

          if ($osC_Database->isError()) {
            $error = true;
          }
        }

        if ($error === false) {
          $osC_Database->commitTransaction();

          osC_Cache::clear('languages');

          return true;
        } else {
          $osC_Database->rollbackTransaction();
        }
      }

      return false;
    }
  }
?>

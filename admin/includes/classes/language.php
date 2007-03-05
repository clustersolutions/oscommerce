<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  require('../includes/classes/language.php');

  class osC_Language_Admin extends osC_Language {

/* Public methods */
    function loadConstants($definition = false) {
      if (is_string($definition) && file_exists('includes/languages/' . $this->getCode() . '/' . $definition)) {
        include('includes/languages/' . $this->getCode() . '/' . $definition);
      } elseif ($definition === false) {
        include('includes/languages/' . $this->getCode() . '.php');
      }
    }

    function injectDefinitions($file) {
      foreach ($this->extractDefinitions($this->_code . '/' . $file) as $def) {
        $this->_definitions[$def['key']] = $def['value'];
      }
    }

    function &extractDefinitions($xml) {
      $osC_XML = new osC_XML(file_get_contents(dirname(__FILE__) . '/../../../includes/languages/' . $xml));

      $definitions = $osC_XML->toArray();

      if (isset($definitions['language']['definitions']['definition'][0]) === false) {
        $definitions['language']['definitions']['definition'] = array($definitions['language']['definitions']['definition']);
      }

      return $definitions['language']['definitions']['definition'];
    }

    function export($id, $groups, $include_language_data = true) {
      global $osC_Database, $osC_Currencies;

      $language = osC_Language_Admin::getData($id);

      $export_array = array();

      if ( $include_language_data === true ) {
        $export_array['language']['data'] = array('title-CDATA' => $language['name'],
                                                  'code-CDATA' => $language['code'],
                                                  'locale-CDATA' => $language['locale'],
                                                  'character_set-CDATA' => $language['charset'],
                                                  'text_direction-CDATA' => $language['text_direction'],
                                                  'date_format_short-CDATA' => $language['date_format_short'],
                                                  'date_format_long-CDATA' => $language['date_format_long'],
                                                  'time_format-CDATA' => $language['time_format'],
                                                  'default_currency-CDATA' => $osC_Currencies->getCode($language['currencies_id']),
                                                  'numerical_decimal_separator-CDATA' => $language['numeric_separator_decimal'],
                                                  'numerical_thousands_separator-CDATA' => $language['numeric_separator_thousands']);
      }

      $Qdefs = $osC_Database->query('select content_group, definition_key, definition_value from :table_languages_definitions where languages_id = :languages_id and content_group in (":content_group") order by content_group, definition_key');
      $Qdefs->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
      $Qdefs->bindInt(':languages_id', $id);
      $Qdefs->bindRaw(':content_group', implode('", "', $groups));
      $Qdefs->execute();

      while ($Qdefs->next()) {
        $export_array['language']['definitions']['definition'][] = array('key' => $Qdefs->value('definition_key'),
                                                                         'value-CDATA' => $Qdefs->value('definition_value'),
                                                                         'group' => $Qdefs->value('content_group'));
      }

      $osC_XML = new osC_XML($export_array, $language['charset']);
      $xml = $osC_XML->toXML();

      header('Content-disposition: attachment; filename=' . $language['code'] . '.xml');
      header('Content-Type: application/force-download');
      header('Content-Transfer-Encoding: binary');
      header('Content-Length: ' . strlen($xml));
      header('Pragma: no-cache');
      header('Expires: 0');

      echo $xml;

      exit;
    }

    function import($file, $type) {
      global $osC_Database, $osC_Currencies;

      if (file_exists('../includes/languages/' . $file . '.xml')) {
        $osC_XML = new osC_XML(file_get_contents('../includes/languages/' . $file . '.xml'));
        $source = $osC_XML->toArray();

        $language = array('name' => $source['language']['data']['title'],
                          'code' => $source['language']['data']['code'],
                          'locale' => $source['language']['data']['locale'],
                          'charset' => $source['language']['data']['character_set'],
                          'date_format_short' => $source['language']['data']['date_format_short'],
                          'date_format_long' => $source['language']['data']['date_format_long'],
                          'time_format' => $source['language']['data']['time_format'],
                          'text_direction' => $source['language']['data']['text_direction'],
                          'currency' => $source['language']['data']['default_currency'],
                          'numeric_separator_decimal' => $source['language']['data']['numerical_decimal_separator'],
                          'numeric_separator_thousands' => $source['language']['data']['numerical_thousands_separator']
                         );

        if (!$osC_Currencies->exists($language['currency'])) {
          $language['currency'] = DEFAULT_CURRENCY;
        }

        $definitions = $source['language']['definitions']['definition'];

        unset($source);

        $error = false;
        $add_category_and_product_placeholders = true;

        $osC_Database->startTransaction();

        $Qcheck = $osC_Database->query('select languages_id from :table_languages where code = :code');
        $Qcheck->bindTable(':table_languages', TABLE_LANGUAGES);
        $Qcheck->bindValue(':code', $language['code']);
        $Qcheck->execute();

        if ($Qcheck->numberOfRows() === 1) {
          $add_category_and_product_placeholders = false;

          $language_id = $Qcheck->valueInt('languages_id');

          $Qlanguage = $osC_Database->query('update :table_languages set name = :name, code = :code, locale = :locale, charset = :charset, date_format_short = :date_format_short, date_format_long = :date_format_long, time_format = :time_format, text_direction = :text_direction, currencies_id = :currencies_id, numeric_separator_decimal = :numeric_separator_decimal, numeric_separator_thousands = :numeric_separator_thousands where languages_id = :languages_id');
          $Qlanguage->bindInt(':languages_id', $language_id);
        } else {
          $Qlanguage = $osC_Database->query('insert into :table_languages (name, code, locale, charset, date_format_short, date_format_long, time_format, text_direction, currencies_id, numeric_separator_decimal, numeric_separator_thousands) values (:name, :code, :locale, :charset, :date_format_short, :date_format_long, :time_format, :text_direction, :currencies_id, :numeric_separator_decimal, :numeric_separator_thousands)');
        }
        $Qlanguage->bindTable(':table_languages', TABLE_LANGUAGES);
        $Qlanguage->bindValue(':name', $language['name']);
        $Qlanguage->bindValue(':code', $language['code']);
        $Qlanguage->bindValue(':locale', $language['locale']);
        $Qlanguage->bindValue(':charset', $language['charset']);
        $Qlanguage->bindValue(':date_format_short', $language['date_format_short']);
        $Qlanguage->bindValue(':date_format_long', $language['date_format_long']);
        $Qlanguage->bindValue(':time_format', $language['time_format']);
        $Qlanguage->bindValue(':text_direction', $language['text_direction']);
        $Qlanguage->bindInt(':currencies_id', $osC_Currencies->getID($language['currency']));
        $Qlanguage->bindValue(':numeric_separator_decimal', $language['numeric_separator_decimal']);
        $Qlanguage->bindValue(':numeric_separator_thousands', $language['numeric_separator_thousands']);
        $Qlanguage->setLogging($_SESSION['module'], ($Qcheck->numberOfRows() === 1 ? $language_id : null));
        $Qlanguage->execute();

        if ($osC_Database->isError()) {
          $error = true;
        } else {
          if ($Qcheck->numberOfRows() !== 1) {
            $language_id = $osC_Database->nextID();
          }

          $default_language_id = osC_Language_Admin::getData(osC_Language_Admin::getID(DEFAULT_LANGUAGE), 'languages_id');

          if ($type == 'replace') {
            $Qdel =  $osC_Database->query('delete from :table_languages_definitions where languages_id = :languages_id');
            $Qdel->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
            $Qdel->bindInt(':languages_id', $language_id);
            $Qdel->execute();

            if ($osC_Database->isError()) {
              $error = true;
            }
          }
        }

        if ($error === false) {
          $osC_DirectoryListing = new osC_DirectoryListing('../includes/languages/' . $file);
          $osC_DirectoryListing->setRecursive(true);
          $osC_DirectoryListing->setIncludeDirectories(false);
          $osC_DirectoryListing->setAddDirectoryToFilename(true);
          $osC_DirectoryListing->setCheckExtension('xml');

          foreach ($osC_DirectoryListing->getFiles() as $files) {
            $definitions = array_merge($definitions, osC_Language_Admin::extractDefinitions($file . '/' . $files['name']));
          }

          foreach ($definitions as $def) {
            $insert = false;
            $update = false;

            if ($type == 'replace') {
              $insert = true;
            } else {
              $Qcheck = $osC_Database->query('select definition_key, content_group from :table_languages_definitions where definition_key = :definition_key and languages_id = :languages_id and content_group = :content_group');
              $Qcheck->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
              $Qcheck->bindValue(':definition_key', $def['key']);
              $Qcheck->bindInt(':languages_id', $language_id);
              $Qcheck->bindValue(':content_group', $def['group']);
              $Qcheck->execute();

              if ($Qcheck->numberOfRows() > 0) {
                if ($type == 'update') {
                  $update = true;
                }
              } elseif ($type == 'add') {
                $insert = true;
              }
            }

            if ( ($insert === true) || ($update === true) ) {
              if ($insert === true) {
                $Qdef = $osC_Database->query('insert into :table_languages_definitions (languages_id, content_group, definition_key, definition_value) values (:languages_id, :content_group, :definition_key, :definition_value)');
              } else {
                $Qdef = $osC_Database->query('update :table_languages_definitions set content_group = :content_group, definition_key = :definition_key, definition_value = :definition_value where definition_key = :definition_key and languages_id = :languages_id and content_group = :content_group');
                $Qdef->bindValue(':definition_key', $def['key']);
                $Qdef->bindValue(':content_group', $def['group']);
              }
              $Qdef->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
              $Qdef->bindInt(':languages_id', $language_id);
              $Qdef->bindValue(':content_group', $def['group']);
              $Qdef->bindValue(':definition_key', $def['key']);
              $Qdef->bindValue(':definition_value', $def['value']);
              $Qdef->execute();

              if ($osC_Database->isError()) {
                $error = true;
                break;
              }
            }
          }
        }

        if ($add_category_and_product_placeholders === true) {
          if ($error === false) {
            $Qcategories = $osC_Database->query('select categories_id, categories_name from :table_categories_description where language_id = :language_id');
            $Qcategories->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
            $Qcategories->bindInt(':language_id', $default_language_id);
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
          }

          if ($error === false) {
            $Qproducts = $osC_Database->query('select products_id, products_name, products_description, products_model, products_keyword, products_tags, products_url from :table_products_description where language_id = :language_id');
            $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
            $Qproducts->bindInt(':language_id', $default_language_id);
            $Qproducts->execute();

            while ($Qproducts->next()) {
              $Qinsert = $osC_Database->query('insert into :table_products_description (products_id, language_id, products_name, products_description, products_model, products_keyword, products_tags, products_url) values (:products_id, :language_id, :products_name, :products_description, :products_model, :products_keyword, :products_tags, :products_url)');
              $Qinsert->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
              $Qinsert->bindInt(':products_id', $Qproducts->valueInt('products_id'));
              $Qinsert->bindInt(':language_id', $language_id);
              $Qinsert->bindValue(':products_name', $Qproducts->value('products_name'));
              $Qinsert->bindValue(':products_description', $Qproducts->value('products_description'));
              $Qinsert->bindValue(':products_model', $Qproducts->value('products_model'));
              $Qinsert->bindValue(':products_keyword', $Qproducts->value('products_keyword'));
              $Qinsert->bindValue(':products_tags', $Qproducts->value('products_tags'));
              $Qinsert->bindValue(':products_url', $Qproducts->value('products_url'));
              $Qinsert->execute();

              if ($osC_Database->isError()) {
                $error = true;
                break;
              }
            }
          }

          if ($error === false) {
            $Qoptions = $osC_Database->query('select products_options_id, products_options_name from :table_products_options where language_id = :language_id');
            $Qoptions->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
            $Qoptions->bindInt(':language_id', $default_language_id);
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
            $Qvalues = $osC_Database->query('select products_options_values_id, products_options_values_name from :table_products_options_values where language_id = :language_id');
            $Qvalues->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
            $Qvalues->bindInt(':language_id', $default_language_id);
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
            $Qmanufacturers = $osC_Database->query('select manufacturers_id, manufacturers_url from :table_manufacturers_info where languages_id = :languages_id');
            $Qmanufacturers->bindTable(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
            $Qmanufacturers->bindInt(':languages_id', $default_language_id);
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
            $Qstatus = $osC_Database->query('select orders_status_id, orders_status_name from :table_orders_status where language_id = :language_id');
            $Qstatus->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
            $Qstatus->bindInt(':language_id', $default_language_id);
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

          if ($error === false) {
            $Qgroup = $osC_Database->query('select id, title, code, size_width, size_height, force_size from :table_products_images_groups where language_id = :language_id');
            $Qgroup->bindTable(':table_products_images_groups', TABLE_PRODUCTS_IMAGES_GROUPS);
            $Qgroup->bindInt(':language_id', $default_language_id);
            $Qgroup->execute();

            while ($Qgroup->next()) {
              $Qinsert = $osC_Database->query('insert into :table_products_images_groups (id, language_id, title, code, size_width, size_height, force_size) values (:id, :language_id, :title, :code, :size_width, :size_height, :force_size)');
              $Qinsert->bindTable(':table_products_images_groups', TABLE_PRODUCTS_IMAGES_GROUPS);
              $Qinsert->bindInt(':id', $Qgroup->valueInt('id'));
              $Qinsert->bindInt(':language_id', $language_id);
              $Qinsert->bindValue(':title', $Qgroup->value('title'));
              $Qinsert->bindValue(':code', $Qgroup->value('code'));
              $Qinsert->bindInt(':size_width', $Qgroup->value('size_width'));
              $Qinsert->bindInt(':size_height', $Qgroup->value('size_height'));
              $Qinsert->bindInt(':force_size', $Qgroup->value('force_size'));
              $Qinsert->execute();

              if ($osC_Database->isError()) {
                $error = true;
                break;
              }
            }
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

    function getData($id, $key = null) {
      global $osC_Database;

      $Qlanguage = $osC_Database->query('select * from :table_languages where languages_id = :languages_id');
      $Qlanguage->bindTable(':table_languages', TABLE_LANGUAGES);
      $Qlanguage->bindInt(':languages_id', $id);
      $Qlanguage->execute();

      $result = $Qlanguage->toArray();

      $Qlanguage->freeResult();

      if ( empty($key) ) {
        return $result;
      } else {
        return $result[$key];
      }
    }

    function getID($code = null) {
      global $osC_Database;

      if ( empty($code) ) {
        return $this->_languages[$this->_code]['id'];
      }

      $Qlanguage = $osC_Database->query('select languages_id from :table_languages where code = :code');
      $Qlanguage->bindTable(':table_languages', TABLE_LANGUAGES);
      $Qlanguage->bindValue(':code', $code);
      $Qlanguage->execute();

      $result = $Qlanguage->toArray();

      $Qlanguage->freeResult();

      return $result['languages_id'];
    }

    function update($id, $language, $default = false) {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();

      $Qlanguage = $osC_Database->query('update :table_languages set name = :name, code = :code, locale = :locale, charset = :charset, date_format_short = :date_format_short, date_format_long = :date_format_long, time_format = :time_format, text_direction = :text_direction, currencies_id = :currencies_id, numeric_separator_decimal = :numeric_separator_decimal, numeric_separator_thousands = :numeric_separator_thousands, sort_order = :sort_order where languages_id = :languages_id');
      $Qlanguage->bindTable(':table_languages', TABLE_LANGUAGES);
      $Qlanguage->bindValue(':name', $language['name']);
      $Qlanguage->bindValue(':code', $language['code']);
      $Qlanguage->bindValue(':locale', $language['locale']);
      $Qlanguage->bindValue(':charset', $language['charset']);
      $Qlanguage->bindValue(':date_format_short', $language['date_format_short']);
      $Qlanguage->bindValue(':date_format_long', $language['date_format_long']);
      $Qlanguage->bindValue(':time_format', $language['time_format']);
      $Qlanguage->bindValue(':text_direction', $language['text_direction']);
      $Qlanguage->bindInt(':currencies_id', $language['currencies_id']);
      $Qlanguage->bindValue(':numeric_separator_decimal', $language['numeric_separator_decimal']);
      $Qlanguage->bindValue(':numeric_separator_thousands', $language['numeric_separator_thousands']);
      $Qlanguage->bindInt(':sort_order', $language['sort_order']);
      $Qlanguage->bindInt(':languages_id', $id);
      $Qlanguage->setLogging($_SESSION['module'], $id);
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
          $Qupdate->setLogging($_SESSION['module'], $id);
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

    function saveDefinitions($id, $group, $data) {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();

      foreach ($data as $key => $value) {
        $Qupdate = $osC_Database->query('update :table_languages_definitions set definition_value = :definition_value where definition_key = :definition_key and languages_id = :languages_id and content_group = :content_group');
        $Qupdate->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
        $Qupdate->bindValue(':definition_value', $value);
        $Qupdate->bindValue(':definition_key', $key);
        $Qupdate->bindInt(':languages_id', $id);
        $Qupdate->bindValue(':content_group', $group);
        $Qupdate->setLogging($_SESSION['module'], $id);
        $Qupdate->execute();

        if ($osC_Database->isError()) {
          $error = true;
          break;
        }
      }

      if ($error === false) {
        $osC_Database->commitTransaction();

        osC_Cache::clear('languages-' . osC_Language_Admin::getData($id, 'code') . '-' . $group);

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    function insertDefinition($group, $data) {
      global $osC_Database, $osC_Language;

      $error = false;

      $osC_Database->startTransaction();

      foreach ($osC_Language->getAll() as $l) {
        $Qdefinition = $osC_Database->query('insert into :table_languages_definitions (languages_id, content_group, definition_key, definition_value) values (:languages_id, :content_group, :definition_key, :definition_value)');
        $Qdefinition->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
        $Qdefinition->bindInt(':languages_id', $l['id']);
        $Qdefinition->bindValue(':content_group', $group);
        $Qdefinition->bindValue(':definition_key', $data['key']);
        $Qdefinition->bindValue(':definition_value', $data['value'][$l['id']]);
        $Qdefinition->setLogging($_SESSION['module']);
        $Qdefinition->execute();

        if ($osC_Database->isError()) {
          $error = true;
          break;
        }
      }

      if ($error === false) {
        $osC_Database->commitTransaction();

        osC_Cache::clear('languages-' . osC_Language_Admin::getData($id, 'code') . '-' . $group);

        return true;
      }

      $osC_Database->rollbackTransaction();

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
          $Qgroup = $osC_Database->query('delete from :table_products_images_groups where language_id = :language_id');
          $Qgroup->bindTable(':table_products_images_groups', TABLE_PRODUCTS_IMAGES_GROUPS);
          $Qgroup->bindInt(':language_id', $id);
          $Qgroup->execute();

          if ($osC_Database->isError()) {
            $error = true;
          }
        }

        if ($error === false) {
          $Qlanguages = $osC_Database->query('delete from :table_languages where languages_id = :languages_id');
          $Qlanguages->bindTable(':table_languages', TABLE_LANGUAGES);
          $Qlanguages->bindInt(':languages_id', $id);
          $Qlanguages->setLogging($_SESSION['module'], $id);
          $Qlanguages->execute();

          if ($osC_Database->isError()) {
            $error = true;
          }
        }

        if ($error === false) {
          $Qdefinitions = $osC_Database->query('delete from :table_languages_definitions where languages_id = :languages_id');
          $Qdefinitions->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
          $Qdefinitions->bindInt(':languages_id', $id);
          $Qdefinitions->execute();

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

    function deleteDefinitions($language_id, $group, $keys) {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();

      foreach ($keys as $id) {
        $Qdel = $osC_Database->query('delete from :table_languages_definitions where id = :id');
        $Qdel->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
        $Qdel->bindValue(':id', $id);
        $Qdel->setLogging($_SESSION['module'], $id);
        $Qdel->execute();

        if ($osC_Database->isError()) {
          $error = true;
          break;
        }
      }

      if ($error === false) {
        $osC_Database->commitTransaction();

        osC_Cache::clear('languages-' . osC_Language_Admin::getData($language_id, 'code') . '-' . $group);

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }
  }
?>

<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Languages_Admin {
    public static function get($id, $key = null) {
      global $osC_Database;

      $result = false;

      $Qlanguage = $osC_Database->query('select * from :table_languages where');

      if ( is_numeric($id) ) {
        $Qlanguage->appendQuery('languages_id = :languages_id');
        $Qlanguage->bindInt(':languages_id', $id);
      } else {
        $Qlanguage->appendQuery('code = :code');
        $Qlanguage->bindValue(':code', $id);
      }

      $Qlanguage->bindTable(':table_languages', TABLE_LANGUAGES);
      $Qlanguage->execute();

      if ( $Qlanguage->numberOfRows() === 1 ) {
        $Qdef = $osC_Database->query('select count(*) as total_definitions from :table_languages_definitions where languages_id = :languages_id');
        $Qdef->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
        $Qdef->bindInt(':languages_id', $Qlanguage->valueInt('languages_id'));
        $Qdef->execute();

        $result = array_merge($Qlanguage->toArray(), $Qdef->toArray());

        if ( !empty($key) && isset($result[$key]) ) {
          $result = $result[$key];
        }
      }

      return $result;
    }

    public static function exists($id) {
      return ( self::get($id) !== false );
    }

    public static function getAll($pageset = 1) {
      global $osC_Database;

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array('entries' => array());

      $Qlanguages = $osC_Database->query('select SQL_CALC_FOUND_ROWS * from :table_languages order by sort_order, name');
      $Qlanguages->bindTable(':table_languages', TABLE_LANGUAGES);

      if ( $pageset !== -1 ) {
        $Qlanguages->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qlanguages->execute();

      while ( $Qlanguages->next() ) {
        $Qdef = $osC_Database->query('select count(*) as total_definitions from :table_languages_definitions where languages_id = :languages_id');
        $Qdef->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
        $Qdef->bindInt(':languages_id', $Qlanguages->valueInt('languages_id'));
        $Qdef->execute();

        $result['entries'][] = array_merge($Qlanguages->toArray(), $Qdef->toArray());
      }

      $result['total'] = $Qlanguages->getBatchSize();

      $Qlanguages->freeResult();

      return $result;
    }

    public static function find($search, $pageset = 1) {
      global $osC_Database;

      if ( !is_numeric($pageset) || (floor($pageset) != $pageset) ) {
        $pageset = 1;
      }

      $result = array('entries' => array());

      $Qlanguages = $osC_Database->query('select SQL_CALC_FOUND_ROWS l.* from :table_languages l left join :table_languages_definitions ld on (l.languages_id = ld.languages_id) where (l.name like :name or l.code like :code or ld.definition_key like :definition_key or ld.definition_value like :definition_value) group by l.languages_id order by l.name');
      $Qlanguages->bindTable(':table_languages', TABLE_LANGUAGES);
      $Qlanguages->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
      $Qlanguages->bindValue(':name', '%' . $search . '%');
      $Qlanguages->bindValue(':code', '%' . $search . '%');
      $Qlanguages->bindValue(':definition_key', '%' . $search . '%');
      $Qlanguages->bindValue(':definition_value', '%' . $search . '%');

      if ( $pageset !== -1 ) {
        $Qlanguages->setBatchLimit($pageset, MAX_DISPLAY_SEARCH_RESULTS);
      }

      $Qlanguages->execute();

      while ( $Qlanguages->next() ) {
        $Qdef = $osC_Database->query('select count(*) as total_definitions from :table_languages_definitions where languages_id = :languages_id');
        $Qdef->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
        $Qdef->bindInt(':languages_id', $Qlanguages->valueInt('languages_id'));
        $Qdef->execute();

        $result['entries'][] = array_merge($Qlanguages->toArray(), $Qdef->toArray());
      }

      $result['total'] = $Qlanguages->getBatchSize();

      $Qlanguages->freeResult();

      return $result;
    }

    public static function getDefinitionGroup($group) {
      global $osC_Database;

      $result = array('entries' => array());

      $Qgroup = $osC_Database->query('select languages_id, count(*) as total_entries from :table_languages_definitions where content_group = :content_group group by languages_id');
      $Qgroup->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
      $Qgroup->bindValue(':content_group', $group);
      $Qgroup->execute();

      while ( $Qgroup->next() ) {
        $result['entries'][] = $Qgroup->toArray();
      }

      $result['total'] = $Qgroup->numberOfRows();

      $Qgroup->freeResult();

      return $result;
    }

    public static function getDefinitionGroups($language_id) {
      global $osC_Database;

      $result = array('entries' => array());

      $Qgroups = $osC_Database->query('select distinct content_group, count(*) as total_entries from :table_languages_definitions where languages_id = :languages_id group by content_group order by content_group');
      $Qgroups->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
      $Qgroups->bindInt(':languages_id', $language_id);
      $Qgroups->execute();

      while ( $Qgroups->next() ) {
        $result['entries'][] = $Qgroups->toArray();
      }

      $result['total'] = $Qgroups->numberOfRows();

      $Qgroups->freeResult();

      return $result;
    }

    public static function isDefinitionGroup($language_id, $group) {
      global $osC_Database;

      $Qgroup = $osC_Database->query('select id from :table_languages_definitions where languages_id = :languages_id and content_group = :content_group limit 1');
      $Qgroup->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
      $Qgroup->bindInt(':languages_id', $language_id);
      $Qgroup->bindValue(':content_group', $group);
      $Qgroup->execute();

      $result = false;

      if ( $Qgroup->numberOfRows() === 1 ) {
        $result = true;
      }

      $Qgroup->freeResult();

      return $result;
    }

    public static function findDefinitionGroups($language_id, $search) {
      global $osC_Database;

      $result = array('entries' => array());

      $Qgroups = $osC_Database->query('select distinct content_group from :table_languages_definitions where languages_id = :languages_id and (definition_key like :definition_key or definition_value like :definition_value) group by content_group order by content_group');
      $Qgroups->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
      $Qgroups->bindInt(':languages_id', $language_id);
      $Qgroups->bindValue(':definition_key', '%' . $search . '%');
      $Qgroups->bindValue(':definition_value', '%' . $search . '%');
      $Qgroups->execute();

      while ( $Qgroups->next() ) {
        $Qtotal = $osC_Database->query('select count(*) as total_entries from :table_languages_definitions where content_group = :content_group');
        $Qtotal->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
        $Qtotal->bindValue(':content_group', $Qgroups->value('content_group'));
        $Qtotal->execute();

        $result['entries'][] = array_merge($Qgroups->toArray(), $Qtotal->toArray());
      }

      $result['total'] = $Qgroups->numberOfRows();

      $Qtotal->freeResult();
      $Qgroups->freeResult();

      return $result;
    }

    public static function deleteDefinitionGroup($group) {
      global $osC_Database;

      $Qdel = $osC_Database->query('delete from :table_languages_definitions where content_group = :content_group');
      $Qdel->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
      $Qdel->bindValue(':content_group', $group);
      $Qdel->setLogging($_SESSION['module']);
      $Qdel->execute();

      if ( !$osC_Database->isError() ) {
        osC_Cache::clear('languages');

        return true;
      }

      return false;
    }

    public static function getDefinition($id) {
      global $osC_Database;

      $Qdef = $osC_Database->query('select * from :table_languages_definitions where id = :id');
      $Qdef->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
      $Qdef->bindInt(':id', $id);
      $Qdef->execute();

      $result = $Qdef->toArray();

      $Qdef->freeResult();

      return $result;
    }

    public static function getDefinitions($language_id, $group) {
      global $osC_Database;

      $result = array('entries' => array());

      $Qdefs = $osC_Database->query('select * from :table_languages_definitions where languages_id = :languages_id and');

      if ( is_array($group) ) {
        $Qdefs->appendQuery('content_group in :content_group');
        $Qdefs->bindRaw(':content_group', '("' . implode('", "', $group) . '")');
      } else {
        $Qdefs->appendQuery('content_group = :content_group');
        $Qdefs->bindValue(':content_group', $group);
      }

      $Qdefs->appendQuery('order by content_group, definition_key');
      $Qdefs->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
      $Qdefs->bindInt(':languages_id', $language_id);
      $Qdefs->execute();

      while ( $Qdefs->next() ) {
        $result['entries'][] = $Qdefs->toArray();
      }

      $result['total'] = $Qdefs->numberOfRows();

      $Qdefs->freeResult();

      return $result;
    }

    public static function findDefinitions($language_id, $group, $search) {
      global $osC_Database;

      $result = array('entries' => array());

      $Qdefs = $osC_Database->query('select * from :table_languages_definitions where languages_id = :languages_id and content_group = :content_group and (definition_key like :definition_key or definition_value like :definition_value) order by definition_key');
      $Qdefs->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
      $Qdefs->bindInt(':languages_id', $language_id);
      $Qdefs->bindValue(':content_group', $group);
      $Qdefs->bindValue(':definition_key', '%' . $search . '%');
      $Qdefs->bindValue(':definition_value', '%' . $search . '%');
      $Qdefs->execute();

      while ( $Qdefs->next() ) {
        $result['entries'][] = $Qdefs->toArray();
      }

      $result['total'] = $Qdefs->numberOfRows();

      $Qdefs->freeResult();

      return $result;
    }

    public static function insertDefinition($group, $data) {
      global $osC_Database, $osC_Language;

      $error = false;

      $osC_Database->startTransaction();

      foreach ( osc_toObjectInfo(self::getAll(-1))->get('entries') as $l) {
        $Qdef = $osC_Database->query('insert into :table_languages_definitions (languages_id, content_group, definition_key, definition_value) values (:languages_id, :content_group, :definition_key, :definition_value)');
        $Qdef->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
        $Qdef->bindInt(':languages_id', $l['languages_id']);
        $Qdef->bindValue(':content_group', $group);
        $Qdef->bindValue(':definition_key', $data['key']);
        $Qdef->bindValue(':definition_value', $data['value'][$l['languages_id']]);
        $Qdef->setLogging($_SESSION['module']);
        $Qdef->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
          break;
        }

        osC_Cache::clear('languages-' . $l['code'] . '-' . $group);
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    public static function updateDefinitions($language_id, $group, $data) {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();

      foreach ( $data as $key => $value ) {
        $Qupdate = $osC_Database->query('update :table_languages_definitions set definition_value = :definition_value where definition_key = :definition_key and languages_id = :languages_id and content_group = :content_group');
        $Qupdate->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
        $Qupdate->bindValue(':definition_value', $value);
        $Qupdate->bindValue(':definition_key', $key);
        $Qupdate->bindInt(':languages_id', $language_id);
        $Qupdate->bindValue(':content_group', $group);
        $Qupdate->setLogging($_SESSION['module'], $language_id);
        $Qupdate->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
          break;
        }
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        osC_Cache::clear('languages-' . self::get($language_id, 'code') . '-' . $group);

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    public static function deleteDefinitions($language_id, $group, $keys) {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();

      foreach ( $keys as $id ) {
        $Qdel = $osC_Database->query('delete from :table_languages_definitions where id = :id');
        $Qdel->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
        $Qdel->bindValue(':id', $id);
        $Qdel->setLogging($_SESSION['module'], $id);
        $Qdel->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
          break;
        }
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        osC_Cache::clear('languages-' . self::get($language_id, 'code') . '-' . $group);

        return true;
      }

      $osC_Database->rollbackTransaction();

      return false;
    }

    public static function update($id, $data, $default = false) {
      global $osC_Database;

      $error = false;

      $osC_Database->startTransaction();

      $Qlanguage = $osC_Database->query('update :table_languages set name = :name, code = :code, locale = :locale, charset = :charset, date_format_short = :date_format_short, date_format_long = :date_format_long, time_format = :time_format, text_direction = :text_direction, currencies_id = :currencies_id, numeric_separator_decimal = :numeric_separator_decimal, numeric_separator_thousands = :numeric_separator_thousands, parent_id = :parent_id, sort_order = :sort_order where languages_id = :languages_id');
      $Qlanguage->bindTable(':table_languages', TABLE_LANGUAGES);
      $Qlanguage->bindValue(':name', $data['name']);
      $Qlanguage->bindValue(':code', $data['code']);
      $Qlanguage->bindValue(':locale', $data['locale']);
      $Qlanguage->bindValue(':charset', $data['charset']);
      $Qlanguage->bindValue(':date_format_short', $data['date_format_short']);
      $Qlanguage->bindValue(':date_format_long', $data['date_format_long']);
      $Qlanguage->bindValue(':time_format', $data['time_format']);
      $Qlanguage->bindValue(':text_direction', $data['text_direction']);
      $Qlanguage->bindInt(':currencies_id', $data['currencies_id']);
      $Qlanguage->bindValue(':numeric_separator_decimal', $data['numeric_separator_decimal']);
      $Qlanguage->bindValue(':numeric_separator_thousands', $data['numeric_separator_thousands']);
      $Qlanguage->bindInt(':parent_id', $data['parent_id']);
      $Qlanguage->bindInt(':sort_order', $data['sort_order']);
      $Qlanguage->bindInt(':languages_id', $id);
      $Qlanguage->setLogging($_SESSION['module'], $id);
      $Qlanguage->execute();

      if ( $osC_Database->isError() ) {
        $error = true;
      }

      if ( $error === false ) {
        if ( $default === true ) {
          $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
          $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
          $Qupdate->bindValue(':configuration_value', $data['code']);
          $Qupdate->bindValue(':configuration_key', 'DEFAULT_LANGUAGE');
          $Qupdate->setLogging($_SESSION['module'], $id);
          $Qupdate->execute();

          if ( $osC_Database->isError() === false ) {
            if ( $Qupdate->affectedRows() ) {
              osC_Cache::clear('configuration');
            }
          } else {
            $error = true;
          }
        }
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        osC_Cache::clear('languages');

        return true;
      } else {
        $osC_Database->rollbackTransaction();
      }

      return false;
    }

    public static function delete($id) {
      global $osC_Database;

      if ( self::get($id, 'code') != DEFAULT_LANGUAGE ) {
        $error = false;

        $osC_Database->startTransaction();

        $Qcategories = $osC_Database->query('delete from :table_categories_description where language_id = :language_id');
        $Qcategories->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
        $Qcategories->bindInt(':language_id', $id);
        $Qcategories->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
        }

        if ( $error === false ) {
          $Qproducts = $osC_Database->query('delete from :table_products_description where language_id = :language_id');
          $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
          $Qproducts->bindInt(':language_id', $id);
          $Qproducts->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }

        if ( $error === false ) {
          $Qproducts = $osC_Database->query('delete from :table_product_attributes where languages_id = :languages_id');
          $Qproducts->bindTable(':table_product_attributes', TABLE_PRODUCT_ATTRIBUTES);
          $Qproducts->bindInt(':languages_id', $id);
          $Qproducts->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }

        if ( $error === false ) {
          $Qproducts = $osC_Database->query('delete from :table_products_variants_groups where languages_id = :languages_id');
          $Qproducts->bindTable(':table_products_variants_groups', TABLE_PRODUCTS_VARIANTS_GROUPS);
          $Qproducts->bindInt(':languages_id', $id);
          $Qproducts->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }

        if ( $error === false ) {
          $Qproducts = $osC_Database->query('delete from :table_products_variants_values where languages_id = :languages_id');
          $Qproducts->bindTable(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
          $Qproducts->bindInt(':languages_id', $id);
          $Qproducts->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }

        if ( $error === false ) {
          $Qmanufacturers = $osC_Database->query('delete from :table_manufacturers_info where languages_id = :languages_id');
          $Qmanufacturers->bindTable(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
          $Qmanufacturers->bindInt(':languages_id', $id);
          $Qmanufacturers->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }

        if ( $error === false ) {
          $Qmanufacturers = $osC_Database->query('delete from :table_reviews where languages_id = :languages_id');
          $Qmanufacturers->bindTable(':table_reviews', TABLE_REVIEWS);
          $Qmanufacturers->bindInt(':languages_id', $id);
          $Qmanufacturers->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }

        if ( $error === false ) {
          $Qstatus = $osC_Database->query('delete from :table_orders_status where language_id = :language_id');
          $Qstatus->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
          $Qstatus->bindInt(':language_id', $id);
          $Qstatus->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }

        if ( $error === false ) {
          $Qgroup = $osC_Database->query('delete from :table_products_images_groups where language_id = :language_id');
          $Qgroup->bindTable(':table_products_images_groups', TABLE_PRODUCTS_IMAGES_GROUPS);
          $Qgroup->bindInt(':language_id', $id);
          $Qgroup->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }

        if ( $error === false ) {
          $Qgroup = $osC_Database->query('delete from :table_shipping_availability where languages_id = :languages_id');
          $Qgroup->bindTable(':table_shipping_availability', TABLE_SHIPPING_AVAILABILITY);
          $Qgroup->bindInt(':languages_id', $id);
          $Qgroup->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }

        if ( $error === false ) {
          $Qgroup = $osC_Database->query('delete from :table_weight_classes where language_id = :language_id');
          $Qgroup->bindTable(':table_weight_classes', TABLE_WEIGHT_CLASS);
          $Qgroup->bindInt(':language_id', $id);
          $Qgroup->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }

        if ( $error === false ) {
          $Qlanguages = $osC_Database->query('delete from :table_languages where languages_id = :languages_id');
          $Qlanguages->bindTable(':table_languages', TABLE_LANGUAGES);
          $Qlanguages->bindInt(':languages_id', $id);
          $Qlanguages->setLogging($_SESSION['module'], $id);
          $Qlanguages->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }

        if ( $error === false ) {
          $Qdefinitions = $osC_Database->query('delete from :table_languages_definitions where languages_id = :languages_id');
          $Qdefinitions->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
          $Qdefinitions->bindInt(':languages_id', $id);
          $Qdefinitions->execute();

          if ( $osC_Database->isError() ) {
            $error = true;
          }
        }

        if ( $error === false ) {
          $osC_Database->commitTransaction();

          osC_Cache::clear('languages');

          return true;
        } else {
          $osC_Database->rollbackTransaction();
        }
      }

      return false;
    }

    public static function export($id, $groups, $include_language_data = true) {
      global $osC_Database;

      $language = self::get($id);

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
                                                  'default_currency-CDATA' => osC_Currencies_Admin::get($language['currencies_id'], 'code'),
                                                  'numerical_decimal_separator-CDATA' => $language['numeric_separator_decimal'],
                                                  'numerical_thousands_separator-CDATA' => $language['numeric_separator_thousands']);

        if ( $language['parent_id'] > 0 ) {
          $export_array['language']['data']['parent_language_code'] = osC_Languages_Admin::get($language['parent_id'], 'code');
        }
      }

      foreach ( osc_toObjectInfo(self::getDefinitions($id, $groups))->get('entries') as $def ) {
        $export_array['language']['definitions']['definition'][] = array('key' => $def['definition_key'],
                                                                         'value-CDATA' => $def['definition_value'],
                                                                         'group' => $def['content_group']);
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

    public static function getDirectoryListing() {
      $result = array();

      $osC_DirectoryListing = new osC_DirectoryListing('../includes/languages');
      $osC_DirectoryListing->setIncludeDirectories(false);
      $osC_DirectoryListing->setCheckExtension('xml');

      foreach ( $osC_DirectoryListing->getFiles() as $file ) {
        $result[] = substr($file['name'], 0, strrpos($file['name'], '.'));
      }

      return $result;
    }

    public static function import($file, $type) {
      global $osC_Database;

      if ( file_exists('../includes/languages/' . $file . '.xml') ) {
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
                          'numeric_separator_thousands' => $source['language']['data']['numerical_thousands_separator'],
                          'parent_language_code' => (isset($source['language']['data']['parent_language_code']) ? $source['language']['data']['parent_language_code'] : ''),
                          'parent_id' => 0);

        if ( osC_Currencies_Admin::exists($language['currency']) === false ) {
          $language['currency'] = DEFAULT_CURRENCY;
        }

        if ( !empty($language['parent_language_code']) && self::exists($language['parent_language_code']) ) {
          $language['parent_id'] = self::get($language['parent_language_code'], 'languages_id');
        }

        $definitions = array();

        if ( isset($source['language']['definitions']['definition']) ) {
          $definitions = $source['language']['definitions']['definition'];

          if ( isset($definitions['key']) && isset($definitions['value']) && isset($definitions['group']) ) {
            $definitions = array(array('key' => $definitions['key'],
                                       'value' => $definitions['value'],
                                       'group' => $definitions['group']));
          }
        }

        unset($source);

        $error = false;
        $add_category_and_product_placeholders = true;

        $osC_Database->startTransaction();

        $language_id = self::get($language['code'], 'languages_id');

        if ( $language_id !== false ) {
          $add_category_and_product_placeholders = false;

          $Qlanguage = $osC_Database->query('update :table_languages set name = :name, code = :code, locale = :locale, charset = :charset, date_format_short = :date_format_short, date_format_long = :date_format_long, time_format = :time_format, text_direction = :text_direction, currencies_id = :currencies_id, numeric_separator_decimal = :numeric_separator_decimal, numeric_separator_thousands = :numeric_separator_thousands, parent_id = :parent_id where languages_id = :languages_id');
          $Qlanguage->bindInt(':languages_id', $language_id);
        } else {
          $Qlanguage = $osC_Database->query('insert into :table_languages (name, code, locale, charset, date_format_short, date_format_long, time_format, text_direction, currencies_id, numeric_separator_decimal, numeric_separator_thousands, parent_id) values (:name, :code, :locale, :charset, :date_format_short, :date_format_long, :time_format, :text_direction, :currencies_id, :numeric_separator_decimal, :numeric_separator_thousands, :parent_id)');
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
        $Qlanguage->bindInt(':currencies_id', osC_Currencies_Admin::get($language['currency'], 'currencies_id'));
        $Qlanguage->bindValue(':numeric_separator_decimal', $language['numeric_separator_decimal']);
        $Qlanguage->bindValue(':numeric_separator_thousands', $language['numeric_separator_thousands']);
        $Qlanguage->bindInt(':parent_id', $language['parent_id']);
        $Qlanguage->setLogging($_SESSION['module'], ($language_id !== false ? $language_id : null));
        $Qlanguage->execute();

        if ( $osC_Database->isError() ) {
          $error = true;
        } else {
          if ( $language_id === false ) {
            $language_id = $osC_Database->nextID();
          }

          $default_language_id = osC_Languages_Admin::get(DEFAULT_LANGUAGE, 'languages_id');

          if ( $type == 'replace' ) {
            $Qdel =  $osC_Database->query('delete from :table_languages_definitions where languages_id = :languages_id');
            $Qdel->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
            $Qdel->bindInt(':languages_id', $language_id);
            $Qdel->execute();

            if ( $osC_Database->isError() ) {
              $error = true;
            }
          }
        }

        if ( $error === false ) {
          $osC_DirectoryListing = new osC_DirectoryListing('../includes/languages/' . $file);
          $osC_DirectoryListing->setRecursive(true);
          $osC_DirectoryListing->setIncludeDirectories(false);
          $osC_DirectoryListing->setAddDirectoryToFilename(true);
          $osC_DirectoryListing->setCheckExtension('xml');

          foreach ( $osC_DirectoryListing->getFiles() as $files ) {
            $definitions = array_merge($definitions, osC_Language_Admin::extractDefinitions($file . '/' . $files['name']));
          }

          foreach ( $definitions as $def ) {
            $insert = false;
            $update = false;

            if ( $type == 'replace' ) {
              $insert = true;
            } else {
              $Qcheck = $osC_Database->query('select definition_key, content_group from :table_languages_definitions where definition_key = :definition_key and languages_id = :languages_id and content_group = :content_group');
              $Qcheck->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
              $Qcheck->bindValue(':definition_key', $def['key']);
              $Qcheck->bindInt(':languages_id', $language_id);
              $Qcheck->bindValue(':content_group', $def['group']);
              $Qcheck->execute();

              if ( $Qcheck->numberOfRows() > 0 ) {
                if ( $type == 'update' ) {
                  $update = true;
                }
              } elseif ( $type == 'add' ) {
                $insert = true;
              }
            }

            if ( ($insert === true) || ($update === true) ) {
              if ( $insert === true ) {
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

              if ( $osC_Database->isError() ) {
                $error = true;
                break;
              }
            }
          }
        }

        if ( $add_category_and_product_placeholders === true ) {
          if ( $error === false ) {
            $Qcategories = $osC_Database->query('select categories_id, categories_name from :table_categories_description where language_id = :language_id');
            $Qcategories->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
            $Qcategories->bindInt(':language_id', $default_language_id);
            $Qcategories->execute();

            while ( $Qcategories->next() ) {
              $Qinsert = $osC_Database->query('insert into :table_categories_description (categories_id, language_id, categories_name) values (:categories_id, :language_id, :categories_name)');
              $Qinsert->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
              $Qinsert->bindInt(':categories_id', $Qcategories->valueInt('categories_id'));
              $Qinsert->bindInt(':language_id', $language_id);
              $Qinsert->bindValue(':categories_name', $Qcategories->value('categories_name'));
              $Qinsert->execute();

              if ( $osC_Database->isError() ) {
                $error = true;
                break;
              }
            }
          }

          if ( $error === false ) {
            $Qproducts = $osC_Database->query('select products_id, products_name, products_description, products_keyword, products_tags, products_url from :table_products_description where language_id = :language_id');
            $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
            $Qproducts->bindInt(':language_id', $default_language_id);
            $Qproducts->execute();

            while ( $Qproducts->next() ) {
              $Qinsert = $osC_Database->query('insert into :table_products_description (products_id, language_id, products_name, products_description, products_keyword, products_tags, products_url) values (:products_id, :language_id, :products_name, :products_description, :products_keyword, :products_tags, :products_url)');
              $Qinsert->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
              $Qinsert->bindInt(':products_id', $Qproducts->valueInt('products_id'));
              $Qinsert->bindInt(':language_id', $language_id);
              $Qinsert->bindValue(':products_name', $Qproducts->value('products_name'));
              $Qinsert->bindValue(':products_description', $Qproducts->value('products_description'));
              $Qinsert->bindValue(':products_keyword', $Qproducts->value('products_keyword'));
              $Qinsert->bindValue(':products_tags', $Qproducts->value('products_tags'));
              $Qinsert->bindValue(':products_url', $Qproducts->value('products_url'));
              $Qinsert->execute();

              if ( $osC_Database->isError() ) {
                $error = true;
                break;
              }
            }
          }

          if ( $error === false ) {
            $Qattributes = $osC_Database->query('select products_id, value from :table_product_attributes where languages_id = :languages_id');
            $Qattributes->bindTable(':table_product_attributes', TABLE_PRODUCT_ATTRIBUTES);
            $Qattributes->bindInt(':languages_id', $default_language_id);
            $Qattributes->execute();

            while ( $Qattributes->next() ) {
              $Qinsert = $osC_Database->query('insert into :table_product_attributes (products_id, languages_id, value) values (:products_id, :languages_id, :value)');
              $Qinsert->bindTable(':table_product_attributes', TABLE_PRODUCT_ATTRIBUTES);
              $Qinsert->bindInt(':products_id', $Qattributes->valueInt('products_id'));
              $Qinsert->bindInt(':languages_id', $language_id);
              $Qinsert->bindValue(':value', $Qattributes->value('value'));
              $Qinsert->execute();

              if ( $osC_Database->isError() ) {
                $error = true;
                break;
              }
            }
          }

          if ( $error === false ) {
            $Qgroups = $osC_Database->query('select id, title, sort_order, module from :table_products_variants_groups where languages_id = :languages_id');
            $Qgroups->bindTable(':table_products_variants_groups', TABLE_PRODUCTS_VARIANTS_GROUPS);
            $Qgroups->bindInt(':languages_id', $default_language_id);
            $Qgroups->execute();

            while ( $Qgroups->next() ) {
              $Qinsert = $osC_Database->query('insert into :table_products_variants_groups (id, languages_id, title, sort_order, module) values (:id, :languages_id, :title, :sort_order, :module)');
              $Qinsert->bindTable(':table_products_variants_groups', TABLE_PRODUCTS_VARIANTS_GROUPS);
              $Qinsert->bindInt(':id', $Qgroups->valueInt('id'));
              $Qinsert->bindInt(':languages_id', $language_id);
              $Qinsert->bindValue(':title', $Qgroups->value('title'));
              $Qinsert->bindInt(':sort_order', $Qgroups->valueInt('sort_order'));
              $Qinsert->bindValue(':module', $Qgroups->value('module'));
              $Qinsert->execute();

              if ( $osC_Database->isError() ) {
                $error = true;
                break;
              }
            }
          }

          if ( $error === false ) {
            $Qvalues = $osC_Database->query('select id, products_variants_groups_id, title, sort_order from :table_products_variants_values where languages_id = :languages_id');
            $Qvalues->bindTable(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
            $Qvalues->bindInt(':languages_id', $default_language_id);
            $Qvalues->execute();

            while ( $Qvalues->next() ) {
              $Qinsert = $osC_Database->query('insert into :table_products_variants_values (id, languages_id, products_variants_groups_id, title, sort_order) values (:id, :languages_id, :products_variants_groups_id, :title, :sort_order)');
              $Qinsert->bindTable(':table_products_variants_values', TABLE_PRODUCTS_VARIANTS_VALUES);
              $Qinsert->bindInt(':id', $Qvalues->valueInt('id'));
              $Qinsert->bindInt(':languages_id', $language_id);
              $Qinsert->bindInt(':products_variants_groups_id', $Qvalues->valueInt('products_variants_groups_id'));
              $Qinsert->bindValue(':title', $Qvalues->value('title'));
              $Qinsert->bindInt(':sort_order', $Qvalues->valueInt('sort_order'));
              $Qinsert->execute();

              if ( $osC_Database->isError() ) {
                $error = true;
                break;
              }
            }
          }

          if ( $error === false ) {
            $Qmanufacturers = $osC_Database->query('select manufacturers_id, manufacturers_url from :table_manufacturers_info where languages_id = :languages_id');
            $Qmanufacturers->bindTable(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
            $Qmanufacturers->bindInt(':languages_id', $default_language_id);
            $Qmanufacturers->execute();

            while ( $Qmanufacturers->next() ) {
              $Qinsert = $osC_Database->query('insert into :table_manufacturers_info (manufacturers_id, languages_id, manufacturers_url) values (:manufacturers_id, :languages_id, :manufacturers_url)');
              $Qinsert->bindTable(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
              $Qinsert->bindInt(':manufacturers_id', $Qmanufacturers->valueInt('manufacturers_id'));
              $Qinsert->bindInt(':languages_id', $language_id);
              $Qinsert->bindValue(':manufacturers_url', $Qmanufacturers->value('manufacturers_url'));
              $Qinsert->execute();

              if ( $osC_Database->isError() ) {
                $error = true;
                break;
              }
            }
          }

          if ( $error === false ) {
            $Qstatus = $osC_Database->query('select orders_status_id, orders_status_name from :table_orders_status where language_id = :language_id');
            $Qstatus->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
            $Qstatus->bindInt(':language_id', $default_language_id);
            $Qstatus->execute();

            while ( $Qstatus->next() ) {
              $Qinsert = $osC_Database->query('insert into :table_orders_status (orders_status_id, language_id, orders_status_name) values (:orders_status_id, :language_id, :orders_status_name)');
              $Qinsert->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
              $Qinsert->bindInt(':orders_status_id', $Qstatus->valueInt('orders_status_id'));
              $Qinsert->bindInt(':language_id', $language_id);
              $Qinsert->bindValue(':orders_status_name', $Qstatus->value('orders_status_name'));
              $Qinsert->execute();

              if ( $osC_Database->isError() ) {
                $error = true;
                break;
              }
            }
          }

          if ( $error === false ) {
            $Qstatus = $osC_Database->query('select id, status_name from :table_orders_transactions_status where language_id = :language_id');
            $Qstatus->bindTable(':table_orders_transactions_status', TABLE_ORDERS_TRANSACTIONS_STATUS);
            $Qstatus->bindInt(':language_id', $default_language_id);
            $Qstatus->execute();

            while ( $Qstatus->next() ) {
              $Qinsert = $osC_Database->query('insert into :table_orders_transactions_status (id, language_id, status_name) values (:id, :language_id, :status_name)');
              $Qinsert->bindTable(':table_orders_transactions_status', TABLE_ORDERS_TRANSACTIONS_STATUS);
              $Qinsert->bindInt(':id', $Qstatus->valueInt('id'));
              $Qinsert->bindInt(':language_id', $language_id);
              $Qinsert->bindValue(':status_name', $Qstatus->value('status_name'));
              $Qinsert->execute();

              if ( $osC_Database->isError() ) {
                $error = true;
                break;
              }
            }
          }

          if ( $error === false ) {
            $Qstatus = $osC_Database->query('select id, title, css_key from :table_shipping_availability where languages_id = :languages_id');
            $Qstatus->bindTable(':table_shipping_availability', TABLE_SHIPPING_AVAILABILITY);
            $Qstatus->bindInt(':languages_id', $default_language_id);
            $Qstatus->execute();

            while ( $Qstatus->next() ) {
              $Qinsert = $osC_Database->query('insert into :table_shipping_availability (id, languages_id, title, css_key) values (:id, :languages_id, :title, :css_key)');
              $Qinsert->bindTable(':table_shipping_availability', TABLE_SHIPPING_AVAILABILITY);
              $Qinsert->bindInt(':id', $Qstatus->valueInt('id'));
              $Qinsert->bindInt(':languages_id', $language_id);
              $Qinsert->bindValue(':title', $Qstatus->value('title'));
              $Qinsert->bindValue(':css_key', $Qstatus->value('css_key'));
              $Qinsert->execute();

              if ( $osC_Database->isError() ) {
                $error = true;
                break;
              }
            }
          }

          if ( $error === false ) {
            $Qstatus = $osC_Database->query('select weight_class_id, weight_class_key, weight_class_title from :table_weight_classes where language_id = :language_id');
            $Qstatus->bindTable(':table_weight_classes', TABLE_WEIGHT_CLASS);
            $Qstatus->bindInt(':language_id', $default_language_id);
            $Qstatus->execute();

            while ( $Qstatus->next() ) {
              $Qinsert = $osC_Database->query('insert into :table_weight_classes (weight_class_id, weight_class_key, language_id, weight_class_title) values (:weight_class_id, :weight_class_key, :language_id, :weight_class_title)');
              $Qinsert->bindTable(':table_weight_classes', TABLE_WEIGHT_CLASS);
              $Qinsert->bindInt(':weight_class_id', $Qstatus->valueInt('weight_class_id'));
              $Qinsert->bindValue(':weight_class_key', $Qstatus->value('weight_class_key'));
              $Qinsert->bindInt(':language_id', $language_id);
              $Qinsert->bindValue(':weight_class_title', $Qstatus->value('weight_class_title'));
              $Qinsert->execute();

              if ( $osC_Database->isError() ) {
                $error = true;
                break;
              }
            }
          }

          if ( $error === false ) {
            $Qgroup = $osC_Database->query('select id, title, code, size_width, size_height, force_size from :table_products_images_groups where language_id = :language_id');
            $Qgroup->bindTable(':table_products_images_groups', TABLE_PRODUCTS_IMAGES_GROUPS);
            $Qgroup->bindInt(':language_id', $default_language_id);
            $Qgroup->execute();

            while ( $Qgroup->next() ) {
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

              if ( $osC_Database->isError() ) {
                $error = true;
                break;
              }
            }
          }
        }
      }

      if ( $error === false ) {
        $osC_Database->commitTransaction();

        osC_Cache::clear('languages');

        return true;
      } else {
        $osC_Database->rollbackTransaction();
      }

      return false;
    }
  }
?>

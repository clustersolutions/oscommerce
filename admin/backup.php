<?php
/*
  $Id: backup.php,v 1.65 2004/11/02 00:47:55 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $selected_box = 'tools';

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (!empty($action)) {
    switch ($action) {
      case 'forget':
        $Qcfg = $osC_Database->query('delete from :table_configuration where configuration_key = :configuration_key');
        $Qcfg->bindTable(':table_configuration', TABLE_CONFIGURATION);
        $Qcfg->bindValue(':configuration_key', 'DB_LAST_RESTORE');
        $Qcfg->execute();

        if ($Qcfg->affectedRows()) {
          $osC_Cache->clear('configuration');
        }

        $osC_MessageStack->add_session('header', SUCCESS_LAST_RESTORE_CLEARED, 'success');

        tep_redirect(tep_href_link(FILENAME_BACKUP));
        break;
      case 'backupnow':
        tep_set_time_limit(0);

        $backup_file = 'db_' . DB_DATABASE . '-' . date('YmdHis') . '.sql';
        $fp = fopen(DIR_FS_BACKUP . $backup_file, 'w');

        $schema = '# osCommerce, Open Source E-Commerce Solutions' . "\n" .
                  '# http://www.oscommerce.com' . "\n" .
                  '#' . "\n" .
                  '# Database Backup For ' . STORE_NAME . "\n" .
                  '# Copyright (c) ' . date('Y') . ' ' . STORE_OWNER . "\n" .
                  '#' . "\n" .
                  '# Database: ' . DB_DATABASE . "\n" .
                  '# Database Server: ' . DB_SERVER . "\n" .
                  '#' . "\n" .
                  '# Backup Date: ' . date(PHP_DATE_TIME_FORMAT) . "\n\n";
        fputs($fp, $schema);

        $Qtables = $osC_Database->query('show tables');
        while ($Qtables->next()) {
          list(,$table) = each($Qtables->toArray());

          $schema = 'drop table if exists ' . $table . ';' . "\n" .
                    'create table ' . $table . ' (' . "\n";

          $table_list = array();

          $Qfields = $osC_Database->query('show fields from :table');
          $Qfields->bindTable(':table', $table);
          $Qfields->execute();

          while ($Qfields->next()) {
            $table_list[] = $Qfields->value('Field');

            $schema .= '  ' . $Qfields->value('Field') . ' ' . $Qfields->value('Type');

            if (strlen($Qfields->value('Default')) > 0) $schema .= ' default \'' . $Qfields->value('Default') . '\'';

            if ($Qfields->value('Null') != 'YES') $schema .= ' not null';

            if (strlen($Qfields->value('Extra')) > 0) $schema .= ' ' . $Qfields->value('Extra');

            $schema .= ',' . "\n";
          }

          $schema = ereg_replace(",\n$", '', $schema);

// add the keys
          $index = array();

          $Qkeys = $osC_Database->query('show keys from :table');
          $Qkeys->bindTable(':table', $table);
          $Qkeys->execute();

          while ($Qkeys->next()) {
            $kname = $Qkeys->value('Key_name');

            if (!isset($index[$kname])) {
              $index[$kname] = array('unique' => !$Qkeys->value('Non_unique'),
                                     'columns' => array());
            }

            $index[$kname]['columns'][] = $Qkeys->value('Column_name');
          }

          while (list($kname, $info) = each($index)) {
            $schema .= ',' . "\n";

            $columns = implode($info['columns'], ', ');

            if ($kname == 'PRIMARY') {
              $schema .= '  PRIMARY KEY (' . $columns . ')';
            } elseif ($info['unique']) {
              $schema .= '  UNIQUE ' . $kname . ' (' . $columns . ')';
            } else {
              $schema .= '  KEY ' . $kname . ' (' . $columns . ')';
            }
          }

          $schema .= "\n" . ');' . "\n\n";
          fputs($fp, $schema);

// dump the data
          $Qrows = $osC_Database->query('select :columns from :table');
          $Qrows->bindRaw(':columns', implode(', ', $table_list));
          $Qrows->bindTable(':table', $table);
          $Qrows->execute();

          while ($Qrows->next()) {
            $rows = $Qrows->toArray();

            $schema = 'insert into ' . $table . ' (' . implode(', ', $table_list) . ') values (';

            reset($table_list);
            while (list(,$i) = each($table_list)) {
              if (!isset($rows[$i])) {
                $schema .= 'NULL, ';
              } elseif (strlen($rows[$i]) > 0) {
                $row = addslashes($rows[$i]);
                $row = ereg_replace("\n#", "\n".'\#', $row);

                $schema .= '\'' . $row . '\', ';
              } else {
                $schema .= '\'\', ';
              }
            }

            $schema = ereg_replace(', $', '', $schema) . ');' . "\n";
            fputs($fp, $schema);
          }
        }

        fclose($fp);

        if (isset($_POST['download']) && ($_POST['download'] == 'yes')) {
          switch ($_POST['compress']) {
            case 'gzip':
              exec(LOCAL_EXE_GZIP . ' ' . DIR_FS_BACKUP . $backup_file);
              $backup_file .= '.gz';
              break;
            case 'zip':
              exec(LOCAL_EXE_ZIP . ' -j ' . DIR_FS_BACKUP . $backup_file . '.zip ' . DIR_FS_BACKUP . $backup_file);
              unlink(DIR_FS_BACKUP . $backup_file);
              $backup_file .= '.zip';
          }
          header('Content-type: application/x-octet-stream');
          header('Content-disposition: attachment; filename=' . $backup_file);

          readfile(DIR_FS_BACKUP . $backup_file);
          unlink(DIR_FS_BACKUP . $backup_file);

          exit;
        } else {
          switch ($_POST['compress']) {
            case 'gzip':
              exec(LOCAL_EXE_GZIP . ' ' . DIR_FS_BACKUP . $backup_file);
              break;
            case 'zip':
              exec(LOCAL_EXE_ZIP . ' -j ' . DIR_FS_BACKUP . $backup_file . '.zip ' . DIR_FS_BACKUP . $backup_file);
              unlink(DIR_FS_BACKUP . $backup_file);
          }

          $osC_MessageStack->add_session('header', SUCCESS_DATABASE_SAVED, 'success');
        }

        tep_redirect(tep_href_link(FILENAME_BACKUP));
        break;
      case 'restorenow':
      case 'restorelocalnow':
        tep_set_time_limit(0);

        if ($action == 'restorenow') {
          $read_from = basename($_GET['file']);

          if (file_exists(DIR_FS_BACKUP . $read_from)) {
            $restore_file = DIR_FS_BACKUP . $read_from;
            $extension = substr($read_from, -3);

            if ( ($extension == 'sql') || ($extension == '.gz') || ($extension == 'zip') ) {
              switch ($extension) {
                case 'sql':
                  $restore_from = $restore_file;
                  $remove_raw = false;
                  break;
                case '.gz':
                  $restore_from = substr($restore_file, 0, -3);
                  exec(LOCAL_EXE_GUNZIP . ' ' . $restore_file . ' -c > ' . $restore_from);
                  $remove_raw = true;
                  break;
                case 'zip':
                  $restore_from = substr($restore_file, 0, -4);
                  exec(LOCAL_EXE_UNZIP . ' ' . $restore_file . ' -d ' . DIR_FS_BACKUP);
                  $remove_raw = true;
              }

              if (isset($restore_from) && file_exists($restore_from) && (filesize($restore_from) > 15000)) {
                $fd = fopen($restore_from, 'rb');
                $restore_query = fread($fd, filesize($restore_from));
                fclose($fd);
              }
            }
          }
        } elseif ($action == 'restorelocalnow') {
          $sql_file = new upload('sql_file');
          $sql_file->set_output_messages('session');

          if ($sql_file->parse() == true) {
            $restore_query = fread(fopen($sql_file->tmp_filename, 'r'), filesize($sql_file->tmp_filename));
            $read_from = $sql_file->filename;
          }
        }

        if (isset($restore_query)) {
          $sql_array = array();
          $sql_length = strlen($restore_query);
          $pos = strpos($restore_query, ';');
          for ($i=$pos; $i<$sql_length; $i++) {
            if ($restore_query[0] == '#') {
              $restore_query = ltrim(substr($restore_query, strpos($restore_query, "\n")));
              $sql_length = strlen($restore_query);
              $i = strpos($restore_query, ';')-1;
              continue;
            }
            if ($restore_query[($i+1)] == "\n") {
              for ($j=($i+2); $j<$sql_length; $j++) {
                if (trim($restore_query[$j]) != '') {
                  $next = substr($restore_query, $j, 6);
                  if ($next[0] == '#') {
// find out where the break position is so we can remove this line (#comment line)
                    for ($k=$j; $k<$sql_length; $k++) {
                      if ($restore_query[$k] == "\n") break;
                    }
                    $query = substr($restore_query, 0, $i+1);
                    $restore_query = substr($restore_query, $k);
// join the query before the comment appeared, with the rest of the dump
                    $restore_query = $query . $restore_query;
                    $sql_length = strlen($restore_query);
                    $i = strpos($restore_query, ';')-1;
                    continue 2;
                  }
                  break;
                }
              }
              if ($next == '') { // get the last insert query
                $next = 'insert';
              }
              if ( (eregi('create', $next)) || (eregi('insert', $next)) || (eregi('drop t', $next)) ) {
                $next = '';
                $sql_array[] = substr($restore_query, 0, $i);
                $restore_query = ltrim(substr($restore_query, $i+1));
                $sql_length = strlen($restore_query);
                $i = strpos($restore_query, ';')-1;
              }
            }
          }

          $Qdrop = $osC_Database->query('drop table if exists :table_address_book, :table_address_format, :table_banners, :table_banners_history, :table_categories, :table_categories_description, :table_configuration, :table_configuration_group, :table_countries, :table_currencies, :table_customers, :table_customers_basket, :table_customers_basket_attributes, :table_customers_info, :table_languages, :table_manufacturers, :table_manufacturers_info, :table_newsletters, :table_orders, :table_orders_products, :table_orders_products_attributes, :table_orders_products_download, :table_orders_status, :table_orders_status_history, :table_orders_total, :table_products, :table_products_attributes, :table_products_attributes_download, :table_products_description, :table_products_notifications, :table_products_options, :table_products_options_values, :table_products_options_values_to_products_options, :table_products_to_categories, :table_reviews, :table_sessions, :table_specials, :table_tax_class, :table_tax_rates, :table_geo_zones, :table_zones_to_geo_zones, :table_whos_online, :table_zones');
          $Qdrop->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
          $Qdrop->bindTable(':table_address_format', TABLE_ADDRESS_FORMAT);
          $Qdrop->bindTable(':table_banners', TABLE_BANNERS);
          $Qdrop->bindTable(':table_banners_history', TABLE_BANNERS_HISTORY);
          $Qdrop->bindTable(':table_categories', TABLE_CATEGORIES);
          $Qdrop->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
          $Qdrop->bindTable(':table_configuration', TABLE_CONFIGURATION);
          $Qdrop->bindTable(':table_configuration_group', TABLE_CONFIGURATION_GROUP);
          $Qdrop->bindTable(':table_countries', TABLE_COUNTRIES);
          $Qdrop->bindTable(':table_currencies', TABLE_CURRENCIES);
          $Qdrop->bindTable(':table_customers', TABLE_CUSTOMERS);
          $Qdrop->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
          $Qdrop->bindTable(':table_customers_basket_attributes', TABLE_CUSTOMERS_BASKET_ATTRIBUTES);
          $Qdrop->bindTable(':table_customers_info', TABLE_CUSTOMERS_INFO);
          $Qdrop->bindTable(':table_languages', TABLE_LANGUAGES);
          $Qdrop->bindTable(':table_manufacturers', TABLE_MANUFACTURERS);
          $Qdrop->bindTable(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
          $Qdrop->bindTable(':table_newsletters', TABLE_NEWSLETTERS);
          $Qdrop->bindTable(':table_orders', TABLE_ORDERS);
          $Qdrop->bindTable(':table_orders_products', TABLE_ORDERS_PRODUCTS);
          $Qdrop->bindTable(':table_orders_products_attributes', TABLE_ORDERS_PRODUCTS_ATTRIBUTES);
          $Qdrop->bindTable(':table_orders_products_download', TABLE_ORDERS_PRODUCTS_DOWNLOAD);
          $Qdrop->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
          $Qdrop->bindTable(':table_orders_status_history', TABLE_ORDERS_STATUS_HISTORY);
          $Qdrop->bindTable(':table_orders_total', TABLE_ORDERS_TOTAL);
          $Qdrop->bindTable(':table_products', TABLE_PRODUCTS);
          $Qdrop->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
          $Qdrop->bindTable(':table_products_attributes_download', TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD);
          $Qdrop->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
          $Qdrop->bindTable(':table_products_notifications', TABLE_PRODUCTS_NOTIFICATIONS);
          $Qdrop->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
          $Qdrop->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
          $Qdrop->bindTable(':table_products_options_values_to_products_options', TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS);
          $Qdrop->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
          $Qdrop->bindTable(':table_reviews', TABLE_REVIEWS);
          $Qdrop->bindTable(':table_sessions', TABLE_SESSIONS);
          $Qdrop->bindTable(':table_specials', TABLE_SPECIALS);
          $Qdrop->bindTable(':table_tax_class', TABLE_TAX_CLASS);
          $Qdrop->bindTable(':table_tax_rates', TABLE_TAX_RATES);
          $Qdrop->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
          $Qdrop->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
          $Qdrop->bindTable(':table_whos_online', TABLE_WHOS_ONLINE);
          $Qdrop->bindTable(':table_zones', TABLE_ZONES);
          $Qdrop->execute();

          for ($i=0, $n=sizeof($sql_array); $i<$n; $i++) {
            $osC_Database->simpleQuery($sql_array[$i]);
          }

          $Qcfg = $osC_Database->query('delete from :table_configuration where configuration_key = :configuration_key');
          $Qcfg->bindTable(':table_configuration', TABLE_CONFIGURATION);
          $Qcfg->bindValue(':configuration_key', 'DB_LAST_RESTORE');
          $Qcfg->execute();

          $Qcfg = $osC_Database->query('insert into :table_configuration values ("", "Last Database Restore", "DB_LAST_RESTORE", :read_from, "Last database restore file", "6", "", "", now(), "", "")');
          $Qcfg->bindTable(':table_configuration', TABLE_CONFIGURATION);
          $Qcfg->bindValue(':read_from', $read_from);
          $Qcfg->execute();

          $osC_Cache->clear('configuration');

          if (isset($remove_raw) && ($remove_raw == true)) {
            unlink($restore_from);
          }

          $osC_MessageStack->add_session('header', SUCCESS_DATABASE_RESTORED, 'success');
        }

        tep_redirect(tep_href_link(FILENAME_BACKUP));
        break;
      case 'download':
        if (isset($_GET['file'])) {
          $extension = substr($_GET['file'], -3);

          if ( ($extension == 'zip') || ($extension == '.gz') || ($extension == 'sql') ) {
            if ($fp = fopen(DIR_FS_BACKUP . basename($_GET['file']), 'rb')) {
              $buffer = fread($fp, filesize(DIR_FS_BACKUP . basename($_GET['file'])));
              fclose($fp);

              header('Content-type: application/x-octet-stream');
              header('Content-disposition: attachment; filename=' . basename($_GET['file']));

              echo $buffer;

              exit;
            }
          } else {
            $osC_MessageStack->add('header', ERROR_DOWNLOAD_LINK_NOT_ACCEPTABLE, 'error');
          }
        }
        break;
      case 'deleteconfirm':
        if (isset($_GET['file'])) {
          if (file_exists(DIR_FS_BACKUP . basename($_GET['file']))) {
            tep_remove(DIR_FS_BACKUP . basename($_GET['file']));

            if (!$tep_remove_error) {
              $osC_MessageStack->add_session('header', SUCCESS_BACKUP_DELETED, 'success');

              tep_redirect(tep_href_link(FILENAME_BACKUP));
            }
          }
        }
        break;
    }
  }

// check if the backup directory exists
  $dir_ok = false;
  if (is_dir(DIR_FS_BACKUP)) {
    if (is_writeable(DIR_FS_BACKUP)) {
      $dir_ok = true;
    } else {
      $osC_MessageStack->add('header', ERROR_BACKUP_DIRECTORY_NOT_WRITEABLE, 'error');
    }
  } else {
    $osC_MessageStack->add('header', ERROR_BACKUP_DIRECTORY_DOES_NOT_EXIST, 'error');
  }

  $page_contents = 'backup.php';

  require('templates/default.php');

  require('includes/application_bottom.php');
?>

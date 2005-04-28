<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

////
// Redirect to another page or site
  function tep_redirect($url) {
    header('Location: ' . $url);

    exit;
  }

////
// Parse the data used in the html tags to ensure the tags will not break
  function tep_parse_input_field_data($data, $parse) {
    return strtr(trim($data), $parse);
  }

  function tep_output_string($string, $translate = false, $protected = false) {
    if ($protected == true) {
      return htmlspecialchars($string);
    } else {
      if ($translate == false) {
        return tep_parse_input_field_data($string, array('"' => '&quot;'));
      } else {
        return tep_parse_input_field_data($string, $translate);
      }
    }
  }

  function tep_output_string_protected($string) {
    return tep_output_string($string, false, true);
  }

  function tep_sanitize_string($string) {
    $string = ereg_replace(' +', ' ', $string);

    return preg_replace("/[<>]/", '_', $string);
  }

  function tep_date_long($raw_date) {
    if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) return false;

    $year = (int)substr($raw_date, 0, 4);
    $month = (int)substr($raw_date, 5, 2);
    $day = (int)substr($raw_date, 8, 2);
    $hour = (int)substr($raw_date, 11, 2);
    $minute = (int)substr($raw_date, 14, 2);
    $second = (int)substr($raw_date, 17, 2);

    return strftime(DATE_FORMAT_LONG, mktime($hour, $minute, $second, $month, $day, $year));
  }

////
// Output a raw date string in the selected locale date format
// $raw_date needs to be in this format: YYYY-MM-DD HH:MM:SS
// NOTE: Includes a workaround for dates before 01/01/1970 that fail on windows servers
  function tep_date_short($raw_date) {
    if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) return false;

    $year = substr($raw_date, 0, 4);
    $month = (int)substr($raw_date, 5, 2);
    $day = (int)substr($raw_date, 8, 2);
    $hour = (int)substr($raw_date, 11, 2);
    $minute = (int)substr($raw_date, 14, 2);
    $second = (int)substr($raw_date, 17, 2);

    if (@date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year) {
      return date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, $year));
    } else {
      return ereg_replace('2037' . '$', $year, date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, 2037)));
    }

  }

  function tep_datetime_short($raw_datetime) {
    if ( ($raw_datetime == '0000-00-00 00:00:00') || ($raw_datetime == '') ) return false;

    $year = (int)substr($raw_datetime, 0, 4);
    $month = (int)substr($raw_datetime, 5, 2);
    $day = (int)substr($raw_datetime, 8, 2);
    $hour = (int)substr($raw_datetime, 11, 2);
    $minute = (int)substr($raw_datetime, 14, 2);
    $second = (int)substr($raw_datetime, 17, 2);

    return strftime(DATE_TIME_FORMAT, mktime($hour, $minute, $second, $month, $day, $year));
  }

  function tep_break_string($string, $len, $break_char = '-') {
    $l = 0;
    $output = '';
    for ($i=0, $n=strlen($string); $i<$n; $i++) {
      $char = substr($string, $i, 1);
      if ($char != ' ') {
        $l++;
      } else {
        $l = 0;
      }
      if ($l > $len) {
        $l = 1;
        $output .= $break_char;
      }
      $output .= $char;
    }

    return $output;
  }

  function tep_get_country_name($country_id) {
    global $osC_Database;

    $Qcountry = $osC_Database->query('select countries_name from :table_countries where countries_id = :countries_id');
    $Qcountry->bindTable(':table_countries', TABLE_COUNTRIES);
    $Qcountry->bindInt(':countries_id', $country_id);
    $Qcountry->execute();

    return $Qcountry->value('countries_name');
  }

  function tep_get_zone_name($country_id, $zone_id, $default_zone) {
    global $osC_Database;

    $Qzone = $osC_Database->query('select zone_name from :table_zones where zone_country_id = :zone_country_id and zone_id = :zone_id');
    $Qzone->bindTable(':table_zones', TABLE_ZONES);
    $Qzone->bindInt(':zone_country_id', $country_id);
    $Qzone->bindInt(':zone_id', $zone_id);
    $Qzone->execute();

    return ($Qzone->numberOfRows() > 0) ? $Qzone->value('zone_name') : $default_zone;
  }

  function tep_not_null($value) {
    if (is_array($value)) {
      if (sizeof($value) > 0) {
        return true;
      } else {
        return false;
      }
    } else {
      if ( (is_string($value) || is_int($value)) && ($value != '') && ($value != 'NULL') && (strlen(trim($value)) > 0)) {
        return true;
      } else {
        return false;
      }
    }
  }

  function tep_browser_detect($component) {
    global $HTTP_USER_AGENT;

    return stristr($HTTP_USER_AGENT, $component);
  }

  function tep_geo_zones_pull_down($parameters, $selected = '') {
    global $osC_Database;

    $select_string = '<select ' . $parameters . '>';

    $Qzones = $osC_Database->query('select geo_zone_id, geo_zone_name from :table_geo_zones order by geo_zone_name');
    $Qzones->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
    $Qzones->execute();

    while ($Qzones->next()) {
      $select_string .= '<option value="' . $Qzones->valueInt('geo_zone_id') . '"';
      if ($selected == $Qzones->valueInt('geo_zone_id')) $select_string .= ' SELECTED';
      $select_string .= '>' . $Qzones->value('geo_zone_name') . '</option>';
    }
    $select_string .= '</select>';

    return $select_string;
  }

  function tep_get_geo_zone_name($geo_zone_id) {
    global $osC_Database;

    $Qzone = $osC_Database->query('select geo_zone_name from :table_geo_zones where geo_zone_id = :geo_zone_id');
    $Qzone->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
    $Qzone->bindInt('geo_zone_id', $geo_zone_id);
    $Qzone->execute();

    return $Qzone->value('geo_zone_name');
  }

  function tep_address_format($address_format_id, $address, $html, $boln, $eoln) {
    global $osC_Database;

    $Qformat = $osC_Database->query('select address_format from :table_address_format where address_format_id = :address_format_id');
    $Qformat->bindTable(':table_address_format', TABLE_ADDRESS_FORMAT);
    $Qformat->bindInt(':address_format_id', $address_format_id);
    $Qformat->execute();

    $company = tep_output_string_protected($address['company']);
    if (isset($address['firstname']) && tep_not_null($address['firstname'])) {
      $firstname = tep_output_string_protected($address['firstname']);
      $lastname = tep_output_string_protected($address['lastname']);
    } elseif (isset($address['name']) && tep_not_null($address['name'])) {
      $firstname = tep_output_string_protected($address['name']);
      $lastname = '';
    } else {
      $firstname = '';
      $lastname = '';
    }
    $street = tep_output_string_protected($address['street_address']);
    $suburb = tep_output_string_protected($address['suburb']);
    $city = tep_output_string_protected($address['city']);
    $state = tep_output_string_protected($address['state']);
    if (isset($address['country_id']) && tep_not_null($address['country_id'])) {
      $country = tep_get_country_name($address['country_id']);

      if (isset($address['zone_id']) && tep_not_null($address['zone_id'])) {
        $state = tep_get_zone_code($address['country_id'], $address['zone_id'], $state);
      }
    } elseif (isset($address['country']) && tep_not_null($address['country'])) {
      $country = tep_output_string_protected($address['country']);
    } else {
      $country = '';
    }
    $postcode = tep_output_string_protected($address['postcode']);
    $zip = $postcode;

    if ($html) {
// HTML Mode
      $HR = '<hr>';
      $hr = '<hr>';
      if ( ($boln == '') && ($eoln == "\n") ) { // Values not specified, use rational defaults
        $CR = '<br>';
        $cr = '<br>';
        $eoln = $cr;
      } else { // Use values supplied
        $CR = $eoln . $boln;
        $cr = $CR;
      }
    } else {
// Text Mode
      $CR = $eoln;
      $cr = $CR;
      $HR = '----------------------------------------';
      $hr = '----------------------------------------';
    }

    $statecomma = '';
    $streets = $street;
    if ($suburb != '') $streets = $street . $cr . $suburb;
    if ($country == '') $country = tep_output_string_protected($address['country']);
    if ($state != '') $statecomma = $state . ', ';

    $fmt = $Qformat->value('address_format');
    eval("\$address = \"$fmt\";");

    if ( (ACCOUNT_COMPANY == 'true') && (tep_not_null($company)) ) {
      $address = $company . $cr . $address;
    }

    return $address;
  }


  function tep_get_uprid($prid, $params) {
    $uprid = $prid;
    if ( (is_array($params)) && (!strstr($prid, '{')) ) {
      while (list($option, $value) = each($params)) {
        $uprid = $uprid . '{' . $option . '}' . $value;
      }
    }

    return $uprid;
  }

  function tep_get_prid($uprid) {
    $pieces = explode('{', $uprid);

    return $pieces[0];
  }

  function tep_get_weight_class_title($weight_class_id, $language_id = '') {
    global $osC_Database, $osC_Language;

    if (empty($language_id)) {
      $language_id = $osC_Language->getID();
    }

    $Qweight = $osC_Database->query('select weight_class_title from :table_weight_class where weight_class_id = :weight_class_id and language_id = :language_id');
    $Qweight->bindTable(':table_weight_class', TABLE_WEIGHT_CLASS);
    $Qweight->bindInt(':weight_class_id', $weight_class_id);
    $Qweight->bindInt(':language_id', $language_id);
    $Qweight->execute();

    return $Qweight->value('weight_class_title');
  }

////
// Return the manufacturers URL in the needed language
// TABLES: manufacturers_info
  function tep_get_manufacturer_url($manufacturer_id, $language_id) {
    global $osC_Database;

    $Qmanufacturer = $osC_Database->query('select manufacturers_url from :table_manufacturers_info where manufacturers_id = :manufacturers_id and languages_id = :languages_id');
    $Qmanufacturer->bindTable(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
    $Qmanufacturer->bindInt(':manufacturers_id', $manufacturer_id);
    $Qmanufacturer->bindInt(':languages_id', $language_id);
    $Qmanufacturer->execute();

    return $Qmanufacturer->value('manufacturers_url');
  }

////
// Wrapper for class_exists() function
// This function is not available in all PHP versions so we test it before using it.
  function tep_class_exists($class_name) {
    if (function_exists('class_exists')) {
      return class_exists($class_name);
    } else {
      return true;
    }
  }

////
// Count how many products exist in a category
// TABLES: products, products_to_categories, categories
  function tep_products_in_category_count($categories_id, $include_deactivated = false) {
    global $osC_Database;

    $products_count = 0;

    $Qproducts = $osC_Database->query('select count(*) as total from :table_products p, :table_products_to_categories p2c where p.products_id = p2c.products_id and p2c.categories_id = :categories_id');

    if ($include_deactivated === true) {
      $Qproducts->appendQuery('and p.products_status = 1');
    }

    $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
    $Qproducts->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
    $Qproducts->bindInt(':categories_id', $categories_id);
    $Qproducts->execute();

    $products_count += $Qproducts->valueInt('total');

    $Qchildren = $osC_Database->query('select categories_id from :table_categories where parent_id = :parent_id');
    $Qchildren->bindTable(':table_categories', TABLE_CATEGORIES);
    $Qchildren->bindInt(':parent_id', $categories_id);
    $Qchildren->execute();

    while ($Qchildren->next()) {
      $products_count += tep_products_in_category_count($Qchildren->valueInt('categories_id'), $include_deactivated);
    }

    return $products_count;
  }

////
// Count how many subcategories exist in a category
// TABLES: categories
  function tep_childs_in_category_count($categories_id) {
    global $osC_Database;

    $categories_count = 0;

    $Qcategories = $osC_Database->query('select categories_id from :table_categories where parent_id = :parent_id');
    $Qcategories->bindTable(':table_categories', TABLE_CATEGORIES);
    $Qcategories->bindInt(':parent_id', $categories_id);
    $Qcategories->execute();

    while ($Qcategories->next()) {
      $categories_count++;

      $categories_count += tep_childs_in_category_count($Qcategories->valueInt('categories_id'));
    }

    return $categories_count;
  }

////
// Returns an array with countries
// TABLES: countries
  function tep_get_countries($default = '') {
    global $osC_Database;

    $countries_array = array();

    if (!empty($default)) {
      $countries_array[] = array('id' => '',
                                 'text' => $default);
    }

    $Qcountries = $osC_Database->query('select countries_id, countries_name from :table_countries order by countries_name');
    $Qcountries->bindTable(':table_countries', TABLE_COUNTRIES);
    $Qcountries->execute();

    while ($Qcountries->next()) {
      $countries_array[] = array('id' => $Qcountries->valueInt('countries_id'),
                                 'text' => $Qcountries->value('countries_name'));
    }

    return $countries_array;
  }

////
// return an array with country zones
  function tep_get_country_zones($country_id) {
    global $osC_Database;

    $zones_array = array();

    $Qzones = $osC_Database->query('select zone_id, zone_name from :table_zones where zone_country_id = :zone_country_id order by zone_name');
    $Qzones->bindTable(':table_zones', TABLE_ZONES);
    $Qzones->bindInt(':zone_country_id', $country_id);
    $Qzones->execute();

    while ($Qzones->next()) {
      $zones_array[] = array('id' => $Qzones->valueInt('zone_id'),
                             'text' => $Qzones->value('zone_name'));
    }

    return $zones_array;
  }

  function tep_prepare_country_zones_pull_down($country_id = '') {
// preset the width of the drop-down for Netscape
    $pre = '';
    if ( (!tep_browser_detect('MSIE')) && (tep_browser_detect('Mozilla/4')) ) {
      for ($i=0; $i<45; $i++) $pre .= '&nbsp;';
    }

    $zones = tep_get_country_zones($country_id);

    if (sizeof($zones) > 0) {
      $zones_select = array(array('id' => '', 'text' => PLEASE_SELECT));
      $zones = array_merge($zones_select, $zones);
    } else {
      $zones = array(array('id' => '', 'text' => TYPE_BELOW));
// create dummy options for Netscape to preset the height of the drop-down
      if ( (!tep_browser_detect('MSIE')) && (tep_browser_detect('Mozilla/4')) ) {
        for ($i=0; $i<9; $i++) {
          $zones[] = array('id' => '', 'text' => $pre);
        }
      }
    }

    return $zones;
  }

////
// Get list of address_format_id's
  function tep_get_address_formats() {
    global $osC_Database;

    $address_format_array = array();

    $Qformats = $osC_Database->query('select address_format_id from :table_address_format order by address_format_id');
    $Qformats->bindTable(':table_address_format', TABLE_ADDRESS_FORMAT);
    $Qformats->execute();

    while ($Qformats->next()) {
      $address_format_array[] = array('id' => $Qformats->valueInt('address_format_id'),
                                      'text' => $Qformats->valueInt('address_format_id'));
    }
    return $address_format_array;
  }

////
// Alias function for Store configuration values in the Administration Tool
  function tep_cfg_pull_down_country_list($country_id) {
    return tep_draw_pull_down_menu('configuration_value', tep_get_countries(), $country_id);
  }

  function tep_cfg_pull_down_zone_list($zone_id) {
    return tep_draw_pull_down_menu('configuration_value', tep_get_country_zones(STORE_COUNTRY), $zone_id);
  }

  function tep_cfg_pull_down_tax_classes($tax_class_id, $key = '') {
    global $osC_Database;

    $name = (empty($key)) ? 'configuration_value' : 'configuration[' . $key . ']';

    $tax_class_array = array(array('id' => '0', 'text' => TEXT_NONE));

    $Qclasses = $osC_Database->query('select tax_class_id, tax_class_title from :table_tax_class order by tax_class_title');
    $Qclasses->bindTable(':table_tax_class', TABLE_TAX_CLASS);
    $Qclasses->execute();

    while ($Qclasses->next()) {
      $tax_class_array[] = array('id' => $Qclasses->valueInt('tax_class_id'),
                                 'text' => $Qclasses->value('tax_class_title'));
    }

    return tep_draw_pull_down_menu($name, $tax_class_array, $tax_class_id);
  }

////
// Function to read in text area in admin
 function tep_cfg_textarea($text) {
    return tep_draw_textarea_field('configuration_value', false, 35, 5, $text);
  }

  function tep_cfg_get_zone_name($zone_id) {
    global $osC_Database;

    $Qzone = $osC_Database->query('select zone_name from :table_zones where zone_id = :zone_id');
    $Qzone->bindTable(':table_zones', TABLE_ZONES);
    $Qzone->bindInt(':zone_id', $zone_id);
    $Qzone->execute();

    return $Qzone->value('zone_name');
  }

  function tep_cfg_pull_down_weight_classes($weight_class_id, $key = '') {
    global $osC_Database, $osC_Language;

    $name = (empty($key)) ? 'configuration_value' : 'configuration[' . $key . ']';

    $weight_class_array = array();

    $Qclasses = $osC_Database->query('select weight_class_id, weight_class_title from :table_weight_class where language_id = :language_id order by weight_class_title');
    $Qclasses->bindTable(':table_weight_class', TABLE_WEIGHT_CLASS);
    $Qclasses->bindInt(':language_id', $osC_Language->getID());
    $Qclasses->execute();

    while ($Qclasses->next()) {
      $weight_class_array[] = array('id' => $Qclasses->valueInt('weight_class_id'),
                                    'text' => $Qclasses->value('weight_class_title'));
    }

    return tep_draw_pull_down_menu($name, $weight_class_array, $weight_class_id);
  }

////
// Sets timeout for the current script.
// Cant be used in safe mode.
  function tep_set_time_limit($limit) {
    if (!get_cfg_var('safe_mode')) {
      set_time_limit($limit);
    }
  }

////
// Alias function for Store configuration values in the Administration Tool
  function tep_cfg_select_option($select_array, $key_value, $key = '') {
    $string = '';

    for ($i=0, $n=sizeof($select_array); $i<$n; $i++) {
      $name = ((tep_not_null($key)) ? 'configuration[' . $key . ']' : 'configuration_value');

      $string .= '<br><input type="radio" name="' . $name . '" value="' . $select_array[$i] . '"';

      if ($key_value == $select_array[$i]) $string .= ' CHECKED';

      $string .= '> ' . $select_array[$i];
    }

    return $string;
  }

////
// Alias function for module configuration keys
  function tep_mod_select_option($select_array, $key_name, $key_value) {
    reset($select_array);
    while (list($key, $value) = each($select_array)) {
      if (is_int($key)) $key = $value;
      $string .= '<br><input type="radio" name="configuration[' . $key_name . ']" value="' . $key . '"';
      if ($key_value == $key) $string .= ' CHECKED';
      $string .= '> ' . $value;
    }

    return $string;
  }

////
// Retreive server information
  function osc_get_system_information() {
    if (PHP_VERSION < 4.1) {
      global $_SERVER;
    }

    global $osC_Database;

    $Qdb_date = $osC_Database->query('select now() as datetime');
    $Qdb_uptime = $osC_Database->query('show status like "Uptime"');

    list($system, $host, $kernel) = preg_split('/[\s,]+/', @exec('uname -a'), 5);

    $db_uptime = intval($Qdb_uptime->valueInt('Value') / 3600) . ':' . str_pad(intval(($Qdb_uptime->valueInt('Value') / 60) % 60), 2, '0', STR_PAD_LEFT);

    return array('date' => tep_datetime_short(date('Y-m-d H:i:s')),
                 'system' => $system,
                 'kernel' => $kernel,
                 'host' => $host,
                 'ip' => gethostbyname($host),
                 'uptime' => @exec('uptime'),
                 'http_server' => $_SERVER['SERVER_SOFTWARE'],
                 'php' => PHP_VERSION,
                 'zend' => (function_exists('zend_version') ? zend_version() : ''),
                 'db_server' => DB_SERVER,
                 'db_ip' => gethostbyname(DB_SERVER),
                 'db_version' => 'MySQL ' . (function_exists('mysql_get_server_info') ? mysql_get_server_info() : ''),
                 'db_date' => tep_datetime_short($Qdb_date->value('datetime')),
                 'db_uptime' => $db_uptime);
  }

  function tep_generate_category_path($id, $from = 'category', $categories_array = '', $index = 0) {
    global $osC_Database, $osC_Language;

    if (!is_array($categories_array)) $categories_array = array();

    if ($from == 'product') {
      $Qcategories = $osC_Database->query('select categories_id from :table_products_to_categories where products_id = :products_id');
      $Qcategories->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
      $Qcategories->bindInt(':products_id', $id);
      $Qcategories->execute();

      while ($Qcategories->next()) {
        if ($Qcategories->valueInt('categories_id') == '0') {
          $categories_array[$index][] = array('id' => '0', 'text' => TEXT_TOP);
        } else {
          $Qcategory = $osC_Database->query('select cd.categories_name, c.parent_id from :table_categories c, :table_categories_description cd where c.categories_id = :categories_id and c.categories_id = cd.categories_id and cd.language_id = :language_id');
          $Qcategory->bindTable(':table_categories', TABLE_CATEGORIES);
          $Qcategory->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
          $Qcategory->bindInt(':categories_id', $Qcategories->valueInt('categories_id'));
          $Qcategory->bindInt(':language_id', $osC_Language->getID());
          $Qcategory->execute();

          $categories_array[$index][] = array('id' => $Qcategories->valueInt('categories_id'), 'text' => $Qcategory->value('categories_name'));

          if ($Qcategory->valueInt('parent_id') != '0') {
            $categories_array = tep_generate_category_path($Qcategory->valueInt('parent_id'), 'category', $categories_array, $index);
          }

          $categories_array[$index] = array_reverse($categories_array[$index]);
        }
        $index++;
      }
    } elseif ($from == 'category') {
      $Qcategory = $osC_Database->query('select cd.categories_name, c.parent_id from :table_categories c, :table_categories_description cd where c.categories_id = :categories_id and c.categories_id = cd.categories_id and cd.language_id = :language_id');
      $Qcategory->bindTable(':table_categories', TABLE_CATEGORIES);
      $Qcategory->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
      $Qcategory->bindInt(':categories_id', $id);
      $Qcategory->bindInt(':language_id', $osC_Language->getID());
      $Qcategory->execute();

      $categories_array[$index][] = array('id' => $id, 'text' => $Qcategory->value('categories_name'));

      if ($Qcategory->valueInt('parent_id') != '0') {
        $categories_array = tep_generate_category_path($Qcategory->valueInt('parent_id'), 'category', $categories_array, $index);
      }
    }

    return $categories_array;
  }

  function tep_output_generated_category_path($id, $from = 'category') {
    $calculated_category_path_string = '';
    $calculated_category_path = tep_generate_category_path($id, $from);
    for ($i=0, $n=sizeof($calculated_category_path); $i<$n; $i++) {
      for ($j=0, $k=sizeof($calculated_category_path[$i]); $j<$k; $j++) {
        $calculated_category_path_string .= $calculated_category_path[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
      }
      $calculated_category_path_string = substr($calculated_category_path_string, 0, -16) . '<br>';
    }
    $calculated_category_path_string = substr($calculated_category_path_string, 0, -4);

    if (strlen($calculated_category_path_string) < 1) $calculated_category_path_string = TEXT_TOP;

    return $calculated_category_path_string;
  }

  function tep_get_generated_category_path_ids($id, $from = 'category') {
    $calculated_category_path_string = '';
    $calculated_category_path = tep_generate_category_path($id, $from);
    for ($i=0, $n=sizeof($calculated_category_path); $i<$n; $i++) {
      for ($j=0, $k=sizeof($calculated_category_path[$i]); $j<$k; $j++) {
        $calculated_category_path_string .= $calculated_category_path[$i][$j]['id'] . '_';
      }
      $calculated_category_path_string = substr($calculated_category_path_string, 0, -1) . '<br>';
    }
    $calculated_category_path_string = substr($calculated_category_path_string, 0, -4);

    if (strlen($calculated_category_path_string) < 1) $calculated_category_path_string = TEXT_TOP;

    return $calculated_category_path_string;
  }

  function tep_remove_category($category_id) {
    global $osC_Database;

    $Qimage = $osC_Database->query('select categories_image from :table_categories where categories_id = :categories_id');
    $Qimage->bindTable(':table_categories', TABLE_CATEGORIES);
    $Qimage->bindInt(':categories_id', $category_id);
    $Qimage->execute();

    $osC_Database->startTransaction();

    $Qc = $osC_Database->query('delete from :table_categories where categories_id = :categories_id');
    $Qc->bindTable(':table_categories', TABLE_CATEGORIES);
    $Qc->bindInt(':categories_id', $category_id);
    $Qc->execute();

    if ($osC_Database->isError() === false) {
      $Qcd = $osC_Database->query('delete from :table_categories_description where categories_id = :categories_id');
      $Qcd->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
      $Qcd->bindInt(':categories_id', $category_id);
      $Qcd->execute();

      if ($osC_Database->isError() === false) {
        $Qp2c = $osC_Database->query('delete from :table_products_to_categories where categories_id = :categories_id');
        $Qp2c->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
        $Qp2c->bindInt(':categories_id', $category_id);
        $Qp2c->execute();

        if ($osC_Database->isError() === false) {
          $osC_Database->commitTransaction();

          osC_Cache::clear('categories');
          osC_Cache::clear('category_tree');
          osC_Cache::clear('also_purchased');

          if (tep_not_null($Qimage->value('categories_image'))) {
            $Qcheck = $osC_Database->query('select count(*) as total from :table_categories where categories_image = :categories_image');
            $Qcheck->bindTable(':table_categories', TABLE_CATEGORIES);
            $Qcheck->bindValue(':categories_image', $Qimage->value('categories_image'));
            $Qcheck->execute();

            if ($Qcheck->numberOfRows() === 0) {
              if (file_exists(realpath('../images/' . $Qimage->value('categories_image')))) {
                @unlink(realpath('../images/' . $Qimage->value('categories_image')));
              }
            }
          }
        } else {
          $osC_Database->rollbackTransaction();
        }
      } else {
        $osC_Database->rollbackTransaction();
      }
    } else {
      $osC_Database->rollbackTransaction();
    }
  }

  function tep_remove_product($product_id) {
    global $osC_Database;

    $error = false;

    $Qimage = $osC_Database->query('select products_image from :table_products where products_id = :products_id');
    $Qimage->bindTable(':table_products', TABLE_PRODUCTS);
    $Qimage->bindInt(':products_id', $product_id);
    $Qimage->execute();

    $osC_Database->startTransaction();

    $Qr = $osC_Database->query('delete from :table_reviews where products_id = :products_id');
    $Qr->bindTable(':table_reviews', TABLE_REVIEWS);
    $Qr->bindInt(':products_id', $product_id);
    $Qr->execute();

    if ($osC_Database->isError() === true) {
      $error = true;
    }

    if ($error === false) {
      $Qcba = $osC_Database->query('delete from :table_customers_basket_attributes where products_id = :products_id');
      $Qcba->bindTable(':table_customers_basket_attributes', TABLE_CUSTOMERS_BASKET_ATTRIBUTES);
      $Qcba->bindInt(':products_id', $product_id);
      $Qcba->execute();

      if ($osC_Database->isError() === true) {
        $error = true;
      }
    }

    if ($error === false) {
      $Qcb = $osC_Database->query('delete from :table_customers_basket where products_id = :products_id');
      $Qcb->bindTable(':table_customers_basket', TABLE_CUSTOMERS_BASKET);
      $Qcb->bindInt(':products_id', $product_id);
      $Qcb->execute();

      if ($osC_Database->isError() === true) {
        $error = true;
      }
    }

    if ($error === false) {
      $Qp2c = $osC_Database->query('delete from :table_products_to_categories where products_id = :products_id');
      $Qp2c->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
      $Qp2c->bindInt(':products_id', $product_id);
      $Qp2c->execute();

      if ($osC_Database->isError() === true) {
        $error = true;
      }
    }

    if ($error === false) {
      $Qs = $osC_Database->query('delete from :table_specials where products_id = :products_id');
      $Qs->bindTable(':table_specials', TABLE_SPECIALS);
      $Qs->bindInt(':products_id', $product_id);
      $Qs->execute();

      if ($osC_Database->isError() === true) {
        $error = true;
      }
    }

    if ($error === false) {
      $Qpa = $osC_Database->query('delete from :table_products_attributes where products_id = :products_id');
      $Qpa->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
      $Qpa->bindInt(':products_id', $product_id);
      $Qpa->execute();

      if ($osC_Database->isError() === true) {
        $error = true;
      }
    }

    if ($error === false) {
      $Qpd = $osC_Database->query('delete from :table_products_description where products_id = :products_id');
      $Qpd->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
      $Qpd->bindInt(':products_id', $product_id);
      $Qpd->execute();

      if ($osC_Database->isError() === true) {
        $error = true;
      }
    }

    if ($error === false) {
      $Qp = $osC_Database->query('delete from :table_products where products_id = :products_id');
      $Qp->bindTable(':table_products', TABLE_PRODUCTS);
      $Qp->bindInt(':products_id', $product_id);
      $Qp->execute();

      if ($osC_Database->isError() === true) {
        $error = true;
      }
    }

    if ($error === false) {
      $osC_Database->commitTransaction();

      osC_Cache::clear('categories');
      osC_Cache::clear('category_tree');
      osC_Cache::clear('also_purchased');

      if (tep_not_null($Qimage->value('products_image'))) {
        $Qcheck = $osC_Database->query('select count(*) as total from :table_products where products_image = :products_image');
        $Qcheck->bindTable(':table_products', TABLE_PRODUCTS);
        $Qcheck->bindValue(':products_image', $Qimage->value('products_image'));
        $Qcheck->execute();

        if ($Qcheck->numberOfRows() === 0) {
          if (file_exists(realpath('../images/' . $Qimage->value('products_image')))) {
            @unlink(realpath('../images/' . $Qimage->value('products_image')));
          }
        }
      }
    } else {
      $osC_Database->rollbackTransaction();
    }
  }

  function tep_remove_order($order_id, $restock = false) {
    global $osC_Database;

    $error = false;

    $osC_Database->startTransaction();

    if ($restock == 'on') {
      $Qproducts = $osC_Database->query('select products_id, products_quantity from :table_orders_products where orders_id = :orders_id');
      $Qproducts->bindTable(':table_orders_products', TABLE_ORDERS_PRODUCTS);
      $Qproducts->bindInt(':orders_id', $order_id);
      $Qproducts->execute();

      while ($Qproducts->next()) {
        $Qupdate = $osC_Database->query('update :table_products set products_quantity = products_quantity + :products_quantity, products_ordered = products_ordered - :products_ordered where products_id = :products_id');
        $Qupdate->bindTable(':table_products', TABLE_PRODUCTS);
        $Qupdate->bindInt(':products_quantity', $Qproducts->valueInt('products_quantity'));
        $Qupdate->bindInt(':products_ordered', $Qproducts->valueInt('products_quantity'));
        $Qupdate->bindInt(':products_id', $Qproducts->valueInt('products_id'));
        $Qupdate->execute();

        if ($osC_Database->isError() === true) {
          $error = true;
          break;
        }

        $Qcheck = $osC_Database->query('select products_quantity from :table_products where products_id = :products_id and products_Status = 0');
        $Qcheck->bindTable(':table_products', TABLE_PRODUCTS);
        $Qcheck->bindInt(':products_id', $Qproducts->valueInt('products_id'));
        $Qcheck->execute();

        if (($Qcheck->numberOfRows() === 1) && ($Qcheck->valueInt('products_quantity') > 0)) {
          $Qstatus = $osC_Database->query('update :table_products set products_status = 1 where products_id = :products_id');
          $Qstatus->bindTable(':table_products', TABLE_PRODUCTS);
          $Qstatus->bindInt(':products_id', $Qproducts->valueInt('products_id'));
          $Qstatus->execute();

          if ($osC_Database->isError() === true) {
            $error = true;
            break;
          }
        }
      }
    }

    if ($error === false) {
      $Qopa = $osC_Database->query('delete from :table_orders_products_attributes where orders_id = :orders_id');
      $Qopa->bindTable(':table_orders_products_attributes', TABLE_ORDERS_PRODUCTS_ATTRIBUTES);
      $Qopa->bindInt(':orders_id', $order_id);
      $Qopa->execute();

      if ($osC_Database->isError() === true) {
        $error = true;
      }
    }

    if ($error === false) {
      $Qop = $osC_Database->query('delete from :table_orders_products where orders_id = :orders_id');
      $Qop->bindTable(':table_orders_products', TABLE_ORDERS_PRODUCTS);
      $Qop->bindInt(':orders_id', $order_id);
      $Qop->execute();

      if ($osC_Database->isError() === true) {
        $error = true;
      }
    }

    if ($error === false) {
      $Qosh = $osC_Database->query('delete from :table_orders_status_history where orders_id = :orders_id');
      $Qosh->bindTable(':table_orders_status_history', TABLE_ORDERS_status_history);
      $Qosh->bindInt(':orders_id', $order_id);
      $Qosh->execute();

      if ($osC_Database->isError() === true) {
        $error = true;
      }
    }

    if ($error === false) {
      $Qot = $osC_Database->query('delete from :table_orders_total where orders_id = :orders_id');
      $Qot->bindTable(':table_orders_total', TABLE_ORDERS_TOTAL);
      $Qot->bindInt(':orders_id', $order_id);
      $Qot->execute();

      if ($osC_Database->isError() === true) {
        $error = true;
      }
    }

    if ($error === false) {
      $Qo = $osC_Database->query('delete from :table_orders where orders_id = :orders_id');
      $Qo->bindTable(':table_orders', TABLE_ORDERS);
      $Qo->bindInt(':orders_id', $order_id);
      $Qo->execute();

      if ($osC_Database->isError() === true) {
        $error = true;
      }
    }

    if ($error === false) {
      $osC_Database->commitTransaction();
    } else {
      $osC_Database->rollbackTransaction();
    }
  }

  function tep_get_file_permissions($mode) {
// determine type
    if ( ($mode & 0xC000) == 0xC000) { // unix domain socket
      $type = 's';
    } elseif ( ($mode & 0x4000) == 0x4000) { // directory
      $type = 'd';
    } elseif ( ($mode & 0xA000) == 0xA000) { // symbolic link
      $type = 'l';
    } elseif ( ($mode & 0x8000) == 0x8000) { // regular file
      $type = '-';
    } elseif ( ($mode & 0x6000) == 0x6000) { //bBlock special file
      $type = 'b';
    } elseif ( ($mode & 0x2000) == 0x2000) { // character special file
      $type = 'c';
    } elseif ( ($mode & 0x1000) == 0x1000) { // named pipe
      $type = 'p';
    } else { // unknown
      $type = '?';
    }

// determine permissions
    $owner['read']    = ($mode & 00400) ? 'r' : '-';
    $owner['write']   = ($mode & 00200) ? 'w' : '-';
    $owner['execute'] = ($mode & 00100) ? 'x' : '-';
    $group['read']    = ($mode & 00040) ? 'r' : '-';
    $group['write']   = ($mode & 00020) ? 'w' : '-';
    $group['execute'] = ($mode & 00010) ? 'x' : '-';
    $world['read']    = ($mode & 00004) ? 'r' : '-';
    $world['write']   = ($mode & 00002) ? 'w' : '-';
    $world['execute'] = ($mode & 00001) ? 'x' : '-';

// adjust for SUID, SGID and sticky bit
    if ($mode & 0x800 ) $owner['execute'] = ($owner['execute'] == 'x') ? 's' : 'S';
    if ($mode & 0x400 ) $group['execute'] = ($group['execute'] == 'x') ? 's' : 'S';
    if ($mode & 0x200 ) $world['execute'] = ($world['execute'] == 'x') ? 't' : 'T';

    return $type .
           $owner['read'] . $owner['write'] . $owner['execute'] .
           $group['read'] . $group['write'] . $group['execute'] .
           $world['read'] . $world['write'] . $world['execute'];
  }

  function tep_remove($source) {
    global $osC_MessageStack, $tep_remove_error;

    if (isset($tep_remove_error)) $tep_remove_error = false;

    if (is_dir($source)) {
      $dir = dir($source);
      while ($file = $dir->read()) {
        if ( ($file != '.') && ($file != '..') ) {
          if (is_writeable($source . '/' . $file)) {
            tep_remove($source . '/' . $file);
          } else {
            $osC_MessageStack->add('header', sprintf(ERROR_FILE_NOT_REMOVEABLE, $source . '/' . $file), 'error');
            $tep_remove_error = true;
          }
        }
      }
      $dir->close();

      if (is_writeable($source)) {
        rmdir($source);
      } else {
        $osC_MessageStack->add('header', sprintf(ERROR_DIRECTORY_NOT_REMOVEABLE, $source), 'error');
        $tep_remove_error = true;
      }
    } else {
      if (is_writeable($source)) {
        unlink($source);
      } else {
        $osC_MessageStack->add('header', sprintf(ERROR_FILE_NOT_REMOVEABLE, $source), 'error');
        $tep_remove_error = true;
      }
    }
  }

////
// Output the tax percentage with optional padded decimals
  function tep_display_tax_value($value, $padding = TAX_DECIMAL_PLACES) {
    if (strpos($value, '.')) {
      $loop = true;
      while ($loop) {
        if (substr($value, -1) == '0') {
          $value = substr($value, 0, -1);
        } else {
          $loop = false;
          if (substr($value, -1) == '.') {
            $value = substr($value, 0, -1);
          }
        }
      }
    }

    if ($padding > 0) {
      if ($decimal_pos = strpos($value, '.')) {
        $decimals = strlen(substr($value, ($decimal_pos+1)));
        for ($i=$decimals; $i<$padding; $i++) {
          $value .= '0';
        }
      } else {
        $value .= '.';
        for ($i=0; $i<$padding; $i++) {
          $value .= '0';
        }
      }
    }

    return $value;
  }

  function tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address) {
    if (SEND_EMAILS != 'true') return false;

    // Instantiate a new mail object
    $message = new email(array('X-Mailer: osCommerce'));

    // Build the text version
    $text = strip_tags($email_text);
    if (EMAIL_USE_HTML == 'true') {
      $message->add_html($email_text, $text);
    } else {
      $message->add_text($text);
    }

    // Send message
    $message->build_message();
    $message->send($to_name, $to_email_address, $from_email_name, $from_email_address, $email_subject);
  }

  function tep_get_tax_class_title($tax_class_id) {
    global $osC_Database;

    if ($tax_class_id == '0') {
      return TEXT_NONE;
    }

    $Qclass = $osC_Database->query('select tax_class_title from :table_tax_class where tax_class_id = :tax_class_id');
    $Qclass->bindTable(':table_tax_class', TABLE_TAX_CLASS);
    $Qclass->bindInt(':tax_class_id', $tax_class_id);
    $Qclass->execute();

    return $Qclass->value('tax_class_title');
  }

  function tep_dynamic_image_extension() {
    static $extension;

    if (!isset($extension)) {
      if (function_exists('imagetypes')) {
        if (imagetypes() & IMG_PNG) {
          $extension = 'png';
        } elseif (imagetypes() & IMG_JPG) {
          $extension = 'jpeg';
        } elseif (imagetypes() & IMG_GIF) {
          $extension = 'gif';
        }
      } elseif (function_exists('imagepng')) {
        $extension = 'png';
      } elseif (function_exists('imagejpeg')) {
        $extension = 'jpeg';
      } elseif (function_exists('imagegif')) {
        $extension = 'gif';
      }
    }

    return $extension;
  }

////
// Wrapper function for round() for php3 compatibility
  function tep_round($value, $precision) {
    if (PHP_VERSION < 4) {
      $exp = pow(10, $precision);
      return round($value * $exp) / $exp;
    } else {
      return round($value, $precision);
    }
  }

////
// Add tax to a products price
  function tep_add_tax($price, $tax) {
    global $osC_Currencies;

    if (DISPLAY_PRICE_WITH_TAX == 'true') {
      return tep_round($price, $osC_Currencies->currencies[DEFAULT_CURRENCY]['decimal_places']) + tep_calculate_tax($price, $tax);
    } else {
      return tep_round($price, $osC_Currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
    }
  }

// Calculates Tax rounding the result
  function tep_calculate_tax($price, $tax) {
    global $osC_Currencies;

    return tep_round($price * $tax / 100, $osC_Currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
  }

////
// Returns the tax rate for a zone / class
// TABLES: tax_rates, zones_to_geo_zones
  function tep_get_tax_rate($class_id, $country_id = -1, $zone_id = -1) {
    global $customer_zone_id, $customer_country_id, $osC_Database, $osC_Session;

    if ( ($country_id == -1) && ($zone_id == -1) ) {
      if ($osC_Session->exists('customer_id')) {
        $country_id = $customer_country_id;
        $zone_id = $customer_zone_id;
      } else {
        $country_id = STORE_COUNTRY;
        $zone_id = STORE_ZONE;
      }
    }

    $tax_multiplier = 0;

    $Qrate = $osC_Database->query('select sum(tax_rate) as tax_rate from :table_tax_rates tr left join :table_zones_to_geo_zones za on (tr.tax_zone_id = za.geo_zone_id) left join :table_geo_zones tz on (tz.geo_zone_id = tr.tax_zone_id) where (za.zone_country_id is null or za.zone_country_id = 0 or za.zone_country_id = :zone_country_id) and (za.zone_id is null or za.zone_id = 0 or za.zone_id = :zone_id) and tr.tax_class_id = :tax_class_id group by tr.tax_priority');
    $Qrate->bindTable(':table_tax_rates', TABLE_TAX_RATES);
    $Qrate->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
    $Qrate->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
    $Qrate->bindInt(':zone_country_id', $country_id);
    $Qrate->bindInt(':zone_id', $zone_id);
    $Qrate->bindInt(':tax_class_id', $class_id);
    $Qrate->execute();

    while ($Qrate->next()) {
      $tax_multiplier += $Qrate->value('tax_rate');
    }

    return $tax_multiplier;
  }

////
// Returns the tax rate for a tax class
// TABLES: tax_rates
  function tep_get_tax_rate_value($class_id) {
    global $osC_Database;

    $tax_multiplier = 0;

    $Qrate = $osC_Database->query('select sum(tax_rate) as tax_rate from :table_tax_rates where tax_class_id = :tax_class_id group by tax_priority');
    $Qrate->bindTable(':table_tax_rates', TABLE_TAX_RATES);
    $Qrate->bindInt(':tax_class_id', $class_id);
    $Qrate->execute();

    while ($Qrate->next()) {
      $tax_multiplier += $Qrate->value('tax_rate');
    }

    return $tax_multiplier;
  }

  function tep_call_function($function, $parameter, $object = '') {
    if ($object == '') {
      return call_user_func($function, $parameter);
    } elseif (PHP_VERSION < 4) {
      return call_user_method($function, $object, $parameter);
    } else {
      return call_user_func(array($object, $function), $parameter);
    }
  }

  function tep_get_zone_class_title($zone_class_id) {
    global $osC_Database;

    if ($zone_class_id == '0') {
      return TEXT_NONE;
    }

    $Qclass = $osC_Database->query('select geo_zone_name from :table_geo_zones where geo_zone_id = :geo_zone_id');
    $Qclass->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
    $Qclass->bindInt(':geo_zone_id', $zone_class_id);
    $Qclass->execute();

    return $Qclass->value('geo_zone_name');
  }

  function tep_cfg_pull_down_zone_classes($zone_class_id, $key = '') {
    global $osC_Database;

    $name = (empty($key)) ? 'configuration_value' : 'configuration[' . $key . ']';

    $zone_class_array = array(array('id' => '0', 'text' => TEXT_NONE));

    $Qzones = $osC_Database->query('select geo_zone_id, geo_zone_name from :table_geo_zones order by geo_zone_name');
    $Qzones->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
    $Qzones->execute();

    while ($Qzones->next()) {
      $zone_class_array[] = array('id' => $Qzones->valueInt('geo_zone_id'),
                                  'text' => $Qzones->value('geo_zone_name'));
    }

    return tep_draw_pull_down_menu($name, $zone_class_array, $zone_class_id);
  }

  function tep_cfg_pull_down_order_statuses($order_status_id, $key = '') {
    global $osC_Database, $osC_Language;

    $name = (empty($key)) ? 'configuration_value' : 'configuration[' . $key . ']';

    $statuses_array = array(array('id' => '0', 'text' => TEXT_DEFAULT));

    $Qstatuses = $osC_Database->query('select orders_status_id, orders_status_name from :table_orders_status where language_id = :language_id order by orders_status_name');
    $Qstatuses->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
    $Qstatuses->bindInt(':language_id', $osC_Language->getID());
    $Qstatuses->execute();

    while ($Qstatuses->next()) {
      $statuses_array[] = array('id' => $Qstatuses->valueInt('orders_status_id'),
                                'text' => $Qstatuses->value('orders_status_name'));
    }

    return tep_draw_pull_down_menu($name, $statuses_array, $order_status_id);
  }

  function tep_get_order_status_name($order_status_id, $language_id = '') {
    global $osC_Database, $osC_Language;

    if ($order_status_id < 1) {
      return TEXT_DEFAULT;
    }

    if (!is_numeric($language_id)) {
      $language_id = $osC_Language->getID();
    }

    $Qstatus = $osC_Database->query('select orders_status_name from :table_orders_status where orders_status_id = :orders_status_id and language_id = :language_id');
    $Qstatus->bindTable(':table_orders_status', TABLE_ORDERS_STATUS);
    $Qstatus->bindInt(':orders_status_id', $order_status_id);
    $Qstatus->bindInt(':language_id', $language_id);
    $Qstatus->execute();

    return $Qstatus->value('orders_status_name');
  }

////
// Return a random value
  function tep_rand($min = null, $max = null) {
    static $seeded;

    if (!$seeded) {
      mt_srand((double)microtime()*1000000);
      $seeded = true;
    }

    if (isset($min) && isset($max)) {
      if ($min >= $max) {
        return $min;
      } else {
        return mt_rand($min, $max);
      }
    } else {
      return mt_rand();
    }
  }

// nl2br() prior PHP 4.2.0 did not convert linefeeds on all OSs (it only converted \n)
  function tep_convert_linefeeds($from, $to, $string) {
    if ((PHP_VERSION < "4.0.5") && is_array($from)) {
      return ereg_replace('(' . implode('|', $from) . ')', $to, $string);
    } else {
      return str_replace($from, $to, $string);
    }
  }

  function tep_string_to_int($string) {
    return (int)$string;
  }

////
// Parse and secure the cPath parameter values
  function tep_parse_category_path($cPath) {
// make sure the category IDs are integers
    $cPath_array = array_map('tep_string_to_int', explode('_', $cPath));

// make sure no duplicate category IDs exist which could lock the server in a loop
    $tmp_array = array();
    $n = sizeof($cPath_array);
    for ($i=0; $i<$n; $i++) {
      if (!in_array($cPath_array[$i], $tmp_array)) {
        $tmp_array[] = $cPath_array[$i];
      }
    }

    return $tmp_array;
  }

  function tep_create_random_value($length, $type = 'mixed') {
    if ( ($type != 'mixed') && ($type != 'chars') && ($type != 'digits')) return false;

    $rand_value = '';
    while (strlen($rand_value) < $length) {
      if ($type == 'digits') {
        $char = tep_rand(0,9);
      } else {
        $char = chr(tep_rand(0,255));
      }
      if ($type == 'mixed') {
        if (eregi('^[a-z0-9]$', $char)) $rand_value .= $char;
      } elseif ($type == 'chars') {
        if (eregi('^[a-z]$', $char)) $rand_value .= $char;
      } elseif ($type == 'digits') {
        if (ereg('^[0-9]$', $char)) $rand_value .= $char;
      }
    }

    return $rand_value;
  }

  function tep_cfg_display_boolean($boolean) {
    if ($boolean > -1) {
      return 'True';
    } else {
      return 'False';
    }
  }

  function tep_array_to_string($array, $exclude = '', $equals = '=', $separator = '&') {
    if (!is_array($exclude)) $exclude = array();

    $get_string = '';
    if (sizeof($array) > 0) {
      while (list($key, $value) = each($array)) {
        if ( (!in_array($key, $exclude)) && ($key != 'x') && ($key != 'y') ) {
          $get_string .= $key . $equals . $value . $separator;
        }
      }
      $remove_chars = strlen($separator);
      $get_string = substr($get_string, 0, -$remove_chars);
    }

    return $get_string;
  }

  function tep_get_serialized_variable(&$serialization_data, $variable_name, $variable_type = 'string') {
    $serialized_variable = '';

    switch ($variable_type) {
      case 'string':
        $start_position = strpos($serialization_data, $variable_name . '|s');

        $serialized_variable = substr($serialization_data, strpos($serialization_data, '|', $start_position) + 1, strpos($serialization_data, '|', $start_position) - 1);
        break;
      case 'array':
      case 'object':
        if ($variable_type == 'array') {
          $start_position = strpos($serialization_data, $variable_name . '|a');
        } else {
          $start_position = strpos($serialization_data, $variable_name . '|O');
        }

        $tag = 0;

        for ($i=$start_position, $n=sizeof($serialization_data); $i<$n; $i++) {
          if ($serialization_data[$i] == '{') {
            $tag++;
          } elseif ($serialization_data[$i] == '}') {
            $tag--;
          } elseif ($tag < 1) {
            break;
          }
        }

        $serialized_variable = substr($serialization_data, strpos($serialization_data, '|', $start_position) + 1, $i - strpos($serialization_data, '|', $start_position) - 1);
        break;
    }

    return $serialized_variable;
  }

  function osc_sanitize_multidimensional_array($array, $key_name = '') {
    static $new_array = array();

    foreach ($array as $key => $value) {
      $array_key = (empty($key_name) ? $key : $key_name . '[' . $key . ']');

      if (is_array($value)) {
        osc_sanitize_multidimensional_array($value, $array_key);
      } else {
        $new_array[$array_key] = $value;
      }
    }

    return $new_array;
  }

  function tep_setcookie($name, $value = '', $expire = 0, $path = false, $domain = false, $secure = false) {
    global $request_type;

    if ($path === false) {
      $path = ($request_type == 'NONSSL') ? HTTP_COOKIE_PATH : HTTPS_COOKIE_PATH;
    }

    if ($domain === false) {
      $domain = ($request_type == 'NONSSL') ? HTTP_COOKIE_DOMAIN : HTTPS_COOKIE_DOMAIN;
    }

    return setcookie($name, $value, $expire, $path, $domain, $secure);
  }
?>

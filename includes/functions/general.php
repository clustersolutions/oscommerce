<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

////
// Redirect to another page or site
  function tep_redirect($url) {
    global $osC_Services;

    if ( (strpos($url, "\n") !== false) || (strpos($url, "\r") !== false) ) {
      $url = osc_href_link(FILENAME_DEFAULT, null, 'NONSSL', false);
    }

    if (strpos($url, '&amp;') !== false) {
      $url = str_replace('&amp;', '&', $url);
    }

    header('Location: ' . $url);

    $osC_Services->stopServices();

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
    $string = ereg_replace(' +', ' ', trim($string));

    return preg_replace("/[<>]/", '_', $string);
  }

////
// Return a product's name
// TABLES: products
  function tep_get_products_name($product_id, $language_id = '') {
    global $osC_Database, $osC_Language;

    if (empty($language_id) || !is_numeric($language_id)) {
      $language_id = $osC_Language->getID();
    }

    $Qproduct = $osC_Database->query('select products_name from :table_products_description where products_id = :products_id and language_id = :language_id');
    $Qproduct->bindRaw(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qproduct->bindInt(':products_id', $product_id);
    $Qproduct->bindInt(':language_id', $language_id);
    $Qproduct->execute();

    $products_name = $Qproduct->value('products_name');

    $Qproduct->freeResult();

    return $products_name;
  }

////
// Return a product's stock
// TABLES: products
  function tep_get_products_stock($products_id) {
    global $osC_Database;

    $products_id = tep_get_prid($products_id);

    $Qstock = $osC_Database->query('select products_quantity from :table_products where products_id = :products_id');
    $Qstock->bindTable(':table_products', TABLE_PRODUCTS);
    $Qstock->bindInt(':products_id', $products_id);
    $Qstock->execute();

    return $Qstock->valueInt('products_quantity');
  }

////
// Check if the required stock is available
// If insufficent stock is available return an out of stock message
  function tep_check_stock($products_id, $products_quantity) {
    $stock_left = tep_get_products_stock($products_id) - $products_quantity;
    $out_of_stock = '';

    if ($stock_left < 0) {
      $out_of_stock = '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>';
    }

    return $out_of_stock;
  }

////
// Break a word in a string if it is longer than a specified length ($len)
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

////
// Return all HTTP GET variables, except those passed as a parameter
  function tep_get_all_get_params($exclude_array = '') {
    global $osC_Session;

    if (!is_array($exclude_array)) $exclude_array = array();

    $get_url = '';
    if (is_array($_GET) && (sizeof($_GET) > 0)) {
      reset($_GET);
      while (list($key, $value) = each($_GET)) {
        if ( ($key != $osC_Session->getName()) && ($key != 'error') && (!in_array($key, $exclude_array)) && ($key != 'x') && ($key != 'y') ) {
          $get_url .= $key . (empty($value) ? '&' : '=' . rawurlencode($value) . '&');
        }
      }
    }

    return $get_url;
  }

////
// Returns an array with countries
// TABLES: countries
  function tep_get_countries($countries_id = '', $with_iso_codes = false) {
    global $osC_Database;

    $countries_array = array();

    if (tep_not_null($countries_id)) {
      if ($with_iso_codes == true) {
        $Qcountries = $osC_Database->query('select countries_name, countries_iso_code_2, countries_iso_code_3 from :table_countries where countries_id = :countries_id');
        $Qcountries->bindTable(':table_countries', TABLE_COUNTRIES);
        $Qcountries->bindInt(':countries_id', $countries_id);
        $Qcountries->execute();

        $countries_array = $Qcountries->toArray();
      } else {
        $Qcountries = $osC_Database->query('select countries_name from :table_countries where countries_id = :countries_id');
        $Qcountries->bindTable(':table_countries', TABLE_COUNTRIES);
        $Qcountries->bindInt(':countries_id', $countries_id);
        $Qcountries->execute();

        $countries_array = $Qcountries->toArray();
      }
    } else {
      $Qcountries = $osC_Database->query('select countries_id, countries_name from :table_countries order by countries_name');
      $Qcountries->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qcountries->execute();

      while ($Qcountries->next()) {
        $countries_array[] = $Qcountries->toArray();
      }
    }

    return $countries_array;
  }

////
// Alias function to tep_get_countries, which also returns the countries iso codes
  function tep_get_countries_with_iso_codes($countries_id) {
    return tep_get_countries($countries_id, true);
  }

////
// Generate a path to categories
  function tep_get_path($current_category_id = '') {
    global $osC_Database, $cPath_array;

    if (tep_not_null($current_category_id)) {
      $cp_size = sizeof($cPath_array);

      if ($cp_size == 0) {
        $cPath_new = $current_category_id;
      } else {
        $cPath_new = '';

        $Qlast_category = $osC_Database->query('select parent_id from :table_categories where categories_id = :categories_id');
        $Qlast_category->bindTable(':table_categories', TABLE_CATEGORIES);
        $Qlast_category->bindInt(':categories_id', $cPath_array[($cp_size-1)]);
        $Qlast_category->execute();

        $Qcurrent_category = $osC_Database->query('select parent_id from :table_categories where categories_id = :categories_id');
        $Qcurrent_category->bindTable(':table_categories', TABLE_CATEGORIES);
        $Qcurrent_category->bindInt(':categories_id', $current_category_id);
        $Qcurrent_category->execute();

        if ($Qlast_category->valueInt('parent_id') == $Qcurrent_category->valueInt('parent_id')) {
          for ($i=0; $i<($cp_size-1); $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        } else {
          for ($i=0; $i<$cp_size; $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        }
        $cPath_new .= '_' . $current_category_id;

        if (substr($cPath_new, 0, 1) == '_') {
          $cPath_new = substr($cPath_new, 1);
        }
      }
    } else {
      $cPath_new = implode('_', $cPath_array);
    }

    return 'cPath=' . $cPath_new;
  }

////
// Returns the clients browser
  function tep_browser_detect($component) {
    return stristr($_SERVER['HTTP_USER_AGENT'], $component);
  }

////
// Alias function to tep_get_countries()
  function tep_get_country_name($country_id) {
    $country_array = tep_get_countries($country_id);

    return $country_array['countries_name'];
  }

////
// Returns the zone (State/Province) name
// TABLES: zones
  function tep_get_zone_name($country_id, $zone_id, $default_zone) {
    global $osC_Database;

    $Qzone = $osC_Database->query('select zone_name from :table_zones where zone_country_id = :zone_country_id and zone_id = :zone_id');
    $Qzone->bindTable(':table_zones', TABLE_ZONES);
    $Qzone->bindInt(':zone_country_id', $country_id);
    $Qzone->bindInt(':zone_id', $zone_id);
    $Qzone->execute();

    if ($Qzone->numberOfRows()) {
      return $Qzone->value('zone_name');
    } else {
      return $default_zone;
    }
  }

////
// Returns the zone (State/Province) code
// TABLES: zones
  function tep_get_zone_code($country_id, $zone_id, $default_zone) {
    global $osC_Database;

    $Qzone = $osC_Database->query('select zone_code from :table_zones where zone_country_id = :zone_country_id and zone_id = :zone_id');
    $Qzone->bindTable(':table_zones', TABLE_ZONES);
    $Qzone->bindInt(':zone_country_id', $country_id);
    $Qzone->bindInt(':zone_id', $zone_id);
    $Qzone->execute();

    if ($Qzone->numberOfRows()) {
      return $Qzone->value('zone_code');
    } else {
      return $default_zone;
    }
  }

////
// Wrapper function for round()
  function tep_round($number, $precision) {
    if (strpos($number, '.') && (strlen(substr($number, strpos($number, '.')+1)) > $precision)) {
      $number = substr($number, 0, strpos($number, '.') + 1 + $precision + 1);

      if (substr($number, -1) >= 5) {
        if ($precision > 1) {
          $number = substr($number, 0, -1) + ('0.' . str_repeat(0, $precision-1) . '1');
        } elseif ($precision == 1) {
          $number = substr($number, 0, -1) + 0.1;
        } else {
          $number = substr($number, 0, -1) + 1;
        }
      } else {
        $number = substr($number, 0, -1);
      }
    }

    return $number;
  }

////
// Add tax to a products price
  function tep_add_tax($price, $tax) {
    global $osC_Currencies;

    if ( (DISPLAY_PRICE_WITH_TAX == '1') && ($tax > 0) ) {
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
// Returns the address_format_id for the given country
// TABLES: countries;
  function tep_get_address_format_id($country_id) {
    global $osC_Database;

    $Qaddress_format = $osC_Database->query('select address_format_id from :table_countries where countries_id = :countries_id');
    $Qaddress_format->bindTable(':table_countries', TABLE_COUNTRIES);
    $Qaddress_format->bindInt(':countries_id', $country_id);
    $Qaddress_format->execute();

    if ($Qaddress_format->numberOfRows()) {
      return $Qaddress_format->valueInt('address_format_id');
    } else {
      return '1';
    }
  }

////
// Return a formatted address
// TABLES: address_format
  function tep_address_format($address_format_id, $address, $html, $boln, $eoln) {
    global $osC_Database;

    $Qaddress_format = $osC_Database->query('select address_format from :table_address_format where address_format_id = :address_format_id');
    $Qaddress_format->bindTable(':table_address_format', TABLE_ADDRESS_FORMAT);
    $Qaddress_format->bindInt(':address_format_id', $address_format_id);
    $Qaddress_format->execute();

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
        $CR = '<br />';
        $cr = '<br />';
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

    $fmt = $Qaddress_format->value('address_format');
    eval("\$address = \"$fmt\";");

    if ( (ACCOUNT_COMPANY == 'true') && (tep_not_null($company)) ) {
      $address = $company . $cr . $address;
    }

    return $address;
  }

////
// Return a formatted address
// TABLES: customers, address_book
  function tep_address_label($customers_id, $address_id = 1, $html = false, $boln = '', $eoln = "\n") {
    global $osC_Database;

    $Qaddress = $osC_Database->query('select entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from :table_address_book where customers_id = :customers_id and address_book_id = :address_book_id');
    $Qaddress->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
    $Qaddress->bindInt(':customers_id', $customers_id);
    $Qaddress->bindInt(':address_book_id', $address_id);
    $Qaddress->execute();

    $format_id = tep_get_address_format_id($Qaddress->valueInt('country_id'));

    return tep_address_format($format_id, $Qaddress->toArray(), $html, $boln, $eoln);
  }

  function tep_row_number_format($number) {
    if ( ($number < 10) && (substr($number, 0, 1) != '0') ) $number = '0' . $number;

    return $number;
  }

  function tep_get_categories($categories_array = '', $parent_id = '0', $indent = '') {
    global $osC_Database, $osC_Language;

    if (!is_array($categories_array)) $categories_array = array();

    $Qcategories = $osC_Database->query('select c.categories_id, cd.categories_name from :table_categories c, :table_categories_description cd where parent_id = :parent_id and c.categories_id = cd.categories_id and cd.language_id = :language_id order by sort_order, cd.categories_name');
    $Qcategories->bindTable(':table_categories', TABLE_CATEGORIES);
    $Qcategories->bindTable(':table_categories_description', TABLE_CATEGORIES_DESCRIPTION);
    $Qcategories->bindInt(':parent_id', $parent_id);
    $Qcategories->bindInt(':language_id', $osC_Language->getID());
    $Qcategories->execute();

    while ($Qcategories->next()) {
      $categories_array[] = array('id' => $Qcategories->valueInt('categories_id'),
                                  'text' => $indent . $Qcategories->value('categories_name'));

      if ($Qcategories->valueInt('categories_id') != $parent_id) {
        $categories_array = tep_get_categories($categories_array, $Qcategories->valueInt('categories_id'), $indent . '&nbsp;&nbsp;');
      }
    }

    return $categories_array;
  }

  function tep_get_manufacturers($manufacturers_array = '') {
    global $osC_Database;

    if (!is_array($manufacturers_array)) $manufacturers_array = array();

    $Qmanufacturers = $osC_Database->query('select manufacturers_id, manufacturers_name from :table_manufacturers order by manufacturers_name');
    $Qmanufacturers->bindTable(':table_manufacturers', TABLE_MANUFACTURERS);
    $Qmanufacturers->execute();

    while ($Qmanufacturers->next()) {
      $manufacturers_array[] = array('id' => $Qmanufacturers->valueInt('manufacturers_id'), 'text' => $Qmanufacturers->value('manufacturers_name'));
    }

    return $manufacturers_array;
  }

////
// Return all subcategory IDs
// TABLES: categories
  function tep_get_subcategories(&$subcategories_array, $parent_id = 0) {
    global $osC_Database;

    $Qsubcategories = $osC_Database->query('select categories_id from :table_categories where parent_id = :parent_id');
    $Qsubcategories->bindTable(':table_categories', TABLE_CATEGORIES);
    $Qsubcategories->bindInt(':parent_id', $parent_id);
    $Qsubcategories->execute();

    while ($Qsubcategories->next()) {
      $subcategories_array[sizeof($subcategories_array)] = $Qsubcategories->valueInt('categories_id');

      if ($Qsubcategories->valueInt('categories_id') != $parent_id) {
        tep_get_subcategories($subcategories_array, $Qsubcategories->valueInt('categories_id'));
      }
    }
  }

////
// Parse search string into indivual objects
  function tep_parse_search_string($search_str = '', &$objects) {
    $search_str = trim(strtolower($search_str));

// Break up $search_str on whitespace; quoted string will be reconstructed later
    $pieces = split('[[:space:]]+', $search_str);
    $objects = array();
    $tmpstring = '';
    $flag = '';

    for ($k=0; $k<count($pieces); $k++) {
      while (substr($pieces[$k], 0, 1) == '(') {
        $objects[] = '(';
        if (strlen($pieces[$k]) > 1) {
          $pieces[$k] = substr($pieces[$k], 1);
        } else {
          $pieces[$k] = '';
        }
      }

      $post_objects = array();

      while (substr($pieces[$k], -1) == ')')  {
        $post_objects[] = ')';
        if (strlen($pieces[$k]) > 1) {
          $pieces[$k] = substr($pieces[$k], 0, -1);
        } else {
          $pieces[$k] = '';
        }
      }

// Check individual words

      if ( (substr($pieces[$k], -1) != '"') && (substr($pieces[$k], 0, 1) != '"') ) {
        $objects[] = trim($pieces[$k]);

        for ($j=0; $j<count($post_objects); $j++) {
          $objects[] = $post_objects[$j];
        }
      } else {
/* This means that the $piece is either the beginning or the end of a string.
   So, we'll slurp up the $pieces and stick them together until we get to the
   end of the string or run out of pieces.
*/

// Add this word to the $tmpstring, starting the $tmpstring
        $tmpstring = trim(ereg_replace('"', ' ', $pieces[$k]));

// Check for one possible exception to the rule. That there is a single quoted word.
        if (substr($pieces[$k], -1 ) == '"') {
// Turn the flag off for future iterations
          $flag = 'off';

          $objects[] = trim($pieces[$k]);

          for ($j=0; $j<count($post_objects); $j++) {
            $objects[] = $post_objects[$j];
          }

          unset($tmpstring);

// Stop looking for the end of the string and move onto the next word.
          continue;
        }

// Otherwise, turn on the flag to indicate no quotes have been found attached to this word in the string.
        $flag = 'on';

// Move on to the next word
        $k++;

// Keep reading until the end of the string as long as the $flag is on

        while ( ($flag == 'on') && ($k < count($pieces)) ) {
          while (substr($pieces[$k], -1) == ')') {
            $post_objects[] = ')';
            if (strlen($pieces[$k]) > 1) {
              $pieces[$k] = substr($pieces[$k], 0, -1);
            } else {
              $pieces[$k] = '';
            }
          }

// If the word doesn't end in double quotes, append it to the $tmpstring.
          if (substr($pieces[$k], -1) != '"') {
// Tack this word onto the current string entity
            $tmpstring .= ' ' . $pieces[$k];

// Move on to the next word
            $k++;
            continue;
          } else {
/* If the $piece ends in double quotes, strip the double quotes, tack the
   $piece onto the tail of the string, push the $tmpstring onto the $haves,
   kill the $tmpstring, turn the $flag "off", and return.
*/
            $tmpstring .= ' ' . trim(ereg_replace('"', ' ', $pieces[$k]));

// Push the $tmpstring onto the array of stuff to search for
            $objects[] = trim($tmpstring);

            for ($j=0; $j<count($post_objects); $j++) {
              $objects[] = $post_objects[$j];
            }

            unset($tmpstring);

// Turn off the flag to exit the loop
            $flag = 'off';
          }
        }
      }
    }

// add default logical operators if needed
    $temp = array();
    for($i=0; $i<(count($objects)-1); $i++) {
      $temp[] = $objects[$i];
      if ( ($objects[$i] != 'and') &&
           ($objects[$i] != 'or') &&
           ($objects[$i] != '(') &&
           ($objects[$i+1] != 'and') &&
           ($objects[$i+1] != 'or') &&
           ($objects[$i+1] != ')') ) {
        $temp[] = ADVANCED_SEARCH_DEFAULT_OPERATOR;
      }
    }
    $temp[] = $objects[$i];
    $objects = $temp;

    $keyword_count = 0;
    $operator_count = 0;
    $balance = 0;
    for($i=0; $i<count($objects); $i++) {
      if ($objects[$i] == '(') $balance --;
      if ($objects[$i] == ')') $balance ++;
      if ( ($objects[$i] == 'and') || ($objects[$i] == 'or') ) {
        $operator_count ++;
      } elseif ( ($objects[$i]) && ($objects[$i] != '(') && ($objects[$i] != ')') ) {
        $keyword_count ++;
      }
    }

    if ( ($operator_count < $keyword_count) && ($balance == 0) ) {
      return true;
    } else {
      return false;
    }
  }

////
// Check date
  function tep_checkdate($date_to_check, $format_string, &$date_array) {
    $separator_idx = -1;

    $separators = array('-', ' ', '/', '.');
    $month_abbr = array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');
    $no_of_days = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

    $format_string = strtolower($format_string);

    if (strlen($date_to_check) != strlen($format_string)) {
      return false;
    }

    $size = sizeof($separators);
    for ($i=0; $i<$size; $i++) {
      $pos_separator = strpos($date_to_check, $separators[$i]);
      if ($pos_separator != false) {
        $date_separator_idx = $i;
        break;
      }
    }

    for ($i=0; $i<$size; $i++) {
      $pos_separator = strpos($format_string, $separators[$i]);
      if ($pos_separator != false) {
        $format_separator_idx = $i;
        break;
      }
    }

    if ($date_separator_idx != $format_separator_idx) {
      return false;
    }

    if ($date_separator_idx != -1) {
      $format_string_array = explode( $separators[$date_separator_idx], $format_string );
      if (sizeof($format_string_array) != 3) {
        return false;
      }

      $date_to_check_array = explode( $separators[$date_separator_idx], $date_to_check );
      if (sizeof($date_to_check_array) != 3) {
        return false;
      }

      $size = sizeof($format_string_array);
      for ($i=0; $i<$size; $i++) {
        if ($format_string_array[$i] == 'mm' || $format_string_array[$i] == 'mmm') $month = $date_to_check_array[$i];
        if ($format_string_array[$i] == 'dd') $day = $date_to_check_array[$i];
        if ( ($format_string_array[$i] == 'yyyy') || ($format_string_array[$i] == 'aaaa') ) $year = $date_to_check_array[$i];
      }
    } else {
      if (strlen($format_string) == 8 || strlen($format_string) == 9) {
        $pos_month = strpos($format_string, 'mmm');
        if ($pos_month != false) {
          $month = substr( $date_to_check, $pos_month, 3 );
          $size = sizeof($month_abbr);
          for ($i=0; $i<$size; $i++) {
            if ($month == $month_abbr[$i]) {
              $month = $i;
              break;
            }
          }
        } else {
          $month = substr($date_to_check, strpos($format_string, 'mm'), 2);
        }
      } else {
        return false;
      }

      $day = substr($date_to_check, strpos($format_string, 'dd'), 2);
      $year = substr($date_to_check, strpos($format_string, 'yyyy'), 4);
    }

    if (strlen($year) != 4) {
      return false;
    }

    if (!settype($year, 'integer') || !settype($month, 'integer') || !settype($day, 'integer')) {
      return false;
    }

    if ($month > 12 || $month < 1) {
      return false;
    }

    if ($day < 1) {
      return false;
    }

    if (tep_is_leap_year($year)) {
      $no_of_days[1] = 29;
    }

    if ($day > $no_of_days[$month - 1]) {
      return false;
    }

    $date_array = array($year, $month, $day);

    return true;
  }

////
// Check if year is a leap year
  function tep_is_leap_year($year) {
    if ($year % 100 == 0) {
      if ($year % 400 == 0) return true;
    } else {
      if (($year % 4) == 0) return true;
    }

    return false;
  }

////
// Return table heading with sorting capabilities
  function tep_create_sort_heading($key, $heading) {
    global $osC_Language;

    $current = false;
    $direction = false;

    if (!isset($_GET['sort'])) {
      $current = 'name';
    } elseif (($_GET['sort'] == $key) || ($_GET['sort'] == $key . '|d')) {
      $current = $key;
    }

    if ($key == $current) {
      if (isset($_GET['sort'])) {
        $direction = ($_GET['sort'] == $key) ? '+' : '-';
      } else {
        $direction = '+';
      }
    }

    $sort_heading = osc_link_object(osc_href_link(basename($_SERVER['PHP_SELF']), tep_get_all_get_params(array('page', 'sort')) . '&sort=' . $key . ($direction == '+' ? '|d' : '')), $heading . (($key == $current) ? $direction : ''), 'title="' . (isset($_GET['sort']) && ($_GET['sort'] == $key) ? sprintf($osC_Language->get('listing_sort_ascendingly'), $heading) : sprintf($osC_Language->get('listing_sort_descendingly'), $heading)) . '" class="productListing-heading"');

    return $sort_heading;
  }

////
// Recursively go through the categories and retreive all parent categories IDs
// TABLES: categories
  function tep_get_parent_categories(&$categories, $categories_id) {
    global $osC_Database;

    $Qparent = $osC_Database->query('select parent_id from :table_categories where categories_id = :categories_id');
    $Qparent->bindTable(':table_categories', TABLE_CATEGORIES);
    $Qparent->bindInt(':categories_id', $categories_id);
    $Qparent->execute();

    while ($Qparent->next()) {
      if ($Qparent->valueInt('parent_id') == 0) return true;

      $categories[sizeof($categories)] = $Qparent->valueInt('parent_id');

      if ($Qparent->valueInt('parent_id') != $categories_id) {
        tep_get_parent_categories($categories, $Qparent->valueInt('parent_id'));
      }
    }
  }

////
// Construct a category path to the product
// TABLES: products_to_categories
  function tep_get_product_path($products_id) {
    global $osC_Database;

    $cPath = '';

    $Qcategory = $osC_Database->query('select p2c.categories_id from :table_products p, :table_products_to_categories p2c where p.products_id = :products_id and p.products_status = :products_status and p.products_id = p2c.products_id limit 1');
    $Qcategory->bindTable(':table_products', TABLE_PRODUCTS);
    $Qcategory->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
    $Qcategory->bindInt(':products_id', $products_id);
    $Qcategory->bindInt(':products_status', 1);
    $Qcategory->execute();

    if ($Qcategory->numberOfRows()) {
      $categories = array();
      tep_get_parent_categories($categories, $Qcategory->valueInt('categories_id'));

      $categories = array_reverse($categories);

      $cPath = implode('_', $categories);

      if (tep_not_null($cPath)) $cPath .= '_';
      $cPath .= $Qcategory->valueInt('categories_id');
    }

    return $cPath;
  }

////
// Return a product ID with attributes
  function tep_get_uprid($prid, $params) {
    if (is_numeric($prid)) {
      $uprid = $prid;

      if (is_array($params) && (sizeof($params) > 0)) {
        $attributes_check = true;
        $attributes_ids = '';

        reset($params);
        while (list($option, $value) = each($params)) {
          if (is_numeric($option) && is_numeric($value)) {
            $attributes_ids .= '{' . (int)$option . '}' . (int)$value;
          } else {
            $attributes_check = false;
            break;
          }
        }

        if ($attributes_check == true) {
          $uprid .= $attributes_ids;
        }
      }
    } else {
      $uprid = tep_get_prid($prid);

      if (is_numeric($uprid)) {
        if (strpos($prid, '{') !== false) {
          $attributes_check = true;
          $attributes_ids = '';

// strpos()+1 to remove up to and including the first { which would create an empty array element in explode()
          $attributes = explode('{', substr($prid, strpos($prid, '{')+1));

          for ($i=0, $n=sizeof($attributes); $i<$n; $i++) {
            $pair = explode('}', $attributes[$i]);

            if (is_numeric($pair[0]) && is_numeric($pair[1])) {
              $attributes_ids .= '{' . (int)$pair[0] . '}' . (int)$pair[1];
            } else {
              $attributes_check = false;
              break;
            }
          }

          if ($attributes_check == true) {
            $uprid .= $attributes_ids;
          }
        }
      } else {
        return false;
      }
    }

    return $uprid;
  }

////
// Return a product ID from a product ID with attributes
  function tep_get_prid($uprid) {
    $pieces = explode('{', $uprid);

    if (is_numeric($pieces[0])) {
      return $pieces[0];
    } else {
      return false;
    }
  }

////
//! Send email (text/html) using MIME
// This is the central mail function. The SMTP Server should be configured
// correct in php.ini
// Parameters:
// $to_name           The name of the recipient, e.g. "Jan Wildeboer"
// $to_email_address  The eMail address of the recipient,
//                    e.g. jan.wildeboer@gmx.de
// $email_subject     The subject of the eMail
// $email_text        The text of the eMail, may contain HTML entities
// $from_email_name   The name of the sender, e.g. Shop Administration
// $from_email_adress The eMail address of the sender,
//                    e.g. info@mytepshop.com

  function tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address) {
    if (SEND_EMAILS == '-1') return false;

    // Instantiate a new mail object
    $message = new email(array('X-Mailer: osCommerce Mailer'));

    // Build the text version
    $text = strip_tags($email_text);
    if (EMAIL_USE_HTML == '1') {
      $message->add_html($email_text, $text);
    } else {
      $message->add_text($text);
    }

    // Send message
    $message->build_message();
    $message->send($to_name, $to_email_address, $from_email_name, $from_email_address, $email_subject);
  }

////
// Check if product has attributes
  function tep_has_product_attributes($products_id) {
    global $osC_Database;

    $Qattributes = $osC_Database->query('select count(*) as count from :table_products_attributes where products_id = :products_id');
    $Qattributes->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
    $Qattributes->bindInt(':products_id', $products_id);
    $Qattributes->execute();

    if ($Qattributes->valueInt('count') > 0) {
      return true;
    } else {
      return false;
    }
  }

////
// Get the number of times a word/character is present in a string
  function tep_word_count($string, $needle) {
    $temp_array = split($needle, $string);

    return sizeof($temp_array);
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

  function tep_not_null($value) {
    if (is_array($value)) {
      if (sizeof($value) > 0) {
        return true;
      } else {
        return false;
      }
    } else {
      if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {
        return true;
      } else {
        return false;
      }
    }
  }

  function osc_empty($value) {
    if (is_array($value)) {
      if (sizeof($value) > 0) {
        return false;
      } else {
        return true;
      }
    } else {
      if ((strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {
        return false;
      } else {
        return true;
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

  function tep_array_filter($array, $callback) {
    $parsed_array = array();

    reset($array);
    while (list($key, $value) = each($array)) {
      if ($callback($value)) {
        $parsed_array[] = $value;
      }
    }

    return $parsed_array;
  }

////
// Parse and secure the cPath parameter values
  function tep_parse_category_path($cPath) {
// make sure the category IDs are integers
    $cPath_array = tep_array_filter(explode('_', $cPath), 'is_numeric');

// make sure no duplicate category IDs exist which could lock the server in a loop
    return array_unique($cPath_array);
  }

////
// Return a random value
  function tep_rand($min = null, $max = null) {
    static $seeded;

    if (!isset($seeded)) {
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

  function tep_get_ip_address() {
    if (isset($_SERVER)) {
      if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
      } else {
        $ip = $_SERVER['REMOTE_ADDR'];
      }
    } else {
      if (getenv('HTTP_X_FORWARDED_FOR')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
      } elseif (getenv('HTTP_CLIENT_IP')) {
        $ip = getenv('HTTP_CLIENT_IP');
      } else {
        $ip = getenv('REMOTE_ADDR');
      }
    }

    return $ip;
  }

  function tep_count_customer_orders($id = '', $check_session = true) {
    global $osC_Database, $osC_Customer;

    if (is_numeric($id) == false) {
      if ($osC_Customer->isLoggedOn()) {
        $id = $osC_Customer->getID();
      } else {
        return 0;
      }
    }

    if ($check_session == true) {
      if ( ($osC_Customer->isLoggedOn() == false) || ($id != $osC_Customer->getID()) ) {
        return 0;
      }
    }

    $Qorders = $osC_Database->query('select count(*) as total from :table_orders where customers_id = :customers_id');
    $Qorders->bindTable(':table_orders', TABLE_ORDERS);
    $Qorders->bindInt(':customers_id', $id);
    $Qorders->execute();

    return $Qorders->valueInt('total');
  }

  function tep_count_customer_address_book_entries($id = '', $check_session = true) {
    global $osC_Database, $osC_Customer;

    if (is_numeric($id) == false) {
      if ($osC_Customer->isLoggedOn()) {
        $id = $osC_Customer->getID();
      } else {
        return 0;
      }
    }

    if ($check_session == true) {
      if ( ($osC_Customer->isLoggedOn() == false) || ($id != $osC_Customer->getID()) ) {
        return 0;
      }
    }

    $Qaddresses = $osC_Database->query('select count(*) as total from :table_address_book where customers_id = :customers_id');
    $Qaddresses->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
    $Qaddresses->bindInt(':customers_id', $id);
    $Qaddresses->execute();

    return $Qaddresses->valueInt('total');
  }

// nl2br() prior PHP 4.2.0 did not convert linefeeds on all OSs (it only converted \n)
  function tep_convert_linefeeds($from, $to, $string) {
    return str_replace($from, $to, $string);
  }

////
// This funstion validates a plain text password with an
// encrpyted password
  function tep_validate_password($plain, $encrypted) {
    if (tep_not_null($plain) && tep_not_null($encrypted)) {
// split apart the hash / salt
      $stack = explode(':', $encrypted);

      if (sizeof($stack) != 2) return false;

      if (md5($stack[1] . $plain) == $stack[0]) {
        return true;
      }
    }

    return false;
  }

////
// This function makes a new password from a plaintext password.
  function tep_encrypt_password($plain) {
    $password = '';

    for ($i=0; $i<10; $i++) {
      $password .= tep_rand();
    }

    $salt = substr(md5($password), 0, 2);

    $password = md5($salt . $plain) . ':' . $salt;

    return $password;
  }

  function tep_validate_email($email) {
    $valid_address = true;

    $mail_pat = '^(.+)@(.+)$';
    $valid_chars = "[^] \(\)<>@,;:\.\\\"\[]";
    $atom = "$valid_chars+";
    $quoted_user='(\"[^\"]*\")';
    $word = "($atom|$quoted_user)";
    $user_pat = "^$word(\.$word)*$";
    $ip_domain_pat='^\[([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\]$';
    $domain_pat = "^$atom(\.$atom)*$";

    if (eregi($mail_pat, $email, $components)) {
      $user = $components[1];
      $domain = $components[2];
      // validate user
      if (eregi($user_pat, $user)) {
        // validate domain
        if (eregi($ip_domain_pat, $domain, $ip_components)) {
          // this is an IP address
          for ($i=1;$i<=4;$i++) {
            if ($ip_components[$i] > 255) {
              $valid_address = false;
              break;
            }
          }
        }
        else {
          // Domain is a name, not an IP
          if (eregi($domain_pat, $domain)) {
            /* domain name seems valid, but now make sure that it ends in a valid TLD or ccTLD
               and that there's a hostname preceding the domain or country. */
            $domain_components = explode(".", $domain);
            // Make sure there's a host name preceding the domain.
            if (sizeof($domain_components) < 2) {
              $valid_address = false;
            } else {
              $top_level_domain = strtolower($domain_components[sizeof($domain_components)-1]);
              // Allow all 2-letter TLDs (ccTLDs)
              if (eregi('^[a-z][a-z]$', $top_level_domain) != 1) {
                $tld_pattern = '';
                // Get authorized TLDs from text file
                $tlds = file('includes/tld.txt');
                while (list(,$line) = each($tlds)) {
                  // Get rid of comments
                  $words = explode('#', $line);
                  $tld = trim($words[0]);
                  // TLDs should be 3 letters or more
                  if (eregi('^[a-z]{3,}$', $tld) == 1) {
                    $tld_pattern .= '^' . $tld . '$|';
                  }
                }
                // Remove last '|'
                $tld_pattern = substr($tld_pattern, 0, -1);
                if (eregi("$tld_pattern", $top_level_domain) == 0) {
                    $valid_address = false;
                }
              }
            }
          }
          else {
            $valid_address = false;
          }
        }
      }
      else {
        $valid_address = false;
      }
    }
    else {
      $valid_address = false;
    }
    if ($valid_address && ENTRY_EMAIL_ADDRESS_CHECK == '1') {
      if (!checkdnsrr($domain, "MX") && !checkdnsrr($domain, "A")) {
        $valid_address = false;
      }
    }
    return $valid_address;
  }

  function tep_validate_credit_card($card_number) {
    $cardNumber = strrev($card_number);
    $numSum = 0;

    for ($i=0; $i<strlen($cardNumber); $i++) {
      $currentNum = substr($cardNumber, $i, 1);

// Double every second digit
      if ($i % 2 == 1) {
        $currentNum *= 2;
      }

// Add digits of 2-digit numbers together
      if ($currentNum > 9) {
        $firstNum = $currentNum % 10;
        $secondNum = ($currentNum - $firstNum) / 10;
        $currentNum = $firstNum + $secondNum;
      }

      $numSum += $currentNum;
    }

// If the total has no remainder it's OK
    return ($numSum % 10 == 0);
  }

////
// Creates a pull-down list of countries
  function tep_get_country_list($name, $selected = '', $parameters = '', $required = false) {
    global $osC_Language;

    $countries_array = array(array('id' => '', 'text' => $osC_Language->get('pull_down_default')));
    $countries = tep_get_countries();

    for ($i=0, $n=sizeof($countries); $i<$n; $i++) {
      $countries_array[] = array('id' => $countries[$i]['countries_id'], 'text' => $countries[$i]['countries_name']);
    }

    return osc_draw_pull_down_menu($name, $countries_array, $selected, $parameters);
  }

  function osc_setlocale($category, $locale) {
    if (version_compare(PHP_VERSION, '4.3', '<')) {
      if (is_array($locale)) {
        foreach ($locale as $l) {
          if (($result = setlocale($category, $l)) !== false) {
            return $result;
          }
        }

        return false;
      } else {
        return setlocale($category, $locale);
      }
    } else {
      return setlocale($category, $locale);
    }
  }

  function osc_object2array_recursive($object) {
    $array = array();
       
    if (is_array($object)) {
      foreach ($object as $key => $value) {
        $array[$key] = osc_object2array_recursive($value);
      }
    } else {
      $var = get_object_vars($object);
           
      if ($var) {
        foreach($var as $key => $value) {
          $array[$key] = ($key && !$value) ? (string)$value : osc_object2array_recursive($value);
        }
      } else {
        return $object;
      }
    }

    return $array;
  }
?>

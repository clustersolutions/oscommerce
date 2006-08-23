<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Address {
    function format($address, $new_line = "\n") {
      global $osC_Database;

      $address_format = '';

      if (is_numeric($address)) {
        $Qaddress = $osC_Database->query('select entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from :table_address_book where address_book_id = :address_book_id');
        $Qaddress->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
        $Qaddress->bindInt(':address_book_id', $address);
        $Qaddress->execute();

        $address = $Qaddress->toArray();
      }

      $firstname = $lastname = '';

      if (isset($address['firstname']) && !empty($address['firstname'])) {
        $firstname = $address['firstname'];
        $lastname = $address['lastname'];
      } elseif (isset($address['name']) && !empty($address['name'])) {
        $firstname = $address['name'];
      }

      $state = $address['state'];
      $state_code = $address['state_code'];

      if (isset($address['zone_id']) && is_numeric($address['zone_id']) && ($address['zone_id'] > 0)) {
        $state = osC_Address::getZoneName($address['zone_id']);
        $state_code = osC_Address::getZoneCode($address['zone_id']);
      }

      $country = $address['country'];

      if (isset($address['country_id']) && is_numeric($address['country_id']) && ($address['country_id'] > 0)) {
        $country = osC_Address::getCountryName($address['country_id']);
      }

      if (isset($address['format'])) {
        $address_format = $address['format'];
      } elseif (isset($address['country_id']) && is_numeric($address['country_id']) && ($address['country_id'] > 0)) {
        $address_format = osC_Address::getFormat($address['country_id']);
      }

      if (empty($address_format)) {
        $address_format = ":name\n:street_address\n:postcode :city\n:country";
      }

      $find_array = array('/\:name\b/',
                          '/\:street_address\b/',
                          '/\:suburb\b/',
                          '/\:city\b/',
                          '/\:postcode\b/',
                          '/\:state\b/',
                          '/\:state_code\b/',
                          '/\:country\b/');

      $replace_array = array(osc_output_string_protected($firstname . ' ' . $lastname),
                             osc_output_string_protected($address['street_address']),
                             osc_output_string_protected($address['suburb']),
                             osc_output_string_protected($address['city']),
                             osc_output_string_protected($address['postcode']),
                             osc_output_string_protected($state),
                             osc_output_string_protected($state_code),
                             osc_output_string_protected($country));

      $formated = preg_replace($find_array, $replace_array, $address_format);

      if ( (ACCOUNT_COMPANY > -1) && !empty($address['company']) ) {
        $company = osc_output_string_protected($address['company']);

        $formated = $company . $new_line . $formated;
      }

      if ($new_line != "\n") {
        $formated = str_replace("\n", $new_line, $formated);
      }

      return $formated;
    }

    function getCountries() {
      global $osC_Database;

      static $_countries;

      if (!isset($_countries)) {
        $_countries = array();

        $Qcountries = $osC_Database->query('select * from :table_countries order by countries_name');
        $Qcountries->bindTable(':table_countries', TABLE_COUNTRIES);
        $Qcountries->execute();

        while ($Qcountries->next()) {
          $_countries[] = array('id' => $Qcountries->valueInt('countries_id'),
                                'name' => $Qcountries->value('countries_name'),
                                'iso_2' => $Qcountries->value('countries_iso_code_2'),
                                'iso_3' => $Qcountries->value('countries_iso_code_3'),
                                'format' => $Qcountries->value('address_format'));
        }

        $Qcountries->freeResult();
      }

      return $_countries;
    }

    function getCountryName($id) {
      global $osC_Database;

      $Qcountry = $osC_Database->query('select countries_name from :table_countries where countries_id = :countries_id');
      $Qcountry->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qcountry->bindInt(':countries_id', $id);
      $Qcountry->execute();

      return $Qcountry->value('countries_name');
    }

    function getCountryIsoCode2($id) {
      global $osC_Database;

      $Qcountry = $osC_Database->query('select countries_iso_code_2 from :table_countries where countries_id = :countries_id');
      $Qcountry->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qcountry->bindInt(':countries_id', $id);
      $Qcountry->execute();

      return $Qcountry->value('countries_iso_code_2');
    }

    function getCountryIsoCode3($id) {
      global $osC_Database;

      $Qcountry = $osC_Database->query('select countries_iso_code_3 from :table_countries where countries_id = :countries_id');
      $Qcountry->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qcountry->bindInt(':countries_id', $id);
      $Qcountry->execute();

      return $Qcountry->value('countries_iso_code_3');
    }

    function getFormat($id) {
      global $osC_Database;

      $Qcountry = $osC_Database->query('select address_format from :table_countries where countries_id = :countries_id');
      $Qcountry->bindTable(':table_countries', TABLE_COUNTRIES);
      $Qcountry->bindInt(':countries_id', $id);
      $Qcountry->execute();

      return $Qcountry->value('address_format');
    }

    function getZoneName($id) {
      global $osC_Database;

      $Qzone = $osC_Database->query('select zone_name from :table_zones where zone_id = :zone_id');
      $Qzone->bindTable(':table_zones', TABLE_ZONES);
      $Qzone->bindInt(':zone_id', $id);
      $Qzone->execute();

      return $Qzone->value('zone_name');
    }

    function getZoneCode($id) {
      global $osC_Database;

      $Qzone = $osC_Database->query('select zone_code from :table_zones where zone_id = :zone_id');
      $Qzone->bindTable(':table_zones', TABLE_ZONES);
      $Qzone->bindInt(':zone_id', $id);
      $Qzone->execute();

      return $Qzone->value('zone_code');
    }

    function getCountryZones($id) {
      global $osC_Database;

      $zones_array = array();

      $Qzones = $osC_Database->query('select zone_id, zone_name from :table_zones where zone_country_id = :zone_country_id order by zone_name');
      $Qzones->bindTable(':table_zones', TABLE_ZONES);
      $Qzones->bindInt(':zone_country_id', $id);
      $Qzones->execute();

      while ($Qzones->next()) {
        $zones_array[] = array('id' => $Qzones->valueInt('zone_id'),
                               'name' => $Qzones->value('zone_name'));
      }

      return $zones_array;
    }
  }
?>

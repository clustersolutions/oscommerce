<?php
/*
  $Id: tax.php,v 1.2 2003/11/17 13:30:25 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  class osC_Tax {
    var $tax_rates;

// class constructor
    function osC_Tax() {
      $this->tax_rates = array();
    }

// class methods
    function getTaxRate($class_id, $country_id = -1, $zone_id = -1) {
      global $osC_Customer;

      if ( ($country_id == -1) && ($zone_id == -1) ) {
        if ($osC_Customer->isLoggedOn()) {
          $country_id = $osC_Customer->country_id;
          $zone_id = $osC_Customer->zone_id;
        } else {
          $country_id = STORE_COUNTRY;
          $zone_id = STORE_ZONE;
        }
      }

      if (isset($this->tax_rates[$class_id][$country_id][$zone_id]['rate']) == false) {
        $tax_query = tep_db_query("select sum(tax_rate) as tax_rate from " . TABLE_TAX_RATES . " tr left join " . TABLE_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id) left join " . TABLE_GEO_ZONES . " tz on (tz.geo_zone_id = tr.tax_zone_id) where (za.zone_country_id is null or za.zone_country_id = '0' or za.zone_country_id = '" . (int)$country_id . "') and (za.zone_id is null or za.zone_id = '0' or za.zone_id = '" . (int)$zone_id . "') and tr.tax_class_id = '" . (int)$class_id . "' group by tr.tax_priority");
        if (tep_db_num_rows($tax_query)) {
          $tax_multiplier = 1.0;
          while ($tax = tep_db_fetch_array($tax_query)) {
            $tax_multiplier *= 1.0 + ($tax['tax_rate'] / 100);
          }

          $tax_rate = ($tax_multiplier - 1.0) * 100;
        } else {
          $tax_rate = 0;
        }

        $this->tax_rates[$class_id][$country_id][$zone_id]['rate'] = $tax_rate;
      }

      return $this->tax_rates[$class_id][$country_id][$zone_id]['rate'];
    }

    function getTaxRateDescription($class_id, $country_id, $zone_id) {
      if (isset($this->tax_rates[$class_id][$country_id][$zone_id]['description']) == false) {
        $tax_query = tep_db_query("select tax_description from " . TABLE_TAX_RATES . " tr left join " . TABLE_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id) left join " . TABLE_GEO_ZONES . " tz on (tz.geo_zone_id = tr.tax_zone_id) where (za.zone_country_id is null or za.zone_country_id = '0' or za.zone_country_id = '" . (int)$country_id . "') and (za.zone_id is null or za.zone_id = '0' or za.zone_id = '" . (int)$zone_id . "') and tr.tax_class_id = '" . (int)$class_id . "' order by tr.tax_priority");
        if (tep_db_num_rows($tax_query)) {
          $tax_description = '';

          while ($tax = tep_db_fetch_array($tax_query)) {
            $tax_description .= $tax['tax_description'] . ' + ';
          }

          $this->tax_rates[$class_id][$country_id][$zone_id]['description'] = substr($tax_description, 0, -3);
        } else {
          $this->tax_rates[$class_id][$country_id][$zone_id]['description'] = TEXT_UNKNOWN_TAX_RATE;
        }
      }

      return $this->tax_rates[$class_id][$country_id][$zone_id]['description'];
    }
  }
?>

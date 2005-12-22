<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

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
      global $osC_Database, $osC_Customer;

      if ( ($country_id == -1) && ($zone_id == -1) ) {
        if ($osC_Customer->isLoggedOn()) {
          $country_id = $osC_Customer->getCountryID();
          $zone_id = $osC_Customer->getZoneID();
        } else {
          $country_id = STORE_COUNTRY;
          $zone_id = STORE_ZONE;
        }
      }

      if (isset($this->tax_rates[$class_id][$country_id][$zone_id]['rate']) == false) {
        $Qtax = $osC_Database->query('select sum(tax_rate) as tax_rate from :table_tax_rates tr left join :table_zones_to_geo_zones za on (tr.tax_zone_id = za.geo_zone_id) left join :table_geo_zones tz on (tz.geo_zone_id = tr.tax_zone_id) where (za.zone_country_id is null or za.zone_country_id = 0 or za.zone_country_id = :zone_country_id) and (za.zone_id is null or za.zone_id = 0 or za.zone_id = :zone_id) and tr.tax_class_id = :tax_class_id group by tr.tax_priority');
        $Qtax->bindTable(':table_tax_rates', TABLE_TAX_RATES);
        $Qtax->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
        $Qtax->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
        $Qtax->bindInt(':zone_country_id', $country_id);
        $Qtax->bindInt(':zone_id', $zone_id);
        $Qtax->bindInt(':tax_class_id', $class_id);
        $Qtax->execute();

        if ($Qtax->numberOfRows()) {
          $tax_multiplier = 1.0;
          while ($Qtax->next()) {
            $tax_multiplier *= 1.0 + ($Qtax->value('tax_rate') / 100);
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
      global $osC_Database;

      if (isset($this->tax_rates[$class_id][$country_id][$zone_id]['description']) == false) {
        $Qtax = $osC_Database->query('select tax_description from :table_tax_rates tr left join :table_zones_to_geo_zones za on (tr.tax_zone_id = za.geo_zone_id) left join :table_geo_zones tz on (tz.geo_zone_id = tr.tax_zone_id) where (za.zone_country_id is null or za.zone_country_id = 0 or za.zone_country_id = :zone_country_id) and (za.zone_id is null or za.zone_id = 0 or za.zone_id = :zone_id) and tr.tax_class_id = :tax_class_id group by tr.tax_priority');
        $Qtax->bindTable(':table_tax_rates', TABLE_TAX_RATES);
        $Qtax->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
        $Qtax->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
        $Qtax->bindInt(':zone_country_id', $country_id);
        $Qtax->bindInt(':zone_id', $zone_id);
        $Qtax->bindInt(':tax_class_id', $class_id);
        $Qtax->execute();

        if ($Qtax->numberOfRows()) {
          $tax_description = '';

          while ($Qtax->next()) {
            $tax_description .= $Qtax->value('tax_description') . ' + ';
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

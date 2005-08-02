<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  class osC_Weight {
    var $weight_classes = array(),
        $precision;

// class constructor
    function osC_Weight($precision = '2') {
      $this->precision = $precision;

      $this->prepareRules();
    }

    function prepareRules() {
      global $osC_Database, $osC_Session;

      $Qrules = $osC_Database->query('select r.weight_class_from_id, r.weight_class_to_id, r.weight_class_rule from :table_weight_class_rules r, :table_weight_class c where c.weight_class_id = r.weight_class_from_id');
      $Qrules->bindRaw(':table_weight_class_rules', TABLE_WEIGHT_CLASS_RULES);
      $Qrules->bindRaw(':table_weight_class', TABLE_WEIGHT_CLASS);
      $Qrules->setCache('weight-rules');
      $Qrules->execute();

      while ($Qrules->next()) {
        $this->weight_classes[$Qrules->valueInt('weight_class_from_id')][$Qrules->valueInt('weight_class_to_id')] = $Qrules->value('weight_class_rule');
      }

      $Qclasses = $osC_Database->query('select weight_class_id, weight_class_key, weight_class_title from :table_weight_class where language_id = :language_id');
      $Qclasses->bindRaw(':table_weight_class', TABLE_WEIGHT_CLASS);
      $Qclasses->bindInt(':language_id', $osC_Session->value('languages_id'));
      $Qclasses->setCache('weight-classes');
      $Qclasses->execute();

      while ($Qclasses->next()) {
        $this->weight_classes[$Qclasses->valueInt('weight_class_id')]['key'] = $Qclasses->value('weight_class_key');
        $this->weight_classes[$Qclasses->valueInt('weight_class_id')]['title'] = $Qclasses->value('weight_class_title');
      }

      $Qrules->freeResult();
      $Qclasses->freeResult();
    }

    function convert($value, $unit_from, $unit_to) {
      if ($unit_from == $unit_to) {
        return number_format($value, (int)$this->precision, NUMERIC_DECIMAL_SEPARATOR, NUMERIC_THOUSANDS_SEPARATOR);
      } else {
        return number_format($value * $this->weight_classes[(int)$unit_from][(int)$unit_to], (int)$this->precision, NUMERIC_DECIMAL_SEPARATOR, NUMERIC_THOUSANDS_SEPARATOR);
      }
    }

    function display($value, $class) {
      return number_format($value, (int)$this->precision, NUMERIC_DECIMAL_SEPARATOR, NUMERIC_THOUSANDS_SEPARATOR) . $this->weight_classes[$class]['key'];
    }
  }
?>

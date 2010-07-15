<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop;

  use osCommerce\OM\Core\Registry;

  class Weight {
    protected $weight_classes = array();
    protected $precision = 2;

    public function __construct($precision = null) {
      if ( is_int($precision) ) {
        $this->precision = $precision;
      }

      $this->prepareRules();
    }

    public function getTitle($id) {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Language = Registry::get('Language');

      $Qweight = $OSCOM_Database->query('select weight_class_title from :table_weight_classes where weight_class_id = :weight_class_id and language_id = :language_id');
      $Qweight->bindInt(':weight_class_id', $id);
      $Qweight->bindInt(':language_id', $OSCOM_Language->getID());
      $Qweight->execute();

      return $Qweight->value('weight_class_title');
    }

    public function prepareRules() {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Language = Registry::get('Language');

      $Qrules = $OSCOM_Database->query('select r.weight_class_from_id, r.weight_class_to_id, r.weight_class_rule from :table_weight_classes_rules r, :table_weight_classes c where c.weight_class_id = r.weight_class_from_id');
      $Qrules->setCache('weight-rules');
      $Qrules->execute();

      while ( $Qrules->next() ) {
        $this->weight_classes[$Qrules->valueInt('weight_class_from_id')][$Qrules->valueInt('weight_class_to_id')] = $Qrules->value('weight_class_rule');
      }

      $Qrules->freeResult();

      $Qclasses = $OSCOM_Database->query('select weight_class_id, weight_class_key, weight_class_title from :table_weight_classes where language_id = :language_id');
      $Qclasses->bindInt(':language_id', $OSCOM_Language->getID());
      $Qclasses->setCache('weight-classes');
      $Qclasses->execute();

      while ( $Qclasses->next() ) {
        $this->weight_classes[$Qclasses->valueInt('weight_class_id')]['key'] = $Qclasses->value('weight_class_key');
        $this->weight_classes[$Qclasses->valueInt('weight_class_id')]['title'] = $Qclasses->value('weight_class_title');
      }

      $Qclasses->freeResult();
    }

    public function convert($value, $unit_from, $unit_to) {
      $OSCOM_Language = Registry::get('Language');

      if ( $unit_from == $unit_to ) {
        return number_format($value, $this->precision, $OSCOM_Language->getNumericDecimalSeparator(), $OSCOM_Language->getNumericThousandsSeparator());
      } else {
        return number_format($value * $this->weight_classes[(int)$unit_from][(int)$unit_to], $this->precision, $OSCOM_Language->getNumericDecimalSeparator(), $OSCOM_Language->getNumericThousandsSeparator());
      }
    }

    public function display($value, $class) {
      $OSCOM_Language = Registry::get('Language');

      return number_format($value, $this->precision, $OSCOM_Language->getNumericDecimalSeparator(), $OSCOM_Language->getNumericThousandsSeparator()) . $this->weight_classes[$class]['key'];
    }

    public function getClasses() {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Language = Registry::get('Language');

      $weight_class_array = array();

      $Qclasses = $OSCOM_Database->query('select weight_class_id, weight_class_title from :table_weight_classes where language_id = :language_id order by weight_class_title');
      $Qclasses->bindInt(':language_id', $OSCOM_Language->getID());
      $Qclasses->execute();

      while ( $Qclasses->next() ) {
        $weight_class_array[] = array('id' => $Qclasses->valueInt('weight_class_id'),
                                      'title' => $Qclasses->value('weight_class_title'));
      }

      return $weight_class_array;
    }
  }
?>

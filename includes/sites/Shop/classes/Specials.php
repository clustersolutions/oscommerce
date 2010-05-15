<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Shop;

  use osCommerce\OM\Registry;

  class Specials {
    protected $_specials = array();

    public function activateAll() {
      $OSCOM_Database = Registry::get('Database');

      $Qspecials = $OSCOM_Database->query('select specials_id from :table_specials where status = 0 and now() >= start_date and start_date > 0 and now() < expires_date');
      $Qspecials->execute();

      while ( $Qspecials->next() ) {
        $this->_setStatus($Qspecials->valueInt('specials_id'), true);
      }
    }

    public function expireAll() {
      $OSCOM_Database = Registry::get('Database');

      $Qspecials = $OSCOM_Database->query('select specials_id from :table_specials where status = 1 and now() >= expires_date and expires_date > 0');
      $Qspecials->execute();

      while ( $Qspecials->next() ) {
        $this->_setStatus($Qspecials->valueInt('specials_id'), false);
      }
    }

    public function isActive($id) {
      $OSCOM_Database = Registry::get('Database');

      if ( !isset($this->_specials[$id]) ) {
        $this->_specials[$id] = $this->getPrice($id);
      }

      return is_numeric($this->_specials[$id]);
    }

    public function getPrice($id) {
      $OSCOM_Database = Registry::get('Database');

      if ( !isset($this->_specials[$id]) ) {
        $Qspecial = $OSCOM_Database->query('select specials_new_products_price from :table_specials where products_id = :products_id and status = 1');
        $Qspecial->bindInt(':products_id', $id);
        $Qspecial->execute();

        if ( $Qspecial->numberOfRows() > 0 ) {
          $this->_specials[$id] = $Qspecial->valueDecimal('specials_new_products_price');
        } else {
          $this->_specials[$id] = null;
        }
      }

      return $this->_specials[$id];
    }

    public function getListing() {
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Language = Registry::get('Language');

      $Qspecials = $OSCOM_Database->query('select p.products_id, p.products_price, p.products_tax_class_id, pd.products_name, pd.products_keyword, s.specials_new_products_price, i.image from :table_products p left join :table_products_images i on (p.products_id = i.products_id and i.default_flag = :default_flag), :table_products_description pd, :table_specials s where p.products_status = 1 and s.products_id = p.products_id and p.products_id = pd.products_id and pd.language_id = :language_id and s.status = 1 order by s.specials_date_added desc');
      $Qspecials->bindInt(':default_flag', 1);
      $Qspecials->bindInt(':language_id', $OSCOM_Language->getID());
      $Qspecials->setBatchLimit((isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1), MAX_DISPLAY_SPECIAL_PRODUCTS);
      $Qspecials->execute();

      return $Qspecials;
    }

    protected function _setStatus($id, $status) {
      $OSCOM_Database = Registry::get('Database');

      $Qstatus = $OSCOM_Database->query('update :table_specials set status = :status, date_status_change = now() where specials_id = :specials_id');
      $Qstatus->bindInt(':status', ($status === true) ? '1' : '0');
      $Qstatus->bindInt(':specials_id', $id);
      $Qstatus->execute();
    }
  }
?>
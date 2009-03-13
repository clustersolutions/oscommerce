<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('includes/applications/products/classes/products.php');

  if ( !class_exists('osC_Summary') ) {
    include('includes/classes/summary.php');
  }

  class osC_Summary_products extends osC_Summary {

/* Class constructor */

    function osC_Summary_products() {
      global $osC_Language;

      $osC_Language->loadIniFile('modules/summary/products.php');

      $this->_title = $osC_Language->get('summary_products_title');
      $this->_title_link = osc_href_link_admin(FILENAME_DEFAULT, 'products');

      if ( osC_Access::hasAccess('products') ) {
        $this->_setData();
      }
    }

/* Private methods */

    function _setData() {
      global $osC_Database, $osC_Language, $osC_Currencies;

      if ( !isset($osC_Currencies) ) {
        if ( !class_exists('osC_Currencies') ) {
          include('../includes/classes/currencies.php');
        }

        $osC_Currencies = new osC_Currencies();
      }

      $this->_data = '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">' .
                     '  <thead>' .
                     '    <tr>' .
                     '      <th>' . $osC_Language->get('summary_products_table_heading_products') . '</th>' .
                     '      <th>' . $osC_Language->get('summary_products_table_heading_price') . '</th>' .
                     '      <th>' . $osC_Language->get('summary_products_table_heading_date') . '</th>' .
                     '      <th>' . $osC_Language->get('summary_products_table_heading_status') . '</th>' .
                     '    </tr>' .
                     '  </thead>' .
                     '  <tbody>';

      $Qproducts = $osC_Database->query('select products_id, greatest(products_date_added, products_last_modified) as date_last_modified from :table_products where parent_id = 0 order by date_last_modified desc limit 6');
      $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
      $Qproducts->execute();

      while ( $Qproducts->next() ) {
        $data = osC_Products_Admin::get($Qproducts->valueInt('products_id'));

        $products_icon = osc_icon('products.png');
        $products_price = $data['products_price'];

        if ( !empty($data['variants']) ) {
          $products_icon = osc_icon('attach.png');
          $products_price = null;

          foreach ( $data['variants'] as $variant ) {
            if ( ($products_price === null) || ($variant['data']['price'] < $products_price) ) {
              $products_price = $variant['data']['price'];
            }
          }

          if ( $products_price === null ) {
            $products_price = 0;
          }
        }

        $this->_data .= '    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">' .
                        '      <td>' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, 'products=' . (int)$data['products_id'] . '&action=save'), $products_icon . '&nbsp;' . osc_output_string_protected($data['products_name'])) . '</td>' .
                        '      <td>' . ( !empty($data['variants']) ? 'from ' : '' ) . $osC_Currencies->format($products_price) . '</td>' .
                        '      <td>' . $Qproducts->value('date_last_modified') . '</td>' .
                        '      <td align="center">' . osc_icon(((int)$data['products_status'] === 1) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif', null, null) . '</td>' .
                        '    </tr>';
      }

      $this->_data .= '  </tbody>' .
                      '</table>';

      $Qproducts->freeResult();
    }
  }
?>

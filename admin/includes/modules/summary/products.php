<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

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

      $Qproducts = $osC_Database->query('select p.products_id, pd.products_name, p.products_price, greatest(p.products_date_added, p.products_last_modified) as date_last_modified, p.products_status from :table_products p, :table_products_description pd where p.products_id = pd.products_id and pd.language_id = :language_id order by date_last_modified desc, pd.products_name limit 6');
      $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
      $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
      $Qproducts->bindInt(':language_id', $osC_Language->getID());
      $Qproducts->execute();

      while ( $Qproducts->next() ) {
        $this->_data .= '    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">' .
                        '      <td>' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, 'products&pID=' . $Qproducts->valueInt('products_id') . '&action=save'), osc_icon('products.png') . '&nbsp;' . $Qproducts->value('products_name')) . '</td>' .
                        '      <td>' . $osC_Currencies->format($Qproducts->value('products_price')) . '</td>' .
                        '      <td>' . $Qproducts->value('date_last_modified') . '</td>' .
                        '      <td align="center">' . osc_icon(($Qproducts->valueInt('products_status') === 1) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif', null, null) . '</td>' .
                        '    </tr>';
      }

      $this->_data .= '  </tbody>' .
                      '</table>';

      $Qproducts->freeResult();
    }
  }
?>

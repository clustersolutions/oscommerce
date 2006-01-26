<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  if (!class_exists('osC_Summary')) {
    include('includes/classes/summary.php');
  }

  if (!defined('MODULE_SUMMARY_PRODUCTS_TITLE')) {
    $osC_Language->loadConstants('modules/summary/products.php');
  }

  class osC_Summary_products extends osC_Summary {

/* Class constructor */

    function osC_Summary_products() {
      $this->_title = MODULE_SUMMARY_PRODUCTS_TITLE;
      $this->_title_link = tep_href_link(FILENAME_PRODUCTS);

      $this->_setData();
    }

/* Private methods */

    function _setData() {
      global $osC_Database, $osC_Language, $osC_Currencies, $template;

      if (!isset($osC_Currencies)) {
        if (!class_exists('osC_Currencies')) {
          include('../includes/classes/currencies.php');
        }

        $osC_Currencies = new osC_Currencies();
      }

      $this->_data = '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">' .
                     '  <thead>' .
                     '    <tr>' .
                     '      <th>' . MODULE_SUMMARY_PRODUCTS_HEADING_PRODUCTS . '</th>' .
                     '      <th>' . MODULE_SUMMARY_PRODUCTS_HEADING_PRICE . '</th>' .
                     '      <th>' . MODULE_SUMMARY_PRODUCTS_HEADING_DATE . '</th>' .
                     '      <th>' . MODULE_SUMMARY_PRODUCTS_HEADING_STATUS . '</th>' .
                     '    </tr>' .
                     '  </thead>' .
                     '  <tbody>';

      $Qproducts = $osC_Database->query('select p.products_id, pd.products_name, p.products_price, greatest(p.products_date_added, p.products_last_modified) as date_last_modified, p.products_status from :table_products p, :table_products_description pd where p.products_id = pd.products_id and pd.language_id = :language_id order by date_last_modified desc, pd.products_name limit 6');
      $Qproducts->bindTable(':table_products', TABLE_PRODUCTS);
      $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
      $Qproducts->bindInt(':language_id', $osC_Language->getID());
      $Qproducts->execute();

      while ($Qproducts->next()) {
        $this->_data .= '    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">' .
                        '      <td><a href="' . tep_href_link(FILENAME_PRODUCTS, 'pID=' . $Qproducts->valueInt('products_id') . '&action=new_product') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/products.png', ICON_PREVIEW, '16', '16') . '&nbsp;' . $Qproducts->value('products_name') . '</a></td>' .
                        '      <td>' . $osC_Currencies->format($Qproducts->value('products_price')) . '</td>' .
                        '      <td>' . $Qproducts->value('date_last_modified') . '</td>' .
                        '      <td align="center">' . tep_image('templates/' . $template . '/images/icons/' . (($Qproducts->valueInt('products_status') === 1) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif')) . '</td>' .
                        '    </tr>';
      }

      $Qproducts->freeResult();

      $this->_data .= '  </tbody>' .
                      '</table>';
    }
  }
?>

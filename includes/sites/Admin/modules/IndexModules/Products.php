<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Admin\Module\IndexModules;

  use osCommerce\OM\Registry;
  use osCommerce\OM\OSCOM;
  use osCommerce\OM\Access;

  require('includes/sites/Admin/applications/products/classes/products.php');

  class Products extends \osCommerce\OM\Site\Admin\IndexModulesAbstract {
    public function __construct() {
      Registry::get('Language')->loadIniFile('modules/IndexModules/Products.php');

      $this->_title = OSCOM::getDef('admin_indexmodules_products_title');
      $this->_title_link = OSCOM::getLink(null, 'Products');

      if ( Access::hasAccess(OSCOM::getSite(), 'Products') ) {
        if ( !isset($osC_Currencies) ) {
          if ( !class_exists('osC_Currencies') ) {
            include('includes/classes/currencies.php');
          }

          $osC_Currencies = new osC_Currencies();
        }

        $this->_data = '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">' .
                       '  <thead>' .
                       '    <tr>' .
                       '      <th>' . OSCOM::getDef('admin_indexmodules_products_table_heading_products') . '</th>' .
                       '      <th>' . OSCOM::getDef('admin_indexmodules_products_table_heading_price') . '</th>' .
                       '      <th>' . OSCOM::getDef('admin_indexmodules_products_table_heading_date') . '</th>' .
                       '      <th>' . OSCOM::getDef('admin_indexmodules_products_table_heading_status') . '</th>' .
                       '    </tr>' .
                       '  </thead>' .
                       '  <tbody>';

        $Qproducts = Registry::get('Database')->query('select products_id, greatest(products_date_added, products_last_modified) as date_last_modified from :table_products where parent_id is null order by date_last_modified desc limit 6');
        $Qproducts->execute();

        $counter = 0;

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

          $this->_data .= '    <tr onmouseover="$(this).addClass(\'mouseOver\');" onmouseout="$(this).removeClass(\'mouseOver\');"' . ($counter % 2 ? ' class="alt"' : '') . '>' .
                          '      <td>' . osc_link_object(OSCOM::getLink(null, 'Products', 'id=' . (int)$data['products_id'] . '&action=save'), $products_icon . '&nbsp;' . osc_output_string_protected($data['products_name'])) . '</td>' .
                          '      <td>' . ( !empty($data['variants']) ? 'from ' : '' ) . $osC_Currencies->format($products_price) . '</td>' .
                          '      <td>' . $Qproducts->value('date_last_modified') . '</td>' .
                          '      <td align="center">' . osc_icon(((int)$data['products_status'] === 1) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif', null, null) . '</td>' .
                          '    </tr>';

          $counter++;
        }

        $this->_data .= '  </tbody>' .
                        '</table>';
      }
    }
  }
?>

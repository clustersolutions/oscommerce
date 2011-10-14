<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Module\Dashboard;

  use osCommerce\OM\Core\Access;
  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Shop\Currencies;

  class Products extends \osCommerce\OM\Core\Site\Admin\IndexModulesAbstract {
    public function __construct() {
      $OSCOM_Language = Registry::get('Language');

      $OSCOM_Language->loadIniFile('modules/Dashboard/Products.php');

      $this->_title = OSCOM::getDef('admin_indexmodules_products_title');
      $this->_title_link = OSCOM::getLink(null, 'Products');

      if ( Access::hasAccess(OSCOM::getSite(), 'Products') ) {
        if ( !Registry::exists('Currencies') ) {
          Registry::set('Currencies', new Currencies());
        }

        $OSCOM_Currencies = Registry::get('Currencies');

        $data = array('language_id' => $OSCOM_Language->getID(),
                      'batch_pageset' => 1,
                      'batch_max_results' => 6);

        $result = OSCOM::callDB('Admin\Products\GetAll', $data);

        foreach ( $result['entries'] as &$p ) {
          if ( $p['has_children'] === 1 ) {
            $p['products_price_formatted'] = $OSCOM_Currencies->format($p['products_price_min']);

            if ( $p['products_price_min'] != $p['products_price_max'] ) {
              $p['products_price_formatted'] .= ' - ' . $OSCOM_Currencies->format($p['products_price_max']);
            }

            $p['products_quantity'] = '(' . $p['products_quantity_variants'] . ')';
          } else {
            $p['products_price_formatted'] = $OSCOM_Currencies->format($p['products_price']);
          }
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

/*
        $Qproducts = Registry::get('PDO')->query('select products_id, greatest(products_date_added, products_last_modified) as date_last_modified from :table_products where parent_id is null order by date_last_modified desc limit 6');
        $Qproducts->execute();

        $counter = 0;

        while ( $Qproducts->fetch() ) {
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
*/

        $counter = 0;

        foreach ( $result['entries'] as $p ) {
          if ( $p['has_children'] === 1 ) {
            $products_icon = HTML::icon('products.png');
          } else {
            $products_icon = HTML::icon('attach.png');
          }

          $this->_data .= '    <tr onmouseover="$(this).addClass(\'mouseOver\');" onmouseout="$(this).removeClass(\'mouseOver\');"' . ($counter % 2 ? ' class="alt"' : '') . '>' .
                          '      <td>' . HTML::link(OSCOM::getLink(null, 'Products', 'Save&id=' . (int)$p['products_id']), $products_icon . '&nbsp;' . HTML::outputProtected($p['products_name'])) . '</td>' .
                          '      <td>' . $p['products_price_formatted'] . '</td>' .
                          '      <td>' . $p['products_last_modified'] . '</td>' .
                          '      <td align="center">' . HTML::icon(((int)$p['products_status'] === 1) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif', null, null) . '</td>' .
                          '    </tr>';

          $counter++;
        }

        $this->_data .= '  </tbody>' .
                        '</table>';
      }
    }
  }
?>

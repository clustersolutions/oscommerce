<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Products\Model;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.3
 */

  class getAll {
    public static function execute($category_id = null, $pageset = 1) {
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_CategoryTree = Registry::get('CategoryTree');
      $OSCOM_Currencies = Registry::get('Currencies');

      if ( !is_numeric($category_id) ) {
        $category_id = 0;
      }

      $data = array('language_id' => $OSCOM_Language->getID(),
                    'batch_pageset' => $pageset,
                    'batch_max_results' => MAX_DISPLAY_SEARCH_RESULTS);

      if ( !is_numeric($data['batch_pageset']) || (floor($data['batch_pageset']) != $data['batch_pageset']) ) {
        $data['batch_pageset'] = 1;
      }

      if ( $category_id > 0 ) {
        $OSCOM_CategoryTree->reset();
        $OSCOM_CategoryTree->setBreadcrumbUsage(false);

        $in_categories = array($category_id);

        foreach ( $OSCOM_CategoryTree->getArray($category_id) as $category ) {
          $in_categories[] = $category['id'];
        }

        $data['categories'] = $in_categories;
      }

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

      return $result;
    }
  }
?>

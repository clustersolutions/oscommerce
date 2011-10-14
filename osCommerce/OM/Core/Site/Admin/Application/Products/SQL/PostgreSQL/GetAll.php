<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Products\SQL\PostgreSQL;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.3
 */

  class GetAll {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $result = array();

      if ( isset($data['categories']) ) {
        $data['categories'] = array_map('intval', $data['categories']);

        $sql_query = 'select distinct p.*, pd.products_name from :table_products p, :table_products_description pd, :table_products_to_categories p2c where p.parent_id is null and p.products_id = pd.products_id and pd.language_id = :language_id and p.products_id = p2c.products_id and p2c.categories_id in (' . implode(',', $data['categories']) . ')';
      } else {
        $sql_query = 'select p.*, pd.products_name from :table_products p, :table_products_description pd where p.parent_id is null and p.products_id = pd.products_id and pd.language_id = :language_id';
      }

      $sql_query .= ' order by pd.products_name';

      if ( $data['batch_pageset'] !== -1 ) {
        $sql_query .= ' limit :batch_max_results offset :batch_pageset';
      }

      $Qproducts = $OSCOM_PDO->prepare($sql_query);
      $Qproducts->bindInt(':language_id', $data['language_id']);

      if ( $data['batch_pageset'] !== -1 ) {
        $Qproducts->bindInt(':batch_pageset', $OSCOM_PDO->getBatchFrom($data['batch_pageset'], $data['batch_max_results']));
        $Qproducts->bindInt(':batch_max_results', $data['batch_max_results']);
      }

      $Qproducts->execute();

      $result['entries'] = $Qproducts->fetchAll();

      if ( isset($data['categories']) ) {
        $sql_query = 'select count(distinct p.products_id) from :table_products p, :table_products_description pd, :table_products_to_categories p2c where p.parent_id is null and p.products_id = pd.products_id and pd.language_id = :language_id and p.products_id = p2c.products_id and p2c.categories_id in (' . implode(',', $data['categories']) . ')';
      } else {
        $sql_query = 'select count(*) from :table_products p, :table_products_description pd where p.parent_id is null and p.products_id = pd.products_id and pd.language_id = :language_id';
      }

      $Qtotal = $OSCOM_PDO->prepare($sql_query);
      $Qtotal->bindInt(':language_id', $data['language_id']);
      $Qtotal->execute();

      $result['total'] = $Qtotal->fetchColumn();

      foreach ( $result['entries'] as &$p ) {
        $p['products_status'] = (intval($p['products_status']) === 1);
        $p['has_children'] = intval($p['has_children']);

        if ( $p['has_children'] === 1 ) {
          $Qvariants = $OSCOM_PDO->prepare('select min(products_price) as min_price, max(products_price) as max_price, sum(products_quantity) as total_quantity, min(products_status) as products_status from :table_products where parent_id = :parent_id');
          $Qvariants->bindInt(':parent_id', $p['products_id']);
          $Qvariants->execute();

          $p['products_status'] = ($Qvariants->valueInt('products_status') === 1);
          $p['products_quantity_variants'] = $Qvariants->valueInt('total_quantity');
          $p['products_price_min'] = $Qvariants->value('min_price');
          $p['products_price_max'] = $Qvariants->value('max_price');
        }
      }

      return $result;
    }
  }
?>

<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Products\SQL\ANSI;

  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.3
 */

  class GetProductVariants {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qvariants = $OSCOM_PDO->prepare('select * from :table_products where parent_id = :parent_id');
      $Qvariants->bindInt(':parent_id', $data['id']);
      $Qvariants->execute();

      $result = $Qvariants->fetchAll();

      foreach ( $result as &$p ) {
        $Qcombos = $OSCOM_PDO->prepare('select pv.default_combo, pvg.id as group_id, pvg.title as group_title, pvv.id as value_id, pvv.title as value_title from :table_products_variants pv, :table_products_variants_groups pvg, :table_products_variants_values pvv where pv.products_id = :products_id and pv.products_variants_values_id = pvv.id and pvv.languages_id = :languages_id and pvv.products_variants_groups_id = pvg.id and pvg.languages_id = :languages_id order by pvg.sort_order, pvg.title');
        $Qcombos->bindInt(':products_id', $p['products_id']);
        $Qcombos->bindInt(':languages_id', $data['language_id']);
        $Qcombos->bindInt(':languages_id', $data['language_id']);
        $Qcombos->execute();

        $p['combos'] = $Qcombos->fetchAll();

        $p['default'] = ( (int)$p['combos'][0]['default_combo'] === 1 );

        for ( $i=0,$n=count($p['combos']); $i<$n; $i++ ) {
          unset($p['combos'][$i]['default_combo']);
        }
      }

      return $result;
    }
  }
?>

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

  class get {
    public static function execute($id) {
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_PDO = Registry::get('PDO');

      $Qproducts = $OSCOM_PDO->prepare('select p.*, pd.* from :table_products p, :table_products_description pd where p.products_id = :products_id and p.products_id = pd.products_id and pd.language_id = :language_id');
      $Qproducts->bindInt(':products_id', $id);
      $Qproducts->bindInt(':language_id', $OSCOM_Language->getID());
      $Qproducts->execute();

      $data = $Qproducts->toArray();

      $variants_array = array();

      if ( $data['has_children'] == '1' ) {
        $Qsubproducts = $OSCOM_PDO->prepare('select * from :table_products where parent_id = :parent_id and products_status = :products_status');
        $Qsubproducts->bindInt(':parent_id', $data['products_id']);
        $Qsubproducts->bindInt(':products_status', 1);
        $Qsubproducts->execute();

        while ( $Qsubproducts->fetch() ) {
          $variants_array[$Qsubproducts->valueInt('products_id')]['data'] = array('price' => $Qsubproducts->value('products_price'),
                                                                                  'tax_class_id' => $Qsubproducts->valueInt('products_tax_class_id'),
                                                                                  'model' => $Qsubproducts->value('products_model'),
                                                                                  'quantity' => $Qsubproducts->value('products_quantity'),
                                                                                  'weight' => $Qsubproducts->value('products_weight'),
                                                                                  'weight_class_id' => $Qsubproducts->valueInt('products_weight_class'),
                                                                                  'availability_shipping' => 1);

          $Qvariants = $OSCOM_PDO->prepare('select pv.default_combo, pvg.id as group_id, pvg.title as group_title, pvg.module, pvv.id as value_id, pvv.title as value_title, pvv.sort_order as value_sort_order from :table_products_variants pv, :table_products_variants_groups pvg, :table_products_variants_values pvv where pv.products_id = :products_id and pv.products_variants_values_id = pvv.id and pvv.languages_id = :languages_id and pvv.products_variants_groups_id = pvg.id and pvg.languages_id = :languages_id order by pvg.sort_order, pvg.title');
          $Qvariants->bindInt(':products_id', $Qsubproducts->valueInt('products_id'));
          $Qvariants->bindInt(':languages_id', $OSCOM_Language->getID());
          $Qvariants->bindInt(':languages_id', $OSCOM_Language->getID());
          $Qvariants->execute();

          while ( $Qvariants->fetch() ) {
            $variants_array[$Qsubproducts->valueInt('products_id')]['values'][$Qvariants->valueInt('group_id')][$Qvariants->valueInt('value_id')] = array('value_id' => $Qvariants->valueInt('value_id'),
                                                                                                                                                          'group_title' => $Qvariants->value('group_title'),
                                                                                                                                                          'value_title' => $Qvariants->value('value_title'),
                                                                                                                                                          'sort_order' => $Qvariants->value('value_sort_order'),
                                                                                                                                                          'default' => (bool)$Qvariants->valueInt('default_combo'),
                                                                                                                                                          'module' => $Qvariants->value('module'));
          }
        }
      }

      $data['variants'] = $variants_array;

      $Qattributes = $OSCOM_PDO->prepare('select id, value from :table_product_attributes where products_id = :products_id and languages_id in (0, :languages_id)');
      $Qattributes->bindInt(':products_id', $id);
      $Qattributes->bindInt(':languages_id', $OSCOM_Language->getID());
      $Qattributes->execute();

      $attributes_array = array();

      while ( $Qattributes->fetch() ) {
        $attributes_array[$Qattributes->valueInt('id')] = $Qattributes->value('value');
      }

      $data['attributes'] = $attributes_array;

      return $data;
    }
  }
?>

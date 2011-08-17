<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Module\ProductAttribute;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

/**
 * @since v3.0.3
 */

  class ShippingAvailability extends \osCommerce\OM\Core\Site\Admin\ProductAttributeModuleAbstract {
    public function setFunction($value) {
      $OSCOM_Language = Registry::get('Language');
      $OSCOM_PDO = Registry::get('PDO');

      $array = array();

      $Qstatus = $OSCOM_PDO->prepare('select id, title from :table_shipping_availability where languages_id = :languages_id order by title');
      $Qstatus->bindInt(':languages_id', $OSCOM_Language->getID());
      $Qstatus->execute();

      while ( $Qstatus->next() ) {
        $array[] = array('id' => $Qstatus->valueInt('id'),
                         'text' => $Qstatus->value('title'));
      }

      return HTML::selectMenu('attributes[' . self::getID() . ']', $array, $value);
    }
  }
?>

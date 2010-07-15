<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Module\Box\ManufacturerInfo;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Controller extends \osCommerce\OM\Core\Modules {
    var $_title,
        $_code = 'ManufacturerInfo',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Box';

    public function __construct() {
      $this->_title = OSCOM::getDef('box_manufacturer_info_heading');
    }

    public function initialize() {
      $OSCOM_Product = ( Registry::exists('Product') ) ? Registry::get('Product') : null;
      $OSCOM_Database = Registry::get('Database');
      $OSCOM_Language = Registry::get('Language');

      if ( isset($OSCOM_Product) && ($OSCOM_Product instanceof \osCommerce\OM\Site\Shop\Product) && $OSCOM_Product->isValid() ) {
        $Qmanufacturer = $OSCOM_Database->query('select m.manufacturers_id, m.manufacturers_name, m.manufacturers_image, mi.manufacturers_url from :table_manufacturers m left join :table_manufacturers_info mi on (m.manufacturers_id = mi.manufacturers_id and mi.languages_id = :languages_id), :table_products p  where p.products_id = :products_id and p.manufacturers_id = m.manufacturers_id');
        $Qmanufacturer->bindInt(':languages_id', $OSCOM_Language->getID());
        $Qmanufacturer->bindInt(':products_id', $OSCOM_Product->getID());
        $Qmanufacturer->execute();

        if ( $Qmanufacturer->numberOfRows() ) {
          $this->_content = '';

          if ( !osc_empty($Qmanufacturer->value('manufacturers_image')) ) {
            $this->_content .= '<div style="text-align: center;">' .
                               osc_link_object(OSCOM::getLink(null, 'Index', 'Manufacturers=' . $Qmanufacturer->valueInt('manufacturers_id')), osc_image(DIR_WS_IMAGES . 'manufacturers/' . $Qmanufacturer->value('manufacturers_image'), $Qmanufacturer->value('manufacturers_name'))) .
                               '</div>';
          }

          $this->_content .= '<ol style="list-style: none; margin: 0; padding: 0;">';

          if ( !osc_empty($Qmanufacturer->value('manufacturers_url')) ) {
            $this->_content .= '<li>' . osc_link_object(OSCOM::getLink(null, 'Redirct', 'Manufacturer=' . $Qmanufacturer->valueInt('manufacturers_id')), sprintf(OSCOM::getDef('box_manufacturer_info_website'), $Qmanufacturer->value('manufacturers_name')), 'target="_blank"') . '</li>';
          }

          $this->_content .= '<li>' . osc_link_object(OSCOM::getLink(null, 'Index', 'Manufacturers=' . $Qmanufacturer->valueInt('manufacturers_id')), OSCOM::getDef('box_manufacturer_info_products')) . '</li>';

          $this->_content .= '</ol>';
        }
      }
    }
  }
?>

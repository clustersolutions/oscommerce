<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop\Module\Box\Manufacturers;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  class Controller extends \osCommerce\OM\Core\Modules {
    var $_title,
        $_code = 'Manufacturers',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'Box';

    public function __construct() {
      $this->_title = OSCOM::getDef('box_manufacturers_heading');
    }

    public function initialize() {
      $OSCOM_Database = Registry::get('Database');

      $Qmanufacturers = $OSCOM_Database->query('select manufacturers_id as id, manufacturers_name as text from :table_manufacturers order by manufacturers_name');
      $Qmanufacturers->setCache('manufacturers');
      $Qmanufacturers->execute();

      $manufacturers_array = array(array('id' => '',
                                         'text' => OSCOM::getDef('pull_down_default')));

      while ( $Qmanufacturers->next() ) {
        $manufacturers_array[] = $Qmanufacturers->toArray();
      }

      $Qmanufacturers->freeResult();

      $this->_content = '<form name="manufacturers" action="' . OSCOM::getLink() . '" method="get">' . osc_draw_hidden_field('Index', null) .
                        osc_draw_pull_down_menu('Manufacturers', $manufacturers_array, null, 'onchange="this.form.submit();" size="' . BOX_MANUFACTURERS_LIST_SIZE . '" style="width: 100%"') . osc_draw_hidden_session_id_field() .
                        '</form>';
    }

    function install() {
      $OSCOM_Database = Registry::get('Database');

      parent::install();

      $OSCOM_Database->simpleQuery("insert into :table_configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Manufacturers List Size', 'BOX_MANUFACTURERS_LIST_SIZE', '1', 'The size of the manufacturers pull down menu listing.', '6', '0', now())");
    }

    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('BOX_MANUFACTURERS_LIST_SIZE');
      }

      return $this->_keys;
    }
  }
?>

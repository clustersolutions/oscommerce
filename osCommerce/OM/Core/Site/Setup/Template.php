<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Setup;

  use osCommerce\OM\Core\OSCOM;

  class Template extends \osCommerce\OM\Core\Template {
    public function __construct() {
      $this->set('default');
    }

    public static function getTemplates() {
      return array(array('id' => 0,
                         'code' => 'default'));
    }

    public function set($code = null) {
      if ( !isset($_SESSION[OSCOM::getSite()]['template']) ) {
        $data = array();

        foreach ( $this->getTemplates() as $template ) {
          $data = array('id' => $template['id'],
                        'code' => $template['code']);
        }

        $_SESSION[OSCOM::getSite()]['template'] = $data;
      }

      $this->_template_id = $_SESSION[OSCOM::getSite()]['template']['id'];
      $this->_template = $_SESSION[OSCOM::getSite()]['template']['code'];
    }
  }
?>

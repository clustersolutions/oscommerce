<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Admin;

  use osCommerce\OM\OSCOM;

  class Template extends \osCommerce\OM\Template {
    public function __construct() {
      $this->set('oscom');
    }

    public function getIcon($size = 16, $icon = null, $title = null) {
      if ( empty($icon) ) {
        $icon = $this->_application->getIcon();
      }

      return '<img src="' . OSCOM::getPublicSiteLink('images/applications/' . (int)$size . '/' . $icon) . '" border="0" alt="" title="' . osc_output_string_protected($title) . '" width="' . (int)$size . '" height="' . (int)$size . '" />';
    }
  }
?>

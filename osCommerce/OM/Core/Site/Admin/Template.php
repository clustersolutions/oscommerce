<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;

  class Template extends \osCommerce\OM\Core\Template {
    public function __construct() {
      $this->set('oscom');
    }

    public function getIcon($size = 16, $icon = null, $title = null) {
      if ( !isset($icon) ) {
        $icon = $this->_application->getIcon();
      }

      return HTML::image(OSCOM::getPublicSiteLink('images/applications/' . $size . '/' . $icon), $title, $size, $size);
    }
  }
?>

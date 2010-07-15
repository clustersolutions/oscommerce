<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Site\Setup\Application\Index;

  use osCommerce\OM\OSCOM;

  class Controller extends \osCommerce\OM\Site\Setup\ApplicationAbstract {
    protected function initialize() {
      $this->_page_title = OSCOM::getDef('heading_title');
    }
  }
?>

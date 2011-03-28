<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\PDO\MySQL;

  class V5 extends \osCommerce\OM\Core\PDO\MySQL\Standard {
    protected $_has_native_fk = true;
    protected $_driver_parent = 'MySQL\\Standard';
  }
?>

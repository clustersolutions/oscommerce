<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\PDO\MySQL;

  class V5 extends \osCommerce\OM\Core\PDO\MySQL\Standard {
    protected $_has_native_fk = true;
    protected $_driver_parent = 'MySQL\\Standard';
  }
?>

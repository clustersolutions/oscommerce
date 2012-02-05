<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop;

  use osCommerce\OM\Core\Registry;

  abstract class ApplicationAbstract extends \osCommerce\OM\Core\ApplicationAbstract {
    abstract protected function initialize();

    public function __construct() {
      $this->ignoreAction(Registry::get('Session')->getName());

      parent::__construct();
    }
  }
?>

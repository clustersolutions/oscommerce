<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Shop;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  abstract class ApplicationAbstract extends \osCommerce\OM\Core\ApplicationAbstract {
    public function __construct($process = true) {
      $this->ignoreAction(Registry::get('Session')->getName());

      $this->initialize();

      if ( $process === true ) {
        $this->process();

        $this->runActions();
      }
    }
  }
?>

<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Module\Template\Value\total_shortcuts;

  use osCommerce\OM\Core\Access;
  use osCommerce\OM\Core\OSCOM;

  class Controller extends \osCommerce\OM\Core\Template\ValueAbstract {
    static public function execute() {
      $total_shortcuts = 0;

      if ( isset($_SESSION[OSCOM::getSite()]['id']) && Access::hasShortcut() ) {
        $total_shortcuts = count(Access::getShortcuts());
      }

      return $total_shortcuts;
    }
  }
?>

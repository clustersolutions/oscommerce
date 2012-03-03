<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Configuration\Action;

  use osCommerce\OM\Core\ApplicationAbstract;

  class EntrySave {
    public static function execute(ApplicationAbstract $application) {
      $application->setPageContent('entries_edit.html');
    }
  }
?>

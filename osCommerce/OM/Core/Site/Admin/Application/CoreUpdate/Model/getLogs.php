<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\Model;

  use \GlobIterator;

  use osCommerce\OM\Core\OSCOM;

  class getLogs {
    public static function execute() {
      $result = array();

      $it = new GlobIterator(OSCOM::BASE_DIRECTORY . 'Work/Logs/update-*.txt');

      foreach ( $it as $f ) {
        $result[] = $f->getFilename();
      }

      return $result;
    }
  }
?>

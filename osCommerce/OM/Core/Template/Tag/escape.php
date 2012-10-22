<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Template\Tag;

  use osCommerce\OM\Core\HTML;

  class escape extends \osCommerce\OM\Core\Template\Tag\value {
    static public function execute($string) {
      return HTML::outputProtected(parent::execute($string));
    }
  }
?>

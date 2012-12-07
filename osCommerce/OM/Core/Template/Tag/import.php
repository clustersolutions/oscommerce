<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Template\Tag;

  use osCommerce\OM\Core\Registry;

  class import extends \osCommerce\OM\Core\Template\TagAbstract {
    static public function execute($file) {
      if ( !empty($file) ) {
        if ( file_exists($file) ) {
// use only file_get_contents() when content pages no longer contain PHP; HPDL
          if ( substr($file, strrpos($file, '.')+1) == 'html' ) {
            return file_get_contents($file);
          } else {
            return Registry::get('Template')->getContent($file);
          }
        } else {
          trigger_error('Template Tag {import}: File does not exist: ' . $file);
        }
      }

      return false;
    }
  }
?>

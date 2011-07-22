<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Categories\Model;

  use osCommerce\OM\Core\Cache;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Admin\CategoryTree;
  use osCommerce\OM\Core\Upload;

  class save {
    public static function execute($id = null, $data) {
      if ( Registry::exists('CategoryTree') ) {
        $OSCOM_CategoryTree = Registry::get('CategoryTree');
      } else {
        $OSCOM_CategoryTree = new CategoryTree();
        Registry::set('CategoryTree', $OSCOM_CategoryTree);
      }

      if ( is_numeric($id) ) {
        $data['id'] = $id;
      }

// Prevent another big bang and check if category is not being moved to a child category
      if ( isset($data['id']) && ($OSCOM_CategoryTree->getParentID($data['id']) != $data['parent_id']) ) {
        if ( in_array($data['id'], explode('_', $OSCOM_CategoryTree->buildBreadcrumb($data['parent_id']))) ) {
          return false;
        }
      }

      if ( isset($data['upload_image']) ) {
        $image = new Upload($data['upload_image'], OSCOM::getConfig('dir_fs_public', 'OSCOM') . 'categories');

        if ( $image->exists() && $image->parse() && $image->save() ) {
          $data['image'] = $image->getFilename();
        }
      }

      if ( OSCOM::callDB('Admin\Categories\Save', $data) ) {
        Cache::clear('categories');
        Cache::clear('category_tree');
        Cache::clear('also_purchased');

        return true;
      }

      return false;
    }
  }
?>

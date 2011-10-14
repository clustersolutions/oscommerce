<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Products;

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Admin\CategoryTree;
  use osCommerce\OM\Core\Site\Admin\Image;
  use osCommerce\OM\Core\Site\Shop\Currencies;

/**
 * @since v3.0.3
 */

  class Controller extends \osCommerce\OM\Core\Site\Admin\ApplicationAbstract {
    protected $_group = 'products';
    protected $_icon = 'products.png';
    protected $_sort_order = 100;

    protected $_category_id = 0;
    protected $_tree = array();

    protected function initialize() {
      $this->_title = OSCOM::getDef('app_title');
    }

    protected function process() {
      $OSCOM_MessageStack = Registry::get('MessageStack');

      $this->_page_title = OSCOM::getDef('heading_title');

      if ( isset($_GET['cid']) && is_numeric($_GET['cid']) ) {
        $this->_category_id = $_GET['cid'];
      }

      $this->_tree = new CategoryTree();
      Registry::set('CategoryTree', $this->_tree);

      Registry::set('Currencies', new Currencies());

// check if the products image directory exists
      if ( is_dir(OSCOM::getConfig('dir_fs_public', 'OSCOM') . 'products') ) {
        if ( !is_writeable(OSCOM::getConfig('dir_fs_public', 'OSCOM') . 'products') ) {
          $OSCOM_MessageStack->add('header', sprintf(OSCOM::getDef('ms_error_image_directory_not_writable'), OSCOM::getConfig('dir_fs_public', 'OSCOM') . 'products'), 'error');
        }
      } else {
        $OSCOM_MessageStack->add('header', sprintf(OSCOM::getDef('ms_error_image_directory_non_existant'), OSCOM::getConfig('dir_fs_public', 'OSCOM') . 'products'), 'error');
      }

// check for imagemagick or GD
      if ( !Image::hasGDSupport() && ((strlen(CFG_APP_IMAGEMAGICK_CONVERT) < 1) || !file_exists(CFG_APP_IMAGEMAGICK_CONVERT)) ) {
        $OSCOM_MessageStack->add('header', OSCOM::getDef('ms_warning_image_processor_not_available'), 'warning');
      }
    }

    public function getCurrentCategoryID() {
      return $this->_category_id;
    }

    public function getTree() {
      return $this->_tree;
    }

    public function getCategoryList() {
      $CT = $this->_tree;

      $CT->reset();
      $CT->setSpacerString('&nbsp;');

      $categories_array = array();

      foreach ( $CT->getArray() as $value ) {
        $cpath = explode('_', $value['id']); // end() only accepts variables

        $categories_array[] = array('id' => end($cpath),
                                    'text' => $value['title']);
      }

      return $categories_array;
    }
  }
?>

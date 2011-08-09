<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Products;

/**
 * @since v3.0.3
 */

  class Products extends \osCommerce\OM\Core\ApplicationModelAbstract { }

/*
  require('includes/applications/products/classes/products.php');
  require('includes/applications/product_attributes/classes/product_attributes.php');
  require('../includes/classes/variants.php');

  class osC_Application_Products extends osC_Template_Admin {

    protected $_module = 'products',
              $_page_title,
              $_page_contents = 'main.php';

    function __construct() {
      global $osC_Language, $osC_MessageStack, $osC_Currencies, $osC_Tax, $osC_CategoryTree, $osC_Image, $current_category_id;

      $this->_page_title = $osC_Language->get('heading_title');

      $current_category_id = 0;

      if ( isset($_GET['cID']) && is_numeric($_GET['cID']) ) {
        $current_category_id = $_GET['cID'];
      } else {
        $_GET['cID'] = $current_category_id;
      }

      require('../includes/classes/currencies.php');
      $osC_Currencies = new osC_Currencies();

      require('includes/classes/tax.php');
      $osC_Tax = new osC_Tax_Admin();

      require('includes/classes/category_tree.php');
      $osC_CategoryTree = new osC_CategoryTree_Admin();
      $osC_CategoryTree->setSpacerString('&nbsp;', 2);

      require('includes/classes/image.php');
      $osC_Image = new osC_Image_Admin();

// check if the catalog image directory exists
      if (is_dir(realpath('../images/products'))) {
        if (!is_writeable(realpath('../images/products'))) {
          $osC_MessageStack->add('header', sprintf($osC_Language->get('ms_error_image_directory_not_writable'), realpath('../images/products')), 'error');
        }
      } else {
        $osC_MessageStack->add('header', sprintf($osC_Language->get('ms_error_image_directory_non_existant'), realpath('../images/products')), 'error');
      }
    }
  }
*/
?>

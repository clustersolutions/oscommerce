<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Boxes_specials extends osC_Modules {
    var $_title = 'Specials',
        $_code = 'specials',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'boxes';

    function osC_Boxes_specials() {
//      $this->_title = BOX_HEADING_SPECIALS;
      $this->_title_link = tep_href_link(FILENAME_PRODUCTS, 'specials');
    }

    function initialize() {
      global $osC_Database, $osC_Services, $osC_Currencies, $osC_Cache;

      if ($osC_Services->isStarted('specials')) {
        if ((BOX_SPECIALS_CACHE > 0) && $osC_Cache->read('box-specials-' . $_SESSION['language'], BOX_SPECIALS_CACHE)) {
          $data = $osC_Cache->getCache();
        } else {
          $Qspecials = $osC_Database->query('select p.products_id, p.products_price, p.products_tax_class_id, p.products_image, pd.products_name, pd.products_keyword, s.specials_new_products_price from :table_products p, :table_products_description pd, :table_specials s where s.status = 1 and s.products_id = p.products_id and p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id order by s.specials_date_added desc limit :max_random_select_specials');
          $Qspecials->bindTable(':table_products', TABLE_PRODUCTS);
          $Qspecials->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
          $Qspecials->bindTable(':table_specials', TABLE_SPECIALS);
          $Qspecials->bindInt(':language_id', $_SESSION['languages_id']);
          $Qspecials->bindInt(':max_random_select_specials', BOX_SPECIALS_RANDOM_SELECT);
          $Qspecials->executeRandomMulti();

          $data = array();

          if ($Qspecials->numberOfRows()) {
            $data = $Qspecials->toArray();

            $data['products_price'] = '<s>' . $osC_Currencies->displayPrice($Qspecials->valueDecimal('products_price'), $Qspecials->valueInt('products_tax_class_id')) . '</s>&nbsp;<span class="productSpecialPrice">' . $osC_Currencies->displayPrice($Qspecials->valueDecimal('specials_new_products_price'), $Qspecials->valueInt('products_tax_class_id')) . '</span>';

            $osC_Cache->writeBuffer($data);
          }
        }

        if (empty($data) === false) {
          $this->_content = '<a href="' . tep_href_link(FILENAME_PRODUCTS, $data['products_keyword']) . '">' . tep_image(DIR_WS_IMAGES . $data['products_image'], $data['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br /><a href="' . tep_href_link(FILENAME_PRODUCTS, $data['products_keyword']) . '">' . $data['products_name'] . '</a><br />' . $data['products_price'];
        }
      }
    }

    function install() {
      global $osC_Database;

      parent::install();

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Random Product Specials Selection', 'BOX_SPECIALS_RANDOM_SELECT', '10', 'Select a random product on special from this amount of the newest products on specials available', '6', '0', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Cache Contents', 'BOX_SPECIALS_CACHE', '1', 'Number of minutes to keep the contents cached (0 = no cache)', '6', '0', now())");
    }

    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('BOX_SPECIALS_RANDOM_SELECT', 'BOX_SPECIALS_CACHE');
      }

      return $this->_keys;
    }
  }
?>

<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Image_Admin_check extends osC_Image_Admin {

// Private variables

    var $_code = 'check';

// Class constructor

    function osC_Image_Admin_check() {
      global $osC_Language;

      parent::osC_Image_Admin();

      $osC_Language->loadConstants('modules/image/check.php');

      $this->_title = MODULE_IMAGE_CHECK;
    }

// Private methods

    function _setHeader() {
      $this->_header = array(MODULE_IMAGE_CHECK_GROUPS,
                             MODULE_IMAGE_CHECK_RESULTS);
    }

    function _setData() {
      global $osC_Database;

      $counter = array();

      $Qimages = $osC_Database->query('select image from :table_products_images');
      $Qimages->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
      $Qimages->execute();

      while ($Qimages->next()) {
        foreach ($this->_groups as $group) {
          if (!isset($counter[$group['id']]['records'])) {
            $counter[$group['id']]['records'] = 0;
          }

          $counter[$group['id']]['records']++;

          if (file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES . 'products/' . $group['code'] . '/' . $Qimages->value('image'))) {
            if (!isset($counter[$group['id']]['existing'])) {
              $counter[$group['id']]['existing'] = 0;
            }

            $counter[$group['id']]['existing']++;
          }
        }
      }

      foreach ($counter as $key => $value) {
        $this->_data[] = array($this->_groups[$key]['title'],
                               $value['existing'] . ' / ' . $value['records']);
      }
    }
  }
?>
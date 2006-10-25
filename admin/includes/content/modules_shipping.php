<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  if ( !class_exists('osC_Content_Modules') ) {
    include('includes/content/modules.php');
  }

  class osC_Content_Modules_shipping extends osC_Content_Modules {

/* Private variables */

    var $_module = 'modules_shipping';

/* Class constructor */

    function osC_Content_Modules_shipping() {
      $this->_module_type = 'shipping';
      $this->_module_class = 'osC_Shipping_';

      parent::osC_Content_Modules();
    }
  }
?>

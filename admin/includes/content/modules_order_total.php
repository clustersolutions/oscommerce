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

  class osC_Content_Modules_order_total extends osC_Content_Modules {

/* Private variables */

    var $_module = 'modules_order_total';

/* Class constructor */

    function osC_Content_Modules_order_total() {
      $this->_module_type = 'order_total';
      $this->_module_class = 'osC_OrderTotal_';

      parent::osC_Content_Modules();
    }
  }
?>

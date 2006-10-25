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

  class osC_Content_Modules_payment extends osC_Content_Modules {

/* Private variables */

    var $_module = 'modules_payment';

/* Class constructor */

    function osC_Content_Modules_payment() {
      $this->_module_type = 'payment';
      $this->_module_class = 'osC_Payment_';

      parent::osC_Content_Modules();
    }
  }
?>

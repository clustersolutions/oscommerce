<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('includes/applications/products/classes/products.php');

  class osC_Application_Products_expected extends osC_Template_Admin {

/* Protected variables */

    protected $_module = 'products_expected',
              $_page_title,
              $_page_contents = 'main.php';

/* Class constructor */

    function __construct() {
      global $osC_Language, $osC_MessageStack;

      $this->_page_title = $osC_Language->get('heading_title');

      if ( !isset($_GET['action']) ) {
        $_GET['action'] = '';
      }

      if ( !isset($_GET['page']) || ( isset($_GET['page']) && !is_numeric($_GET['page']) ) ) {
        $_GET['page'] = 1;
      }

/*HPDL
      $Qcheck = $osC_Database->query('select products_id from :table_products where products_date_available is not null limit 1');
      $Qcheck->bindTable(':table_products', TABLE_PRODUCTS);
      $Qcheck->execute();

      if ($Qcheck->numberOfRows()) {
        $Qupdate = $osC_Database->query('update :table_products set products_date_available = null where unix_timestamp(now()) > unix_timestamp(products_date_available)');
        $Qupdate->bindTable(':table_products', TABLE_PRODUCTS);
        $Qupdate->execute();
      }
*/

      if ( !empty($_GET['action']) ) {
        switch ( $_GET['action'] ) {
          case 'save':
            $this->_page_contents = 'edit.php';

            if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') ) {
              $data = array('date_available' => $_POST['products_date_available']);

              if ( osC_Products_Admin::setDateAvailable($_GET['pID'], $data) ) {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_success_action_performed'), 'success');
              } else {
                $osC_MessageStack->add($this->_module, $osC_Language->get('ms_error_action_not_performed'), 'error');
              }

              osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
            }

            break;
        }
      }
    }
  }
?>

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

  class osC_Application_Login_Actions_process extends osC_Application_Login {
    public function __construct() {
      global $osC_Database, $osC_Language, $osC_MessageStack;

      parent::__construct();

      if ( !empty($_POST['user_name']) && !empty($_POST['user_password']) ) {
        $Qadmin = $osC_Database->query('select id, user_name, user_password from :table_administrators where user_name = :user_name');
        $Qadmin->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
        $Qadmin->bindValue(':user_name', $_POST['user_name']);
        $Qadmin->execute();

        if ( $Qadmin->numberOfRows() ) {
          if ( osc_validate_password($_POST['user_password'], $Qadmin->value('user_password')) ) {
            $_SESSION['admin'] = array('id' => $Qadmin->valueInt('id'),
                                       'username' => $Qadmin->value('user_name'),
                                       'access' => osC_Access::getUserLevels($Qadmin->valueInt('id')));

            $get_string = null;

            if ( isset($_SESSION['redirect_origin']) ) {
              $get_string = http_build_query($_SESSION['redirect_origin']['get']);

              unset($_SESSION['redirect_origin']);
            }

            osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $get_string));
          }
        }
      }

      $osC_MessageStack->add('header', $osC_Language->get('ms_error_login_invalid'), 'error');
    }
  }
?>

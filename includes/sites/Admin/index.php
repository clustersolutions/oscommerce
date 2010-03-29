<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  require('includes/application_top.php');
  require('includes/classes/template.php');

  $_SESSION['module'] = 'index';

  if ( !empty($_GET) ) {
    $first_array = array_slice($_GET, 0, 1);
    $_module = osc_sanitize_string(basename(key($first_array)));

    if ( file_exists('includes/applications/' . $_module . '/' . $_module . '.php') ) {
      $_SESSION['module'] = $_module;
    }
  }

  if ( !osC_Access::hasAccess($_SESSION['module']) ) {
    $osC_MessageStack->add('header', 'No access.', 'error');

    osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT));
  }

  $osC_Language->loadIniFile($_SESSION['module'] . '.php');

  require('includes/applications/' . $_SESSION['module'] . '/' . $_SESSION['module'] . '.php');

  $osC_Template = osC_Template_Admin::setup($_SESSION['module']);
  $osC_Template->set('default');

  require('templates/default.php');

  require('includes/application_bottom.php');
?>

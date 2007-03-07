<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $_SESSION['module'] = 'index';

  if (empty($_GET) === false) {
    $first_array = array_slice($_GET, 0, 1);
    $_module = osc_sanitize_string(basename(key($first_array)));

    if (file_exists('includes/content/' . $_module . '.php')) {
      $_SESSION['module'] = $_module;
    }
  }

  if ( !osC_Access::hasAccess($_SESSION['module']) ) {
    $osC_MessageStack->add_session('header', 'No access.', 'error');

    osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT));
  }

  $osC_Language->loadConstants($_SESSION['module'] . '.php');

  require('../includes/classes/template.php');
  require('includes/content/' . $_SESSION['module'] . '.php');

  $module_class = 'osC_Content_' . ucfirst($_SESSION['module']);

  $osC_Template = new $module_class();
  $osC_Template->set('default');

  require('templates/default.php');

  require('includes/application_bottom.php');
?>

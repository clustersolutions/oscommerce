<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (!empty($action)) {
    switch ($action) {
      case 'reset':
        if (isset($_GET['cache']) && !empty($_GET['cache'])) {
          osC_Cache::clear($_GET['cache']);
        }

        tep_redirect(tep_href_link(FILENAME_CACHE));
        break;
    }
  }

// check if the cache directory exists
  if (is_dir(DIR_FS_WORK)) {
    if (!is_writeable(DIR_FS_WORK)) $osC_MessageStack->add('header', ERROR_CACHE_DIRECTORY_NOT_WRITEABLE, 'error');
  } else {
    $osC_MessageStack->add('header', ERROR_CACHE_DIRECTORY_DOES_NOT_EXIST, 'error');
  }

  $page_contents = 'cache.php';

  require('templates/default.php');

  require('includes/application_bottom.php');
?>

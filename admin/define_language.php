<?php
/*
  $Id: define_language.php,v 1.20 2004/11/20 02:08:19 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $selected_box = 'tools';

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  $lng = (isset($_GET['lng']) ? $_GET['lng'] : $osC_Session->value('language'));

  $languages_array = array();
  $exists = false;
  foreach ($osC_Language->getAll() as $language) {
    if ($language['directory'] == $lng) {
      $exists = true;
    }

    $languages_array[] = array('id' => $language['directory'],
                               'text' => $language['name']);
  }

  if ($exists === false) {
    if (isset($_GET['lng'])) {
      tep_redirect(tep_href_link(FILENAME_DEFINE_LANGUAGE));
    }
  }

  if (!empty($action)) {
    switch ($action) {
      case 'save':
        if (isset($_GET['file']) && !empty($_GET['file'])) {
          $file = realpath('../includes/languages/' . $lng . '/' . $_GET['file']);

          if (substr($file, 0, strlen(realpath('../includes/languages'))) != realpath('../includes/languages')) {
            $file = false;
          }

          if (file_exists($file)) {
            $new_file = fopen($file, 'w');
            $file_contents = $_POST['contents'];
            fwrite($new_file, $file_contents, strlen($file_contents));
            fclose($new_file);
          }

          tep_redirect(tep_href_link(FILENAME_DEFINE_LANGUAGE, 'lng=' . $lng));
        }
        break;
    }
  }

  switch ($action) {
    case 'edit': $page_contents = 'define_language_edit.php'; break;
    default: $page_contents = 'define_language.php';
  }

  require('templates/default.php');

  require('includes/application_bottom.php');
?>

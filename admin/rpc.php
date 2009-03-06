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

  header('Cache-Control: no-cache, must-revalidate');
  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

  require('includes/application_top.php');

  define('RPC_STATUS_SUCCESS', 1);
  define('RPC_STATUS_NO_SESSION', -10);
  define('RPC_STATUS_NO_MODULE', -20);
  define('RPC_STATUS_NO_ACCESS', -50);
  define('RPC_STATUS_CLASS_NONEXISTENT', -60);
  define('RPC_STATUS_NO_ACTION', -70);
  define('RPC_STATUS_ACTION_NONEXISTENT', -71);

  if ( !isset($_SESSION['admin']) ) {
    echo json_encode(array('rpcStatus' => RPC_STATUS_NO_SESSION));
    exit;
  }

  $module = null;
  $class = null;

  if ( empty($_GET) ) {
    echo json_encode(array('rpcStatus' => RPC_STATUS_NO_MODULE));
    exit;
  } else {
    $first_array = array_slice($_GET, 0, 1);
    $_module = osc_sanitize_string(basename(key($first_array)));

    if ( !osC_Access::hasAccess($_module) ) {
      echo json_encode(array('rpcStatus' => RPC_STATUS_NO_ACCESS));
      exit;
    }

    $class = (isset($_GET['class']) && !empty($_GET['class'])) ? osc_sanitize_string(basename($_GET['class'])) : 'rpc';
    $action = (isset($_GET['action']) && !empty($_GET['action'])) ? osc_sanitize_string(basename($_GET['action'])) : '';

    if ( empty($action) ) {
      echo json_encode(array('rpcStatus' => RPC_STATUS_NO_ACTION));
      exit;
    }

    if ( file_exists('includes/applications/' . $_module . '/classes/' . $class . '.php')) {
      include('includes/applications/' . $_module . '/classes/' . $class . '.php');

      if ( method_exists('osC_' . ucfirst($_module) . '_Admin_' . $class, $action) ) {
        call_user_func(array('osC_' . ucfirst($_module) . '_Admin_' . $class, $action));
        exit;
      } else {
        echo json_encode(array('rpcStatus' => RPC_STATUS_ACTION_NONEXISTENT));
        exit;
      }
    } else {
      echo json_encode(array('rpcStatus' => RPC_STATUS_CLASS_NONEXISTENT));
      exit;
    }
  }

  if (isset($_GET['action']) && !empty($_GET['action'])) {
    switch ($_GET['action']) {
      case 'getImages':
        include('includes/classes/image.php');
        $osC_Image = new osC_Image_Admin();

        $array = array();

        $Qimages = $osC_Database->query('select id, image, default_flag from :table_products_images where products_id = :products_id order by sort_order');
        $Qimages->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
        $Qimages->bindInt(':products_id', $_GET['pID']);
        $Qimages->execute();

        while ($Qimages->next()) {
          foreach ($osC_Image->getGroups() as $group) {
            $pass = true;

            if (isset($_GET['filter']) && (($_GET['filter'] == 'originals') && ($group['id'] != '1'))) {
              $pass = false;
            } elseif (isset($_GET['filter']) && (($_GET['filter'] == 'others') && ($group['id'] == '1'))) {
              $pass = false;
            }

            if ($pass === true) {
              $element = array($Qimages->valueInt('id'),
                               $group['id'],
                               $Qimages->value('image'),
                               $group['code'],
                               osc_href_link($osC_Image->getAddress($Qimages->value('image'), $group['code']), null, 'NONSSL', false, false, true),
                               number_format(@filesize(DIR_FS_CATALOG . DIR_WS_IMAGES . 'products/' . $group['code'] . '/' . $Qimages->value('image'))),
                               $Qimages->valueInt('default_flag'));

              $array[] = implode('[-]', $element);
            }
          }
        }

        if (empty($array)) {
          echo '[[0|' . $_GET['pID'] . ']]';
        } else {
          echo '[[1|' . implode('[x]', $array) . ']]';
        }

        exit;
        break;

      case 'setDefaultImage':
        include('includes/classes/image.php');
        $osC_Image = new osC_Image_Admin();

        if ($osC_Image->setAsDefault($_GET['image'])) {
          echo '[[1|' . $_GET['image'] . ']]';
        } else {
          echo '[[0|' . $_GET['image'] . ']]';
        }

        exit;
        break;

      case 'reorderImages':
        include('includes/classes/image.php');
        $osC_Image = new osC_Image_Admin();

        if ($osC_Image->reorderImages($_GET['imagesOriginal'])) {
          echo '[[1|' . $_GET['pID'] . ']]';
        } else {
          echo '[[0|' . $_GET['pID'] . ']]';
        }

        exit;
        break;

      case 'deleteProductImage':
        include('includes/classes/image.php');
        $osC_Image = new osC_Image_Admin();

        if ($osC_Image->delete($_GET['image'])) {
          echo '[[1|' . $_GET['image'] . ']]';
        } else {
          echo '[[0|' . $_GET['image'] . ']]';
        }

        exit;
        break;

      case 'getLocalImages':
        $osC_DirectoryListing = new osC_DirectoryListing('../images/products/_upload', true);
        $osC_DirectoryListing->setCheckExtension('gif');
        $osC_DirectoryListing->setCheckExtension('jpg');
        $osC_DirectoryListing->setCheckExtension('png');
        $osC_DirectoryListing->setIncludeDirectories(false);

        $array = array();

        foreach ($osC_DirectoryListing->getFiles() as $file) {
          $array[] = $file['name'];
        }

        echo '[[1|' . implode('#', $array) . ']]';

        exit;
        break;
    }
  }

  echo '[[-100|noActionError]]';
?>
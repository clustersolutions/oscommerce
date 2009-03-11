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
?>

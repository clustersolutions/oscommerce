<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

// start the timer for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime());

// set the local configuration parameters - mainly for developers
  if (file_exists('includes/local/configure.php')) include('includes/local/configure.php');

// include server parameters
  require('includes/configure.php');

// set the level of error reporting
  error_reporting(E_ALL & ~E_NOTICE);

// redirect to the installation module if DB_SERVER is empty
  if (strlen(DB_SERVER) < 1) {
    if (is_dir('install')) {
      header('Location: install/index.php');
    }
  }

// define the project version
  define('PROJECT_VERSION', 'osCommerce 3.0a3pre');

// set the type of request (secure or not)
  $request_type = (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on')) ? 'SSL' : 'NONSSL';

  if ($request_type == 'NONSSL') {
    define('DIR_WS_CATALOG', DIR_WS_HTTP_CATALOG);
  } else {
    define('DIR_WS_CATALOG', DIR_WS_HTTPS_CATALOG);
  }

// compatibility work-around logic for PHP4
  require('includes/functions/compatibility.php');

// include the list of project filenames
  require('includes/filenames.php');

// include the list of project database tables
  require('includes/database_tables.php');

// initialize the message stack for output messages
  require('includes/classes/message_stack.php');
  $messageStack = new messageStack;

// initialize the cache class
  require('includes/classes/cache.php');
  $osC_Cache = new osC_Cache;

// include the database class
  require('includes/classes/database.php');

// make a connection to the database... now
  $osC_Database = osC_Database::connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
  $osC_Database->selectDatabase(DB_DATABASE);

// set the application parameters
  $Qcfg = $osC_Database->query('select configuration_key as cfgKey, configuration_value as cfgValue from :table_configuration');
  $Qcfg->bindTable(':table_configuration', TABLE_CONFIGURATION);
  $Qcfg->setCache('configuration');
  $Qcfg->execute();

  while ($Qcfg->next()) {
    define($Qcfg->value('cfgKey'), $Qcfg->value('cfgValue'));
  }

  $Qcfg->freeResult();

// include functions
  require('includes/functions/general.php');
  require('includes/functions/html_output.php');

// include and start the services
  require('includes/classes/services.php');
  $osC_Services = new osC_Services;
  $osC_Services->startServices();

// Shopping cart actions
  if (isset($_GET['action'])) {
// redirect the customer to a friendly cookie-must-be-enabled page if cookies are disabled
    if ($osC_Session->hasStarted() === false) {
      tep_redirect(tep_href_link(FILENAME_INFO, 'cookie'));
    }

    if (DISPLAY_CART == '1') {
      $goto =  FILENAME_CHECKOUT;
      $parameters = array('action', 'cPath', 'products_id', 'pid');
    } else {
      $goto = basename($_SERVER['PHP_SELF']);
      if ($_GET['action'] == 'buy_now') {
        $parameters = array('action', 'pid', 'products_id');
      } else {
        $parameters = array('action', 'pid');
      }
    }

    switch ($_GET['action']) {
      // customer wants to remove a product from their shopping cart
      case 'cartRemove' :     $osC_ShoppingCart->remove($_GET['products_id']);

                              tep_redirect(tep_href_link($goto, tep_get_all_get_params($parameters)));
                              break;
      // customer wants to update the product quantity in their shopping cart
      case 'update_product' : for ($i=0, $n=sizeof($_POST['products_id']); $i<$n; $i++) {
                                $attributes = (isset($_POST['id']) && isset($_POST['id'][$_POST['products_id'][$i]])) ? $_POST['id'][$_POST['products_id'][$i]] : '';
                                $osC_ShoppingCart->add($_POST['products_id'][$i], $attributes, $_POST['cart_quantity'][$i]);
                              }

                              tep_redirect(tep_href_link($goto, tep_get_all_get_params($parameters)));
                              break;
      // customer adds a product from the products page
      case 'add_product' :    if (isset($_POST['products_id']) && is_numeric($_POST['products_id'])) {
                                if (isset($_POST['id']) && is_array($_POST['id'])) {
                                  $osC_ShoppingCart->add($_POST['products_id'], $_POST['id']);
                                } else {
                                  $osC_ShoppingCart->add($_POST['products_id']);
                                }
                              }

                              tep_redirect(tep_href_link($goto, tep_get_all_get_params($parameters)));
                              break;
      // performed by the 'buy now' button in product listings and review page
      case 'buy_now' :        if (isset($_GET['products_id'])) {
                                if (tep_has_product_attributes($_GET['products_id'])) {
                                  tep_redirect(tep_href_link(FILENAME_PRODUCTS, $_GET['products_id']));
                                } else {
                                  $osC_ShoppingCart->add($_GET['products_id']);
                                }
                              }

                              tep_redirect(tep_href_link($goto, tep_get_all_get_params($parameters)));
                              break;
      case 'notify' :         if ($osC_Customer->isLoggedOn()) {
                                if (isset($_GET['products_id'])) {
                                  $notify = $_GET['products_id'];
                                } elseif (isset($_GET['notify'])) {
                                  $notify = $_GET['notify'];
                                } elseif (isset($_POST['notify'])) {
                                  $notify = $_POST['notify'];
                                } else {
                                  tep_redirect(tep_href_link(basename($_SERVER['PHP_SELF']), tep_get_all_get_params(array('action', 'notify'))));
                                }

                                if (!is_array($notify)) $notify = array($notify);
                                for ($i=0, $n=sizeof($notify); $i<$n; $i++) {
                                  $Qcheck = $osC_Database->query('select count(*) as count from :table_products_notifications where products_id = :products_id and customers_id = :customers_id');
                                  $Qcheck->bindTable(':table_products_notifications', TABLE_PRODUCTS_NOTIFICATIONS);
                                  $Qcheck->bindInt(':products_id', $notify[$i]);
                                  $Qcheck->bindInt(':customers_id', $osC_Customer->getID());
                                  $Qcheck->execute();

                                  if ($Qcheck->valueInt('count') < 1) {
                                    $Qn = $osC_Database->query('insert into :table_products_notifications (products_id, customers_id, date_added) values (:products_id, :customers_id, :date_added)');
                                    $Qn->bindTable(':table_products_notifications', TABLE_PRODUCTS_NOTIFICATIONS);
                                    $Qn->bindInt(':products_id', $notify[$i]);
                                    $Qn->bindInt(':customers_id', $osC_Customer->getID());
                                    $Qn->bindRaw(':date_added', 'now()');
                                    $Qn->execute();
                                  }
                                }

                                tep_redirect(tep_href_link(basename($_SERVER['PHP_SELF']), tep_get_all_get_params(array('action', 'notify'))));
                              } else {
                                $osC_NavigationHistory->setSnapshot();

                                tep_redirect(tep_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
                              }
                              break;
      case 'notify_remove' :  if ($osC_Customer->isLoggedOn() && isset($_GET['products_id'])) {
                                $Qcheck = $osC_Database->query('select count(*) as count from :table_products_notifications where products_id = :products_id and customers_id = :customers_id');
                                $Qcheck->bindTable(':table_products_notifications', TABLE_PRODUCTS_NOTIFICATIONS);
                                $Qcheck->bindInt(':products_id', $_GET['products_id']);
                                $Qcheck->bindInt(':customers_id', $osC_Customer->getID());
                                $Qcheck->execute();

                                if ($Qcheck->valueInt('count') > 0) {
                                  $Qn = $osC_Database->query('delete from :table_products_notifications where products_id = :products_id and customers_id = :customers_id');
                                  $Qn->bindTable(':table_products_notifications', TABLE_PRODUCTS_NOTIFICATIONS);
                                  $Qn->bindInt(':products_id', $_GET['products_id']);
                                  $Qn->bindInt(':customers_id', $osC_Customer->getID());
                                  $Qn->execute();
                                }

                                tep_redirect(tep_href_link(basename($_SERVER['PHP_SELF']), tep_get_all_get_params(array('action'))));
                              } else {
                                $osC_NavigationHistory->setSnapshot();

                                tep_redirect(tep_href_link(FILENAME_ACCOUNT, 'login', 'SSL'));
                              }
                              break;
      case 'cust_order' :     if ($osC_Customer->isLoggedOn() && isset($_GET['pid'])) {
                                if (tep_has_product_attributes($_GET['pid'])) {
                                  tep_redirect(tep_href_link(FILENAME_PRODUCTS, $_GET['pid']));
                                } else {
                                  $osC_ShoppingCart->add($_GET['pid']);
                                }
                              }

                              tep_redirect(tep_href_link($goto, tep_get_all_get_params($parameters)));
                              break;
    }
  }
?>

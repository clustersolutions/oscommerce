<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require(DIR_WS_BOXES . 'shopping_cart.php');

  if (isset($_GET['products_id'])) include(DIR_WS_BOXES . 'manufacturer_info.php');

  if ($osC_Customer->isLoggedOn()) include(DIR_WS_BOXES . 'order_history.php');

  if (isset($_GET['products_id'])) {
    if ($osC_Customer->isLoggedOn()) {
      $Qcheck = $osC_Database->query('select count(*) as count from :table_customers_info where customers_info_id = :customers_info_id and global_product_notifications = :global_product_notifications');
      $Qcheck->bindTable(':table_customers_info', TABLE_CUSTOMERS_INFO);
      $Qcheck->bindInt(':customers_info_id', $osC_Customer->id);
      $Qcheck->bindInt(':global_product_notifications', 1);
      $Qcheck->execute();

      if ($Qcheck->valueInt('count') > 0) {
        include(DIR_WS_BOXES . 'best_sellers.php');
      } else {
        include(DIR_WS_BOXES . 'product_notifications.php');
      }
    } else {
      include(DIR_WS_BOXES . 'product_notifications.php');
    }
  } else {
    include(DIR_WS_BOXES . 'best_sellers.php');
  }

  if (isset($_GET['products_id'])) {
    if (basename($_SERVER['PHP_SELF']) != FILENAME_TELL_A_FRIEND) include(DIR_WS_BOXES . 'tell_a_friend.php');
  } else {
    include(DIR_WS_BOXES . 'specials.php');
  }

  require(DIR_WS_BOXES . 'reviews.php');

  if (substr(basename($_SERVER['PHP_SELF']), 0, 8) != 'checkout') {
    include(DIR_WS_BOXES . 'languages.php');
    include(DIR_WS_BOXES . 'currencies.php');
  }
?>

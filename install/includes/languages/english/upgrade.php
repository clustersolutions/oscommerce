<?php
/*
  $Id: upgrade.php,v 1.2 2004/02/02 20:15:14 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  define('PAGE_TITLE_UPGRADE', 'Upgrade');
  define('TEXT_UPGRADE_DESCRIPTION', '<p>This upgrade procedure will upgrade the <nobr>The Exchange Project 2.1</nobr> database structure to the <nobr>osCommerce 2.2 Milestone 3-CVS</nobr> database structure.</p><p>It is recommended to perform this upgrade procedure on an up-to-date backup of your 2.1 database.</p>');
  define('TEXT_ENTER_DATABASE_INFORMATION', '<p>Please enter the database server information:</p>');

  define('CONFIG_DATABASE_SERVER', 'Database Server:');
  define('CONFIG_DATABASE_SERVER_DESCRIPTION', 'Hostame or IP-address of the database server');
  define('CONFIG_DATABASE_SERVER_DESCRIPTION_LONG', 'The database server can be in the form of a hostname, such as db1.myserver.com, or as an IP-address, such as 192.168.0.1');

  define('CONFIG_DATABASE_USERNAME', 'Username:');
  define('CONFIG_DATABASE_USERNAME_DESCRIPTION', 'Database username');
  define('CONFIG_DATABASE_USERNAME_DESCRIPTION_LONG', 'The username used to connect to the database server. An example username is \'mysql_10\'.<br><br>Note: Create and Drop permissions <b>are required</b> at this point of the installation procedure.');
  define('CONFIG_DATABASE_USERNAME_RESTRICTED_DESCRIPTION_LONG', 'The username used to connect to the database server. An example username is \'mysql_10\'.<br><br>Note: Create and Drop permissions <b>are not required</b> for the general use of osCommerce.');

  define('CONFIG_DATABASE_PASSWORD', 'Password:');
  define('CONFIG_DATABASE_PASSWORD_DESCRIPTION', 'Database password');
  define('CONFIG_DATABASE_PASSWORD_DESCRIPTION_LONG', 'The password is used together with the username, which forms the database user account.');

  define('CONFIG_DATABASE_NAME', 'Database Name:');
  define('CONFIG_DATABASE_NAME_DESCRIPTION', 'Database name');
  define('CONFIG_DATABASE_NAME_DESCRIPTION_LONG', 'The database used to hold the data. An example database name is \'osCommerce\'.');

  define('ERROR_UNSUCCESSFUL_DATABASE_CONNECTION', '<p>A test connection made to the database was <b><u>NOT</u></b> successful.</p><p>The error message returned is:</p><p class="boxme">%s</p><p>Please click on the <i>Back</i> button below to review your database server settings.</p><p>If you require help with your database server settings, please consult your hosting company.</p>');

  define('TEXT_SUCCESSFUL_DATABASE_CONNECTION', '<p>A test connection made to the database was <b><u>successful</u></b>.</p><p>Please continue the upgrade process to perform the database upgrade procedure.</p><p>It is important this procedure is not interrupted, otherwise the database may end up corrupt.</p>');

  define('TEXT_ADDRESS_BOOK', 'Address Book');
  define('TEXT_BANNERS', 'Banners');
  define('TEXT_CATEGORIES', 'Categories');
  define('TEXT_CONFIGURATION', 'Configuration');
  define('TEXT_CURRENCIES', 'Currencies');
  define('TEXT_CUSTOMERS', 'Customers');
  define('TEXT_IMAGES', 'Images');
  define('TEXT_LANGUAGES', 'Languages');
  define('TEXT_MANUFACTURERS', 'Manufacturers');
  define('TEXT_ORDERS', 'Orders');
  define('TEXT_PRODUCTS', 'Products');
  define('TEXT_REVIEWS', 'Reviews');
  define('TEXT_SESSIONS', 'Sessions');
  define('TEXT_SPECIALS', 'Specials');
  define('TEXT_TAXES', 'Taxes');
  define('TEXT_WHOS_ONLINE', 'Whos Online');

  define('TEXT_STATUS', 'Status:');
  define('TEXT_PREPARING', 'Preparing');
  define('TEXT_UPDATING', 'Updating %s');
  define('TEXT_UPDATING_DONE', 'Updating %s .. done!');
  define('TEXT_UPDATING_COMPLETE', 'Update complete!');

  define('TEXT_SUCCESSFUL_DATABASE_UPGRADE', '<p>The database upgrade procedure was successful!</p>');
?>

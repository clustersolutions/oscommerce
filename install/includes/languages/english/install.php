<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  define('PAGE_TITLE_INSTALLATION', 'New Installation');
  define('TEXT_CUSTOMIZE_INSTALLATION', 'Please customize the new installation with the following options:');

  define('CONFIG_IMPORT_CATALOG_DATABASE', 'Import Catalog Database:');
  define('CONFIG_IMPORT_CATALOG_DATABASE_DESCRIPTION', 'Install the database and add the sample data');
  define('CONFIG_IMPORT_CATALOG_DATABASE_DESCRIPTION_LONG', 'Checking this box will import the database structure, required data, and some sample data. (required for first time installations)');

  define('CONFIG_AUTOMATIC_CONFIGURATION', 'Automatic Configuration:');
  define('CONFIG_AUTOMATIC_CONFIGURATION_DESCRIPTION', 'Save configuration values');
  define('CONFIG_AUTOMATIC_CONFIGURATION_DESCRIPTION_LONG', 'Checking this box will save all entered data during the installation procedure to the appropriate configuration files on the server.');

  define('PAGE_SUBTITLE_DATABASE_IMPORT', 'Database Import');
  define('TEXT_ENTER_DATABASE_INFORMATION', 'Please enter the database server information:');

  define('CONFIG_DATABASE_SERVER', 'Database Server:');
  define('CONFIG_DATABASE_SERVER_DESCRIPTION', 'Hostname or IP-address of the database server');
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

  define('CONFIG_DATABASE_TABLE_PREFIX', 'Database Table Prefix:');
  define('CONFIG_DATABASE_TABLE_PREFIX_DESCRIPTION', 'Database table prefix');
  define('CONFIG_DATABASE_TABLE_PREFIX_DESCRIPTION_LONG', 'The prefix to use for the database tables created. An example table prefix is \'osc_\' which would create a table name of osc_products.');

  define('CONFIG_DATABASE_PERSISTENT_CONNECTIONS', 'Persistent Connections:');
  define('CONFIG_DATABASE_PERSISTENT_CONNECTIONS_DESCRIPTION', '');
  define('CONFIG_DATABASE_PERSISTENT_CONNECTIONS_DESCRIPTION_LONG', 'Enable persistent database connections.<br><br>Note: Persistent connections should be disabled for shared servers.');

  define('CONFIG_DATABASE_CLASS', 'Database Type:');
  define('CONFIG_DATABASE_CLASS_DESCRIPTION', '');
  define('CONFIG_DATABASE_CLASS_DESCRIPTION_LONG', 'The database type to use.<br><br>"Transaction-Safe" database types are recommended however will only be used if the database server supports transactions.');

  define('CONFIG_SESSION_STORAGE', 'Session Storage:');
  define('CONFIG_SESSION_STORAGE_FILES', 'Files');
  define('CONFIG_SESSION_STORAGE_DATABASE', 'Database');
  define('CONFIG_SESSION_STORAGE_DESCRIPTION', '');
  define('CONFIG_SESSION_STORAGE_DESCRIPTION_LONG', 'Store user session data as files on the server, or in the database.<br><br>Note: Due to security related issues, database session storage is recommended for shared servers.');

  define('CONFIG_IMPORT_SAMPLE_DATA', 'Import Sample Data:');
  define('CONFIG_IMPORT_SAMPLE_DATA_DESCRIPTION', '');
  define('CONFIG_IMPORT_SAMPLE_DATA_DESCRIPTION_LONG', 'Insert sample data into the database (recommended for first time installations).');

  define('ERROR_UNSUCCESSFUL_DATABASE_TYPE', '<p>The selected database type of <b>%s</b> is not supported by the database server. The database table type will be set back to the default value of <b>%s</b>.</p>');
  define('ERROR_UNSUCCESSFUL_DATABASE_CONNECTION', '<p>A test connection made to the database was <b><u>NOT</u></b> successful.</p><p>The error message returned is:</p><p class="boxme">%s</p><p>Please click on the <i>Back</i> button below to review your database server settings.</p><p>If you require help with your database server settings, please consult your hosting company.</p>');

  define('TEXT_SUCCESSFUL_DATABASE_CONNECTION', '<p>A test connection made to the database was <b><u>successful</u></b>.</p><p>Please continue the installation process to perform the database import procedure.</p><p>It is important this procedure is not interrupted, otherwise the database may end up corrupt.</p>');
  define('TEXT_IMPORT_SQL', '<p>The file to import must be located and named at:</p><p>%s</p>');
  define('TEXT_IMPORT_DATA_SAMPLE_SQL', '<p>The sample data file to import must be located and named at:</p><p>%s</p>');

  define('ERROR_UNSUCCESSFUL_DATABASE_IMPORT', '<p>The following error has occurred:</p><p class="boxme">%s</p>');

  define('TEXT_SUCCESSFUL_DATABASE_IMPORT', 'The database import was <b><u>successful</u></b>!');

  define('PAGE_SUBTITLE_OSCOMMERCE_CONFIGURATION', 'osCommerce Configuration');
  define('TEXT_ENTER_WEBSERVER_INFORMATION', 'Please enter the web server information:');

  define('CONFIG_WWW_ADDRESS', 'WWW Address:');
  define('CONFIG_WWW_ADDRESS_DESCRIPTION', 'The full website address to the online store');
  define('CONFIG_WWW_ADDRESS_DESCRIPTION_LONG', 'The web address to the online store, for example <i>http://www.my-server.com/catalog/</i>');

  define('CONFIG_WWW_ROOT_DIRECTORY', 'Webserver Root Directory:');
  define('CONFIG_WWW_ROOT_DIRECTORY_DESCRIPTION', 'The server path to the online store');
  define('CONFIG_WWW_ROOT_DIRECTORY_DESCRIPTION_LONG', 'The directory where osCommerce is installed on the server, for example <i>/home/myname/public_html/osCommerce/</i>');

  define('CONFIG_WWW_HTTP_COOKIE_DOMAIN', 'HTTP Cookie Domain:');
  define('CONFIG_WWW_HTTP_COOKIE_DOMAIN_DESCRIPTION', 'The domain to store cookies in');
  define('CONFIG_WWW_HTTP_COOKIE_DOMAIN_DESCRIPTION_LONG', 'The full or top-level domain to store the cookies in, for example <i>.my-server.com</i>');

  define('CONFIG_WWW_HTTP_COOKIE_PATH', 'HTTP Cookie Path:');
  define('CONFIG_WWW_HTTP_COOKIE_PATH_DESCRIPTION', 'The path to store cookies under');
  define('CONFIG_WWW_HTTP_COOKIE_PATH_DESCRIPTION_LONG', 'The web address to limit the cookie to, for example <i>/catalog/</i>');

  define('CONFIG_ENABLE_SSL', 'Enable SSL Connections:');
  define('CONFIG_ENABLE_SSL_DESCRIPTION', '');
  define('CONFIG_ENABLE_SSL_DESCRIPTION_LONG', 'Enable secure SSL/HTTPS connections (requires a secure certificate installed on the web server)');

  define('CONFIG_WWW_WORK_DIRECTORY', 'Work Directory:');
  define('CONFIG_WWW_WORK_DIRECTORY_DESCRIPTION', 'The path to store osCommerce work data under (cache, sessions)');
  define('CONFIG_WWW_WORK_DIRECTORY_DESCRIPTION_LONG', 'This path should be located <u>outside</u> the public HTML directory. (please avoid /tmp/ for security reasons)');

  define('ERROR_WORK_DIRECTORY_NON_EXISTANT', '<p>The following error has occurred:</p><p><div class="boxMe"><b>The work directory does not exist.</b><br><br>Please perform the following actions:<ul class="boxMe"><li>mkdir %s</li></ul></div></p>');
  define('ERROR_WORK_DIRECTORY_NOT_WRITEABLE', '<p>The following error has occurred:</p><p><div class="boxMe"><b>The work directory cannot be written to by the web server.</b><br><br>Please perform the following actions:<ul class="boxMe"><li>chmod 706 %s</li></ul></div></p><p class="noteBox">If <i>chmod 706</i> does not work, please try <i>chmod 777</i>.</p>');

  define('TEXT_ENTER_SECURE_WEBSERVER_INFORMATION', 'Please enter the secure web server information:');

  define('CONFIG_WWW_HTTPS_ADDRESS', 'Secure WWW Address:');
  define('CONFIG_WWW_HTTPS_ADDRESS_DESCRIPTION', 'The full website address to the online store on the secure server');
  define('CONFIG_WWW_HTTPS_ADDRESS_DESCRIPTION_LONG', 'The secure web address to the online store, for example <i>https://ssl.my-hosting-company.com/my_name/catalog/</i>');

  define('CONFIG_WWW_HTTPS_COOKIE_DOMAIN', 'Secure Cookie Domain:');
  define('CONFIG_WWW_HTTPS_COOKIE_DOMAIN_DESCRIPTION', 'The secure domain to store cookies in');
  define('CONFIG_WWW_HTTPS_COOKIE_DOMAIN_DESCRIPTION_LONG', 'The full or top-level domain of the secure server to store the cookies in, for example <i>ssl.my-hosting-company.com</i>');

  define('CONFIG_WWW_HTTPS_COOKIE_PATH', 'Secure Cookie Path:');
  define('CONFIG_WWW_HTTPS_COOKIE_PATH_DESCRIPTION', 'The secure path to store cookies under');
  define('CONFIG_WWW_HTTPS_COOKIE_PATH_DESCRIPTION_LONG', 'The web address of the secure server to limit the cookie to, for example <i>/my_name/catalog/</i>');

  define('ERROR_CONFIG_FILE_NOT_WRITEABLE', '<p>The following error has occurred:</p><p><div class="boxMe"><b>The configuration file does not exist, or permission levels are not set.</b><br><br>Please perform the following actions:<ul class="boxMe"><li>cd %sincludes/</li><li>touch configure.php</li><li>chmod 706 configure.php</li></ul></div></p><p class="noteBox">If <i>chmod 706</i> does not work, please try <i>chmod 777</i>.</p><p class="noteBox">If you are running this installation procedure under a Microsoft Windows environment, try renaming the existing configuration file so a new file can be created.</p>');

  define('TEXT_SUCCESSFUL_CONFIGURATION', 'The configuration was successful!');
?>

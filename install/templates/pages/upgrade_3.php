<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/
?>

<p class="pageTitle"><?php echo PAGE_TITLE_UPGRADE; ?></p>

<?php
  $db = array('DB_SERVER' => trim($_POST['DB_SERVER']),
              'DB_SERVER_USERNAME' => trim($_POST['DB_SERVER_USERNAME']),
              'DB_SERVER_PASSWORD' => trim($_POST['DB_SERVER_PASSWORD']),
              'DB_DATABASE' => trim($_POST['DB_DATABASE']));

  $osC_Database = osC_Database::connect($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD']);
  $osC_Database->selectDatabase($db['DB_DATABASE']);

  if (!get_cfg_var('safe_mode')) {
    @set_time_limit(0);
  }

  $languages = tep_get_languages();

// send data to the browser, so the flushing works with IE
  echo str_repeat(' ', 300) . "\n";
?>

<p><span id="addressBook"><span id="addressBookMarker">-</span> <?php echo TEXT_ADDRESS_BOOK; ?></span><br />
<span id="banners"><span id="bannersMarker">-</span> <?php echo TEXT_BANNERS; ?></span><br />
<span id="categories"><span id="categoriesMarker">-</span> <?php echo TEXT_CATEGORIES; ?></span><br />
<span id="configuration"><span id="configurationMarker">-</span> <?php echo TEXT_CONFIGURATION; ?></span><br />
<span id="currencies"><span id="currenciesMarker">-</span> <?php echo TEXT_CURRENCIES; ?></span><br />
<span id="customers"><span id="customersMarker">-</span> <?php echo TEXT_CUSTOMERS; ?></span><br />
<span id="images"><span id="imagesMarker">-</span> <?php echo TEXT_IMAGES; ?></span><br />
<span id="languages"><span id="languagesMarker">-</span> <?php echo TEXT_LANGUAGES; ?></span><br />
<span id="manufacturers"><span id="manufacturersMarker">-</span> <?php echo TEXT_MANUFACTURERS; ?></span><br />
<span id="orders"><span id="ordersMarker">-</span> <?php echo TEXT_ORDERS; ?></span><br />
<span id="products"><span id="productsMarker">-</span> <?php echo TEXT_PRODUCTS; ?></span><br />
<span id="reviews"><span id="reviewsMarker">-</span> <?php echo TEXT_REVIEWS; ?></span><br />
<span id="sessions"><span id="sessionsMarker">-</span> <?php echo TEXT_SESSIONS; ?></span><br />
<span id="specials"><span id="specialsMarker">-</span> <?php echo TEXT_SPECIALS; ?></span><br />
<span id="taxes"><span id="taxesMarker">-</span> <?php echo TEXT_TAXES; ?></span><br />
<span id="whosOnline"><span id="whosOnlineMarker">-</span> <?php echo TEXT_WHOS_ONLINE; ?></span></p>

<p><?php echo TEXT_STATUS; ?> <span id="statusText"><?php echo TEXT_PREPARING; ?></span></p>

<?php flush(); ?>

<script type="text/javascript"><!--
changeStyle('addressBook', 'bold');
changeText('addressBookMarker', '?');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING, TEXT_ADDRESS_BOOK); ?>');
//--></script>

<?php
  flush();

  $osC_Database->simpleQuery('alter table address_book add customers_id int not null after address_book_id');
  $osC_Database->simpleQuery('alter table address_book add entry_company varchar(32) after entry_gender');

  $osC_Database->simpleQuery('alter table customers add customers_default_address_id int(5) not null after customers_email_address');

  $Qab2c = $osC_Database->query('select address_book_id, customers_id from address_book_to_customers');
  while ($Qab2c->next()) {
    $Qab = $osC_Database->query('update address_book set customers_id = :customers_id where address_book_id = :address_book_id');
    $Qab->bindInt(':customers_id', $Qab2c->value('customers_id'));
    $Qab->bindInt(':address_book_id', $Qab2c->value('address_book_id'));
    $Qab->execute();
  }

  $Qab2c->freeResult();

  $Qcustomers = $osC_Database->query('select customers_id, customers_gender, customers_firstname, customers_lastname, customers_street_address, customers_suburb, customers_postcode, customers_city, customers_state, customers_country_id, customers_zone_id from customers');
  while ($Qcustomers->next()) {
    $Qab = $osC_Database->query('insert into address_book (customers_id, entry_gender, entry_company, entry_firstname, entry_lastname, entry_street_address, entry_suburb, entry_postcode, entry_city, entry_state, entry_country_id, entry_zone_id) values (:customers_id, :entry_gender, :entry_company, :entry_firstname, :entry_lastname, :entry_street_address, :entry_suburb, :entry_postcode, :entry_city, :entry_state, :entry_country_id, :entry_zone_id)');
    $Qab->bindInt(':customers_id', $Qcustomers->value('customers_id'));
    $Qab->bindValue(':entry_gender', $Qcustomers->value('customers_gender'));
    $Qab->bindValue(':entry_company', '');
    $Qab->bindValue(':entry_firstname', $Qcustomers->value('customers_firstname'));
    $Qab->bindValue(':entry_lastname', $Qcustomers->value('customers_lastname'));
    $Qab->bindValue(':entry_street_address', $Qcustomers->value('customers_street_address'));
    $Qab->bindValue(':entry_suburb', $Qcustomers->value('customers_suburb'));
    $Qab->bindValue(':entry_postcode', $Qcustomers->value('customers_postcode'));
    $Qab->bindValue(':entry_city', $Qcustomers->value('customers_city'));
    $Qab->bindValue(':entry_state', $Qcustomers->value('customers_state'));
    $Qab->bindInt(':entry_country_id', $Qcustomers->value('customers_country_id'));
    $Qab->bindInt(':entry_zone_id', $Qcustomers->value('customers_zone_id'));
    $Qab->execute();

    $address_book_id = $osC_Database->nextID();

    $Qcustomers_update = $osC_Database->query('update customers set customers_default_address_id = :customers_default_address_id where customers_id = :customers_id');
    $Qcustomers_update->bindInt(':customers_default_address_id', $address_book_id);
    $Qcustomers_update->bindInt(':customers_id', $Qcustomers->value('customers_id'));
    $Qcustomers_update->execute();
  }

  $Qcustomers->freeResult();

  $osC_Database->simpleQuery('alter table address_book add index idx_address_book_customers_id (customers_id)');

  $osC_Database->simpleQuery('drop table address_book_to_customers');
?>
<script type="text/javascript"><!--
changeStyle('addressBook', 'normal');
changeText('addressBookMarker', '*');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING_DONE, TEXT_ADDRESS_BOOK); ?>');

changeStyle('banners', 'bold');
changeText('bannersMarker', '?');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING, TEXT_BANNERS); ?>');
//--></script>

<?php
  flush();

  $osC_Database->simpleQuery("create table banners ( banners_id int(5) not null auto_increment, banners_title varchar(64) not null, banners_url varchar(255) not null, banners_image varchar(64) not null, banners_group varchar(10) not null, banners_html_text text, expires_impressions int(7) default '0', expires_date datetime default null, date_scheduled datetime default null, date_added datetime not null, date_status_change datetime default null, status int(1) default '1' not null, primary key (banners_id) )");
  $osC_Database->simpleQuery("create table banners_history ( banners_history_id int(5) not null auto_increment, banners_id int(5) not null, banners_shown int(5) not null default '0', banners_clicked int(5) not null default '0', banners_history_date datetime not null, primary key (banners_history_id) )");

  $osC_Database->simpleQuery("insert into banners (banners_id, banners_title, banners_url, banners_image, banners_group, banners_html_text, expires_impressions, expires_date, date_scheduled, date_added, date_status_change, status) values (1, 'osCommerce', 'http://www.oscommerce.com', 'banners/oscommerce.gif', '468x50', '', 0, null, null, now(), null, 1)");
?>
<script type="text/javascript"><!--
changeStyle('banners', 'normal');
changeText('bannersMarker', '*');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING_DONE, TEXT_BANNERS); ?>');

changeStyle('categories', 'bold');
changeText('categoriesMarker', '?');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING, TEXT_CATEGORIES); ?>');
//--></script>

<?php
  flush();

  $osC_Database->simpleQuery("create table categories_description ( categories_id int(5) default '0' not null, language_id int(5) default '1' not null, categories_name varchar(32) not null, primary key (categories_id, language_id), key idx_categories_name (categories_name) )");

  $Qcategories = $osC_Database->query('select categories_id, categories_name from categories order by categories_id');
  while ($Qcategories->next()) {
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
      $Qcategories_description = $osC_Database->query('insert into categories_description (categories_id, language_id, categories_name) values (:categories_id, :language_id, :categories_name)');
      $Qcategories_description->bindInt(':categories_id', $Qcategories->value('categories_id'));
      $Qcategories_description->bindInt(':language_id', $languages[$i]['id']);
      $Qcategories_description->bindValue(':categories_name', $Qcategories->value('categories_name'));
      $Qcategories_description->execute();
    }
  }

  $Qcategories->freeResult();

  $osC_Database->simpleQuery("alter table categories drop index IDX_CATEGORIES_NAME");
  $osC_Database->simpleQuery("alter table categories drop categories_name");
  $osC_Database->simpleQuery("alter table categories change parent_id parent_id int(5) not null default '0'");
  $osC_Database->simpleQuery("alter table categories add date_added datetime after sort_order");
  $osC_Database->simpleQuery("alter table categories add last_modified datetime after date_added");
  $osC_Database->simpleQuery("alter table categories add index idx_categories_parent_id (parent_id)");
?>
<script type="text/javascript"><!--
changeStyle('categories', 'normal');
changeText('categoriesMarker', '*');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING_DONE, TEXT_CATEGORIES); ?>');

changeStyle('configuration', 'bold');
changeText('configurationMarker', '?');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING, TEXT_CONFIGURATION); ?>');
//--></script>

<?php
  flush();

  $osC_Database->simpleQuery("alter table configuration change last_modified last_modified datetime");
  $osC_Database->simpleQuery("alter table configuration change date_added date_added datetime not null");
  $osC_Database->simpleQuery("alter table configuration modify use_function varchar(255)");
  $osC_Database->simpleQuery("alter table configuration add set_function varchar(255) after use_function");

  $osC_Database->simpleQuery("update configuration set configuration_key = 'SHIPPING_ORIGIN_COUNTRY' where configuration_key = 'STORE_ORIGIN_COUNTRY'");
  $osC_Database->simpleQuery("update configuration set configuration_key = 'SHIPPING_ORIGIN_ZIP' where configuration_key = 'STORE_ORIGIN_ZIP'");
  $osC_Database->simpleQuery("update configuration set set_function = 'tep_cfg_pull_down_country_list(' where configuration_key = 'STORE_COUNTRY' or configuration_key = 'SHIPPING_ORIGIN_COUNTRY'");
  $osC_Database->simpleQuery("update configuration set configuration_value = 'desc', configuration_description = 'This is the sort order used in the expected products box.', set_function = 'tep_cfg_select_option(array(\'asc\', \'desc\'), ' where configuration_key = 'EXPECTED_PRODUCTS_SORT'");
  $osC_Database->simpleQuery("update configuration set configuration_value = 'date_expected', configuration_description = 'The column to sort by in the expected products box.', set_function = 'tep_cfg_select_option(array(\'products_name\', \'date_expected\'), ' where configuration_key = 'EXPECTED_PRODUCTS_FIELD'");
  $osC_Database->simpleQuery("update configuration set use_function = 'tep_cfg_get_zone_name' where configuration_key = 'STORE_ZONE'");

  $Qcfg = $osC_Database->query("select configuration_value from configuration where configuration_key = 'IMAGE_REQUIRED'");
  if ($Qcfg->value('configuration_value') == '1') {
    $config_flag = 'true';
  } else {
    $config_flag = 'false';
  }
  $osC_Database->simpleQuery("update configuration set configuration_value = '" . $config_flag . "', set_function = 'tep_cfg_select_option(array(\'true\', \'false\'),' where configuration_key = 'IMAGE_REQUIRED'");

  $Qcfg = $osC_Database->query("select configuration_value from configuration where configuration_key = 'CONFIG_CALCULATE_IMAGE_SIZE'");
  if ($Qcfg->value('configuration_value') == '1') {
    $config_flag = 'true';
  } else {
    $config_flag = 'false';
  }
  $osC_Database->simpleQuery("update configuration set configuration_value = '" . $config_flag . "', set_function = 'tep_cfg_select_option(array(\'true\', \'false\'),' where configuration_key = 'CONFIG_CALCULATE_IMAGE_SIZE'");

  $Qcfg->freeResult();

  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Zone', 'STORE_ZONE', '18', 'The zone my store is located in', '1', '7', 'tep_cfg_get_zone_name', 'tep_cfg_pull_down_zone_list(', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Send Extra Order E-Mails To', 'SEND_EXTRA_ORDER_EMAILS_TO', '', 'Send extra order e-mails to the following e-mail addresses, in this format: Name 1 &lt;email@address1&gt;, Name 2 &lt;email@address2&gt;', '1', '11', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Cart After Adding Product', 'DISPLAY_CART', 'true', 'Display the shopping cart after adding a product (or return back to their origin)', '1', '14', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Allow Guest To Tell A Friend', 'ALLOW_GUEST_TO_TELL_A_FRIEND', 'false', 'Allow guests to tell a friend about a product', '1', '15', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Default Search Operator', 'ADVANCED_SEARCH_DEFAULT_OPERATOR', 'and', 'Default search operators', '1', '17', 'tep_cfg_select_option(array(\'and\', \'or\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Store Address and Phone', 'STORE_NAME_ADDRESS', '', 'This is the Store Name, Address and Phone used on printable documents and displayed online', '1', '18', 'tep_cfg_textarea(', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Tax Decimal Places', 'TAX_DECIMAL_PLACES', '2', 'Pad the tax value this amount of decimal places', '1', '20', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Prices with Tax', 'DISPLAY_PRICE_WITH_TAX', 'false', 'Display prices with tax included (true) or add the tax at the end (false)', '1', '21', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");

  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Company', 'ENTRY_COMPANY_LENGTH', '2', 'Minimum length of company name', '2', '6', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Best Sellers', 'MIN_DISPLAY_BESTSELLERS', '1', 'Minimum number of best sellers to display', '2', '15', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Also Purchased', 'MIN_DISPLAY_ALSO_PURCHASED', '1', 'Minimum number of products to display in the \'This Customer Also Purchased\' box', '2', '16', now())");

  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Manufacturers Select Size', 'MAX_MANUFACTURERS_LIST', '1', 'Used in manufacturers box; when this value is \'1\' the classic drop-down list will be used for the manufacturers box. Otherwise, a list-box with the specified number of rows will be displayed.', '3', '7', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('New Products Listing', 'MAX_DISPLAY_PRODUCTS_NEW', '10', 'Maximum number of new products to display in new products page', '3', '14', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Best Sellers', 'MAX_DISPLAY_BESTSELLERS', '10', 'Maximum number of best sellers to display', '3', '15', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Also Purchased', 'MAX_DISPLAY_ALSO_PURCHASED', '5', 'Maximum number of products to display in the \'This Customer Also Purchased\' box', '3', '16', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Customer Order History Box', 'MAX_DISPLAY_PRODUCTS_IN_ORDER_HISTORY_BOX', '6', 'Maximum number of products to display in the customer order history box', '3', '17', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Order History', 'MAX_DISPLAY_ORDER_HISTORY', '10', 'Maximum number of orders to display in the order history page', '3', '18', now())");

  $osC_Database->simpleQuery("delete from configuration where configuration_group_id = '5'");

  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Gender', 'ACCOUNT_GENDER', 'true', 'Display gender in the customers account', '5', '1', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Date of Birth', 'ACCOUNT_DOB', 'true', 'Display date of birth in the customers account', '5', '2', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Company', 'ACCOUNT_COMPANY', 'true', 'Display company in the customers account', '5', '3', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Suburb', 'ACCOUNT_SUBURB', 'true', 'Display suburb in the customers account', '5', '4', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('State', 'ACCOUNT_STATE', 'true', 'Display state in the customers account', '5', '5', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");

  $osC_Database->simpleQuery("delete from configuration where configuration_group_id = '6'");

  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Installed Modules', 'MODULE_PAYMENT_INSTALLED', 'cc.php;cod.php', 'List of payment module filenames separated by a semi-colon. This is automatically updated. No need to edit. (Example: cc.php;cod.php;paypal.php)', '6', '0', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Installed Modules', 'MODULE_SHIPPING_INSTALLED', '', 'List of shipping module filenames separated by a semi-colon. This is automatically updated. No need to edit. (Example: ups.php;flat.php;item.php)', '6', '0', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Installed Modules', 'MODULE_ORDER_TOTAL_INSTALLED', 'ot_subtotal.php;ot_tax.php;ot_shipping.php;ot_total.php', 'List of order_total module filenames separated by a semi-colon. This is automatically updated. No need to edit. (Example: ot_subtotal.php;ot_tax.php;ot_shipping.php;ot_total.php)', '6', '0', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Cash On Delivery Module', 'MODULE_PAYMENT_COD_STATUS', 'True', 'Do you want to accept Cash On Delevery payments?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_COD_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_COD_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_COD_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Credit Card Module', 'MODULE_PAYMENT_CC_STATUS', 'True', 'Do you want to accept credit card payments?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Split Credit Card E-Mail Address', 'MODULE_PAYMENT_CC_EMAIL', '', 'If an e-mail address is entered, the middle digits of the credit card number will be sent to the e-mail address (the outside digits are stored in the database with the middle digits censored)', '6', '0', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_CC_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0' , now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_CC_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_CC_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Default Currency', 'DEFAULT_CURRENCY', 'USD', 'Default Currency', '6', '0', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Default Language', 'DEFAULT_LANGUAGE', 'en', 'Default Language', '6', '0', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Default Order Status For New Orders', 'DEFAULT_ORDERS_STATUS_ID', '1', 'When a new order is created, this order status will be assigned to it.', '6', '0', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Shipping', 'MODULE_ORDER_TOTAL_SHIPPING_STATUS', 'true', 'Do you want to display the order shipping cost?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER', '2', 'Sort order of display.', '6', '2', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Allow Free Shipping', 'MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING', 'false', 'Do you want to allow free shipping?', '6', '3', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added) values ('Free Shipping For Orders Over', 'MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER', '50', 'Provide free shipping for orders over the set amount.', '6', '4', 'currencies->format', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Provide Free Shipping For Orders Made', 'MODULE_ORDER_TOTAL_SHIPPING_DESTINATION', 'national', 'Provide free shipping for orders sent to the set destination.', '6', '5', 'tep_cfg_select_option(array(\'national\', \'international\', \'both\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Sub-Total', 'MODULE_ORDER_TOTAL_SUBTOTAL_STATUS', 'true', 'Do you want to display the order sub-total cost?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER', '1', 'Sort order of display.', '6', '2', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Tax', 'MODULE_ORDER_TOTAL_TAX_STATUS', 'true', 'Do you want to display the order tax value?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_TAX_SORT_ORDER', '3', 'Sort order of display.', '6', '2', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Total', 'MODULE_ORDER_TOTAL_TOTAL_STATUS', 'true', 'Do you want to display the total order value?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER', '4', 'Sort order of display.', '6', '2', now())");

  $osC_Database->simpleQuery("delete from configuration where configuration_group_id = '7' and configuration_key != 'SHIPPING_BOX_WEIGHT' and configuration_key != 'SHIPPING_BOX_PADDING' and configuration_key != 'SHIPPING_MAX_WEIGHT' and configuration_key != 'SHIPPING_ORIGIN_ZIP' and configuration_key != 'SHIPPING_ORIGIN_COUNTRY'");
  $osC_Database->simpleQuery("update configuration set sort_order = '5' where sort_order = '2'");
  $osC_Database->simpleQuery("update configuration set configuration_group_id = '7', sort_order = '1' where configuration_key = 'SHIPPING_ORIGIN_ZIP'");
  $osC_Database->simpleQuery("update configuration set configuration_group_id = '7', sort_order = '2' where configuration_key = 'SHIPPING_ORIGIN_COUNTRY'");

  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Check stock level', 'STOCK_CHECK', 'false', 'Check to see if sufficent stock is available', '9', '1', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Subtract stock', 'STOCK_LIMITED', 'true', 'Subtract product in stock by product orders', '9', '2', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Allow Checkout', 'STOCK_ALLOW_CHECKOUT', 'true', 'Allow customer to checkout even if there is insufficient stock', '9', '3', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Mark product out of stock', 'STOCK_MARK_PRODUCT_OUT_OF_STOCK', '***', 'Display something on screen so customer can see which product has insufficient stock', '9', '4', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Stock Re-order level', 'STOCK_REORDER_LEVEL', '5', 'Define when stock needs to be re-ordered', '9', '5', now())");

  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('E-Mail Transport Method', 'EMAIL_TRANSPORT', 'sendmail', 'Defines if this server uses a local connection to sendmail or uses an SMTP connection via TCP/IP. Servers running ong Windows or MacOS should change this setting to SMTP.', '12', '1', 'tep_cfg_select_option(array(\'sendmail\', \'smtp\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('E-Mail Linefeeds', 'EMAIL_LINEFEED', 'LF', 'Defines the character sequence used to separate mail headers. When using sendmail use LF, when using smtp use CRLF.', '12', '2', 'tep_cfg_select_option(array(\'LF\', \'CRLF\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Use MIME HTML When Sending E-Mails', 'EMAIL_USE_HTML', 'false', 'Send e-mails in HTML format', '12', '3', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Verfiy E-Mail Addresses Through DNS', 'ENTRY_EMAIL_ADDRESS_CHECK', 'false', 'Verfiy e-mail address through a DNS server', '12', '4', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Send E-Mails', 'SEND_EMAILS', 'true', 'Send out e-mails', '12', '5', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");

  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('enable download', 'download_enabled', 'false', 'enable the products download functions.', '13', '1', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('download by redirect', 'download_by_redirect', 'false', 'use browser redirection for download. disable on non-unix systems.', '13', '2', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('expiry delay (days)' ,'download_max_days', '7', 'set number of days before the download link expires. 0 means no limit.', '13', '3', '', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('maximum number of downloads' ,'download_max_count', '5', 'set the maximum number of downloads. 0 means no download authorized.', '13', '4', '', now())");

  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Confirm Terms and Conditions During Checkout Procedure', 'DISPLAY_CONDITIONS_ON_CHECKOUT', 'false', 'Show the Terms and Conditions during the checkout procedure which the customer must agree to.', '16', '1', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Confirm Privacy Notice During Account Creation Procedure', 'DISPLAY_PRIVACY_CONDITIONS', 'false', 'Show the Privacy Notice during the account creation procedure which the customer must agree to.', '16', '2', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");

  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Service Modules', 'MODULE_SERVICES_INSTALLED',  'output_compression;sefu;session;language;currencies;simple_counter;category_path;breadcrumb;whos_online;banner;specials;debug', 'Installed services modules', '6', '0', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Show Number Of Products In Each Category', 'SHOW_COUNTS', 'true', 'Recursively count how many products are in each category.', '6', '0', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Use Default Language Currency', 'USE_DEFAULT_LANGUAGE_CURRENCY', 'false', 'Automatically use the currency set with the language (eg, German->Euro).', '6', '0', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Page Execution Time Log File', 'SERVICE_DEBUG_EXECUTION_TIME_LOG', '', 'Location of the page execution time log file (eg, /www/log/page_parse.log).', '6', '0', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Show The Page Execution Time', 'SERVICE_DEBUG_EXECUTION_DISPLAY', 'True', 'Show the page execution time.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Log Database Queries', 'SERVICE_DEBUG_LOG_DB_QUERIES', 'False', 'Log all database queries in the page execution time log file.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Show Development Version Warning', 'SERVICE_DEBUG_SHOW_DEVELOPMENT_WARNING', 'True', 'Show an osCommerce development version warning message.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Check Language Locale', 'SERVICE_DEBUG_CHECK_LOCALE', 'True', 'Show a warning message if the set language locale does not exist on the server.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Check Installation Module', 'SERVICE_DEBUG_CHECK_INSTALLATION_MODULE', 'True', 'Show a warning message if the installation module exists.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Check Configuration File', 'SERVICE_DEBUG_CHECK_CONFIGURATION', 'True', 'Show a warning if the configuration file is writeable.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Check Sessions Directory', 'SERVICE_DEBUG_CHECK_SESSION_DIRECTORY', 'True', 'Show a warning if the file-based session directory does not exist.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Check Sessions Auto Start', 'SERVICE_DEBUG_CHECK_SESSION_AUTOSTART', 'True', 'Show a warning if PHP is configured to automatically start sessions.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Check Download Directory', 'SERVICE_DEBUG_CHECK_DOWNLOAD_DIRECTORY', 'True', 'Show a warning if the digital product download directory does not exist.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('GZIP Compression Level', 'SERVICE_OUTPUT_COMPRESSION_GZIP_LEVEL', '5', 'Set the GZIP compression level to this value (0=min, 9=max).', '6', '0', 'tep_cfg_select_option(array(\'0\', \'1\', \'2\', \'3\', \'4\', \'5\', \'6\', \'7\', \'8\', \'9\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Force Cookie Usage', 'SERVICE_SESSION_FORCE_COOKIE_USAGE', 'False', 'Only start a session when cookies are enabled.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Block Search Engine Spiders', 'SERVICE_SESSION_BLOCK_SPIDERS', 'False', 'Block search engine spider robots from starting a session.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Check SSL Session ID', 'SERVICE_SESSION_CHECK_SSL_SESSION_ID', 'False', 'Check the SSL_SESSION_ID on every secure HTTPS page request.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Check User Agent', 'SERVICE_SESSION_CHECK_USER_AGENT', 'False', 'Check the browser user agent on every page request.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Check IP Address', 'SERVICE_SESSION_CHECK_IP_ADDRESS', 'False', 'Check the IP address on every page request.', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Regenerate Session ID', 'SERVICE_SESSION_REGENERATE_ID', 'False', 'Regenerate the session ID when a customer logs on or creates an account (requires PHP >= 4.1).', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
  $osC_Database->simpleQuery("insert into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Detect Search Engine Spider Robots', 'SERVICE_WHOS_ONLINE_SPIDER_DETECTION', 'True', 'Detect search engine spider robots (GoogleBot, Yahoo, etc).', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

  $osC_Database->simpleQuery("delete from configuration_group");

  $osC_Database->simpleQuery("alter table configuration_group add visible int(1) default '1'");

  $osC_Database->simpleQuery("insert into configuration_group values ('1', 'My Store', 'General information about my store', '1', '1')");
  $osC_Database->simpleQuery("insert into configuration_group values ('2', 'Minimum Values', 'The minimum values for functions / data', '2', '1')");
  $osC_Database->simpleQuery("insert into configuration_group values ('3', 'Maximum Values', 'The maximum values for functions / data', '3', '1')");
  $osC_Database->simpleQuery("insert into configuration_group values ('4', 'Images', 'Image parameters', '4', '1')");
  $osC_Database->simpleQuery("insert into configuration_group values ('6', 'Module Options', 'Hidden from configuration', '6', '0')");
  $osC_Database->simpleQuery("insert into configuration_group values ('5', 'Customer Details', 'Customer account configuration', '5', '1')");
  $osC_Database->simpleQuery("insert into configuration_group values ('7', 'Shipping/Packaging', 'Shipping options available at my store', '7', '1')");
  $osC_Database->simpleQuery("insert into configuration_group values ('8', 'Product Listing', 'Product Listing    configuration options', '8', '1')");
  $osC_Database->simpleQuery("insert into configuration_group values ('9', 'Stock', 'Stock configuration options', '9', '1')");
  $osC_Database->simpleQuery("insert into configuration_group values ('12', 'E-Mail Options', 'General setting for E-Mail transport and HTML E-Mails', '12', '1')");
  $osC_Database->simpleQuery("insert into configuration_group values ('13', 'Download', 'Downloadable products options', '13', '1')");
  $osC_Database->simpleQuery("insert into configuration_group values ('16', 'Regulations', 'Regulation options', '16', '1')");

?>

<script type="text/javascript"><!--
changeStyle('configuration', 'normal');
changeText('configurationMarker', '*');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING_DONE, TEXT_CONFIGURATION); ?>');

changeStyle('currencies', 'bold');
changeText('currenciesMarker', '?');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING, TEXT_CURRENCIES); ?>');
//--></script>

<?php
  flush();

  $osC_Database->simpleQuery("alter table currencies add value float(13,8)");
  $osC_Database->simpleQuery("alter table currencies add last_updated datetime");
  $osC_Database->simpleQuery("alter table currencies drop decimal_point, drop thousands_point");

  $osC_Database->simpleQuery("update currencies set value = '1'");
?>

<script type="text/javascript"><!--
changeStyle('currencies', 'normal');
changeText('currenciesMarker', '*');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING_DONE, TEXT_CURRENCIES); ?>');

changeStyle('customers', 'bold');
changeText('customersMarker', '?');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING, TEXT_CUSTOMERS); ?>');
//--></script>

<?php
  flush();

  $osC_Database->simpleQuery("alter table customers drop customers_street_address");
  $osC_Database->simpleQuery("alter table customers drop customers_suburb");
  $osC_Database->simpleQuery("alter table customers drop customers_postcode");
  $osC_Database->simpleQuery("alter table customers drop customers_city");
  $osC_Database->simpleQuery("alter table customers drop customers_state");
  $osC_Database->simpleQuery("alter table customers drop customers_zone_id");
  $osC_Database->simpleQuery("alter table customers drop customers_country_id");
  $osC_Database->simpleQuery("alter table customers change customers_dob customers_dob datetime not null default '0000-00-00 00:00:00'");
  $osC_Database->simpleQuery("alter table customers add customers_newsletter char(1)");
  $osC_Database->simpleQuery("alter table customers add customers_ip_address varchar(15)");

  $osC_Database->simpleQuery("alter table customers_basket change products_id products_id tinytext not null");
  $osC_Database->simpleQuery("alter table customers_basket change customers_basket_date_added customers_basket_date_added varchar(8)");
  $osC_Database->simpleQuery("alter table customers_basket change final_price final_price decimal(15,4) not null");

  $osC_Database->simpleQuery("alter table customers_basket_attributes change products_id products_id tinytext not null");

  $osC_Database->simpleQuery("alter table customers_info change customers_info_date_account_created customers_info_date_account_created datetime");
  $osC_Database->simpleQuery("alter table customers_info change customers_info_date_of_last_logon customers_info_date_of_last_logon datetime");
  $osC_Database->simpleQuery("alter table customers_info change customers_info_date_account_last_modified customers_info_date_account_last_modified datetime");
  $osC_Database->simpleQuery("alter table customers_info add global_product_notifications int(1) default '0'");

  $osC_Database->simpleQuery("create table newsletters ( newsletters_id int(5) not null auto_increment, title varchar(255) not null, content text not null, module varchar(255) not null, date_added datetime not null, date_sent datetime, status int(1), locked int(1) default '0', primary key (newsletters_id))");
?>

<script type="text/javascript"><!--
changeStyle('customers', 'normal');
changeText('customersMarker', '*');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING_DONE, TEXT_CUSTOMERS); ?>');

changeStyle('images', 'bold');
changeText('imagesMarker', '?');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING, TEXT_IMAGES); ?>');
//--></script>

<?php
  flush();

// categories
  $Qcategories = $osC_Database->query("select categories_id, categories_image from categories where left(categories_image, 7) = 'images/'");
  while ($Qcategories->next()) {
    $Qcategories_update = $osC_Database->query('update categories set categories_image = substring(:categories_image, 8) where categories_id = :categories_id');
    $Qcategories_update->bindValue(':categories_image', $Qcategories->value('categories_image'));
    $Qcategories_update->bindInt(':categories_id', $Qcategories->value('categories_id'));
    $Qcategories_update->execute();
  }

  $Qcategories->freeResult();

// manufacturers
  $Qmanufacturers = $osC_Database->query("select manufacturers_id, manufacturers_image from manufacturers where left(manufacturers_image, 7) = 'images/'");
  while ($Qmanufacturers->next()) {
    $Qmanufacturers_update = $osC_Database->query('update manufacturers set manufacturers_image = substring(:manufacturers_image, 8) where manufacturers_id = :manufacturers_id');
    $Qmanufacturers_update->bindValue(':manufacturers_image', $Qmanufacturers->value('manufacturers_image'));
    $Qmanufacturers_update->bindInt(':manufacturers_id', $Qmanufacturers->value('manufacturers_id'));
    $Qmanufacturers_update->execute();
  }

  $Qmanufacturers->freeResult();

// products
  $Qproducts = $osC_Database->query("select products_id, products_image from products where left(products_image, 7) = 'images/'");
  while ($Qproducts->next()) {
    $Qproducts_update = $osC_Database->query('update products set products_image = substring(:products_image, 8) where products_id = :products_id');
    $Qproducts_update->bindValue(':products_image', $Qproducts->value('products_image'));
    $Qproducts_update->bindInt(':products_id', $Qproducts->value('products_id'));
    $Qproducts_update->execute();
  }

  $Qproducts->freeResult();
?>

<script type="text/javascript"><!--
changeStyle('images', 'normal');
changeText('imagesMarker', '*');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING_DONE, TEXT_IMAGES); ?>');

changeStyle('languages', 'bold');
changeText('languagesMarker', '?');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING, TEXT_LANGUAGES); ?>');
//--></script>

<?php
  flush();

  $osC_Database->simpleQuery("update languages set image = 'icon.gif'");
?>

<script type="text/javascript"><!--
changeStyle('languages', 'normal');
changeText('languagesMarker', '*');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING_DONE, TEXT_LANGUAGES); ?>');

changeStyle('manufacturers', 'bold');
changeText('manufacturersMarker', '?');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING, TEXT_MANUFACTURERS); ?>');
//--></script>

<?php
  flush();

  $osC_Database->simpleQuery("alter table manufacturers add date_added datetime null after manufacturers_image, add last_modified datetime null after date_added");
  $osC_Database->simpleQuery("create table manufacturers_info (manufacturers_id int(5) not null, languages_id int(5) not null, manufacturers_url varchar(255) not null, url_clicked int(5) not null default '0', date_last_click datetime, primary key (manufacturers_id, languages_id))");
?>

<script type="text/javascript"><!--
changeStyle('manufacturers', 'normal');
changeText('manufacturersMarker', '*');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING_DONE, TEXT_MANUFACTURERS); ?>');

changeStyle('orders', 'bold');
changeText('ordersMarker', '?');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING, TEXT_ORDERS); ?>');
//--></script>

<?php
  flush();

  $osC_Database->simpleQuery("alter table orders add customers_company varchar(32) after customers_name");
  $osC_Database->simpleQuery("alter table orders add delivery_company varchar(32) after delivery_name");
  $osC_Database->simpleQuery("alter table orders add billing_name varchar(64) not null after delivery_address_format_id");
  $osC_Database->simpleQuery("alter table orders add billing_company varchar(32) after billing_name");
  $osC_Database->simpleQuery("alter table orders add billing_street_address varchar(64) not null after billing_company");
  $osC_Database->simpleQuery("alter table orders add billing_suburb varchar(32) after billing_street_address");
  $osC_Database->simpleQuery("alter table orders add billing_city varchar(32) not null after billing_suburb");
  $osC_Database->simpleQuery("alter table orders add billing_postcode varchar(10) not null after billing_city");
  $osC_Database->simpleQuery("alter table orders add billing_state varchar(32) after billing_postcode");
  $osC_Database->simpleQuery("alter table orders add billing_country varchar(32) not null after billing_state");
  $osC_Database->simpleQuery("alter table orders add billing_address_format_id int(5) not null after billing_country");
  $osC_Database->simpleQuery("alter table orders add customers_ip_address varchar(15)");
  $osC_Database->simpleQuery("alter table orders change payment_method payment_method varchar(32) not null");
  $osC_Database->simpleQuery("alter table orders change date_purchased date_purchased datetime");
  $osC_Database->simpleQuery("alter table orders change last_modified last_modified datetime");
  $osC_Database->simpleQuery("alter table orders change orders_date_finished orders_date_finished datetime");
  $osC_Database->simpleQuery("alter table orders_products add column products_model varchar(12)");
  $osC_Database->simpleQuery("alter table orders_products change products_price products_price decimal(15,4) not null");
  $osC_Database->simpleQuery("alter table orders_products change final_price final_price decimal(15,4) not null");
  $osC_Database->simpleQuery("alter table orders_products_attributes change options_values_price options_values_price decimal(15,4) not null");

  $osC_Database->simpleQuery("create table orders_status ( orders_status_id int(5) default '0' not null, language_id int(5) default '1' not null, orders_status_name varchar(32) not null, primary key (orders_status_id, language_id), key idx_orders_status_name (orders_status_name))");

  for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
    $osC_Database->simpleQuery("insert into orders_status values ('1', '" . $languages[$i]['id'] . "', 'Pending')");
    $osC_Database->simpleQuery("insert into orders_status values ('2', '" . $languages[$i]['id'] . "', 'Processing')");
    $osC_Database->simpleQuery("insert into orders_status values ('3', '" . $languages[$i]['id'] . "', 'Delivered')");
  }

  $osC_Database->simpleQuery("update orders set orders_status = '1' where orders_status = 'Pending'");
  $osC_Database->simpleQuery("update orders set orders_status = '2' where orders_status = 'Processing'");
  $osC_Database->simpleQuery("update orders set orders_status = '3' where orders_status = 'Delivered'");

  $status = array();
  $Qorders = $osC_Database->query("select distinct orders_status from orders where orders_status not in ('1', '2', '3')");
  while ($Qorders->next()) {
    $status[] = array('text' => $Qorders->value('orders_status'));
  }

  $orders_status_id = 4;
  for ($i=0, $n=sizeof($status); $i<$n; $i++) {
    for ($j=0, $k=sizeof($languages); $j<$k; $j++) {
      $osC_Database->simpleQuery("insert into orders_status values ('" . $orders_status_id . "', '" . $languages[$j]['id'] . "', '" . $status[$i]['text'] . "')");
    }

    $osC_Database->simpleQuery("update orders set orders_status = '" . $orders_status_id . "' where orders_status = '" . $status[$i]['text'] . "'");

    $orders_status_id++;
  }

  $osC_Database->simpleQuery("alter table orders change orders_status orders_status int(5) not null");

  $osC_Database->simpleQuery("create table orders_status_history ( orders_status_history_id int(5) not null auto_increment, orders_id int(5) not null, orders_status_id int(5) not null, date_added datetime not null, customer_notified int(1) default '0', comments text, primary key (orders_status_history_id))");

  $Qorders = $osC_Database->query("select orders_id, date_purchased, comments from orders where comments <> ''");
  while ($Qorders->next()) {
    $Qorders_update = $osC_Database->query("insert into orders_status_history (orders_id, orders_status_id, date_added, comments) values (:orders_id, '1', :date_added, :comments)");
    $Qorders_update->bindInt(':orders_id', $Qorders->value('orders_id'));
    $Qorders_update->bindValue(':date_added', $Qorders->value('date_purchased'));
    $Qorders_update->bindValue(':comments', $Qorders->value('comments'));
    $Qorders_update->execute();
  }

  $Qorders->freeResult();

  $osC_Database->simpleQuery("alter table orders drop comments");

  $Qorders_products = $osC_Database->query('select op.orders_products_id, opa.orders_products_attributes_id, op.products_id from orders_products op, orders_products_attributes opa where op.orders_id = opa.orders_id');
  while ($Qorders_products->next()) {
    $Qorders_products_update = $osC_Database->query('update orders_products_attributes set orders_products_id = :orders_products_id where orders_products_attributes_id = :orders_products_attributes_id and orders_products_id = :orders_products_id');
    $Qorders_products_update->bindInt(':orders_products_id', $Qorders_products->value('orders_products_id'));
    $Qorders_products_update->bindInt(':orders_products_attributes_id', $Qorders_products->value('orders_products_attributes_id'));
    $Qorders_products_update->bindInt(':orders_products_id', $Qorders_products->value('products_id'));
    $Qorders_products_update->execute();
  }

  $Qorders_products->freeResult();

  $osC_Database->simpleQuery("create table orders_products_download ( orders_products_download_id int(5) not null auto_increment, orders_id int(5) not null default '0', orders_products_id int(5) not null default '0', orders_products_filename varchar(255) not null, download_maxdays int(2) not null default '0', download_count int(2) not null default '0', primary key (orders_products_download_id))");

  $osC_Database->simpleQuery("create table orders_total ( orders_total_id int unsigned not null auto_increment, orders_id int not null, title varchar(255) not null, text varchar(255) not null, value decimal(15,4) not null, class varchar(32) not null, sort_order int not null, primary key (orders_total_id), key idx_orders_total_orders_id (orders_id))");

  $i = 0;
  $Qorders = $osC_Database->query('select orders_id, shipping_method, shipping_cost, currency, currency_value from orders');
  while ($Qorders->next()) {
    $o = array();
    $total_cost = 0;

    $o['id'] = $Qorders->value('orders_id');
    $o['shipping_method'] = $Qorders->value('shipping_method');
    $o['shipping_cost'] = $Qorders->value('shipping_cost');
    $o['currency'] = $Qorders->value('currency');
    $o['currency_value'] = $Qorders->value('currency_value');
    $o['tax'] = 0;

    $Qorders_products = $osC_Database->query('select final_price, products_tax, products_quantity from orders_products where orders_id = :orders_id');
    $Qorders_products->bindInt(':orders_id', $Qorders->value('orders_id'));
    $Qorders_products->execute();

    while ($Qorders_products->next()) {
      $o['products'][$i]['final_price'] = $Qorders_products->value('final_price');
      $o['products'][$i]['qty'] = $Qorders_products->value('products_quantity');

      $o['products'][$i]['tax_groups'][$Qorders_products->value('products_tax')] += $Qorders_products->value('products_tax')/100 * ($Qorders_products->value('final_price') * $Qorders_products->value('products_quantity'));
      $o['tax'] += $Qorders_products->value('products_tax')/100 * ($Qorders_products->value('final_price') * $Qorders_products->value('products_quantity'));

      $total_cost += ($o['products'][$i]['final_price'] * $o['products'][$i]['qty']);
    }

    $subtotal_text = tep_currency_format($total_cost, true, $o['currency'], $o['currency_value']);
    $subtotal_value = $total_cost;

    $Qorders_total = $osC_Database->query("insert into orders_total (orders_total_id, orders_id, title, text, value, class, sort_order) values ('', :orders_id, :title, :text, :value, :class, :sort_order)");
    $Qorders_total->bindInt(':orders_id', $o['id']);
    $Qorders_total->bindValue(':title', 'Sub-Total:');
    $Qorders_total->bindValue(':text', $subtotal_text);
    $Qorders_total->bindDecimal(':value', $subtotal_value);
    $Qorders_total->bindValue(':class', 'ot_subtotal');
    $Qorders_total->bindInt(':sort_order', 1);
    $Qorders_total->execute();

    $tax_text = tep_currency_format($o['tax'], true, $o['currency'], $o['currency_value']);
    $tax_value = $o['tax'];

    $Qorders_total = $osC_Database->query("insert into orders_total (orders_total_id, orders_id, title, text, value, class, sort_order) values ('', :orders_id, :title, :text, :value, :class, :sort_order)");
    $Qorders_total->bindInt(':orders_id', $o['id']);
    $Qorders_total->bindValue(':title', 'Tax:');
    $Qorders_total->bindValue(':text', $tax_text);
    $Qorders_total->bindDecimal(':value', $tax_value);
    $Qorders_total->bindValue(':class', 'ot_tax');
    $Qorders_total->bindInt(':sort_order', 2);
    $Qorders_total->execute();

    if (strlen($o['shipping_method']) < 1) {
      $o['shipping_method'] = 'Shipping:';
    } else {
      $o['shipping_method'] .= ':';
    }

    if ($o['shipping_cost'] > 0) {
      $shipping_text = tep_currency_format($o['shipping_cost'], true, $o['currency'], $o['currency_value']);
      $shipping_value = $o['shipping_cost'];

      $Qorders_total = $osC_Database->query("insert into orders_total (orders_total_id, orders_id, title, text, value, class, sort_order) values ('', :orders_id, :title, :text, :value, :class, :sort_order)");
      $Qorders_total->bindInt(':orders_id', $o['id']);
      $Qorders_total->bindValue(':title', $o['shipping_method']);
      $Qorders_total->bindValue(':text', $shipping_text);
      $Qorders_total->bindDecimal(':value', $shipping_value);
      $Qorders_total->bindValue(':class', 'ot_shipping');
      $Qorders_total->bindInt(':sort_order', 3);
      $Qorders_total->execute();
    }

    $total_text = tep_currency_format($total_cost + $o['tax'] + $o['shipping_cost'], true, $o['currency'], $o['currency_value']);
    $total_value = $total_cost + $o['tax'] + $o['shipping_cost'];

    $Qorders_total = $osC_Database->query("insert into orders_total (orders_total_id, orders_id, title, text, value, class, sort_order) values ('', :orders_id, :title, :text, :value, :class, :sort_order)");
    $Qorders_total->bindInt(':orders_id', $o['id']);
    $Qorders_total->bindValue(':title', 'Total:');
    $Qorders_total->bindValue(':text', $total_text);
    $Qorders_total->bindDecimal(':value', $total_value);
    $Qorders_total->bindValue(':class', 'ot_total');
    $Qorders_total->bindInt(':sort_order', 4);
    $Qorders_total->execute();

    $i++;
  }

  $Qorders->freeResult();

  $osC_Database->simpleQuery("alter table orders drop shipping_method");
  $osC_Database->simpleQuery("alter table orders drop shipping_cost");
?>

<script type="text/javascript"><!--
changeStyle('orders', 'normal');
changeText('ordersMarker', '*');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING_DONE, TEXT_ORDERS); ?>');

changeStyle('products', 'bold');
changeText('productsMarker', '?');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING, TEXT_PRODUCTS); ?>');
//--></script>

<?php
  flush();

  $osC_Database->simpleQuery("create table products_description ( products_id int(5) not null auto_increment, language_id int(5) not null default '1', products_name varchar(64) not null default '',  products_description text, products_url varchar(255), products_viewed int(5) default '0', primary key (products_id, language_id), key products_name (products_name))");

  $Qproducts = $osC_Database->query('select products_id, products_name, products_description, products_url, products_viewed from products order by products_id');
  while ($Qproducts->next()) {
    for ($i=0; $i<sizeof($languages); $i++) {
      $Qproducts_description = $osC_Database->query('insert into products_description (products_id, language_id, products_name, products_description, products_url, products_viewed) values (:products_id, :language_id, :products_name, :products_description, :products_url, :products_viewed)');
      $Qproducts_description->bindInt(':products_id', $Qproducts->value('products_id'));
      $Qproducts_description->bindInt(':language_id', $languages[$i]['id']);
      $Qproducts_description->bindValue(':products_name', $Qproducts->value('products_name'));
      $Qproducts_description->bindValue(':products_description', $Qproducts->value('products_description'));
      $Qproducts_description->bindValue(':products_url', $Qproducts->value('products_url'));
      $Qproducts_description->bindInt(':products_viewed', $Qproducts->value('products_viewed'));
      $Qproducts_description->execute();
    }
  }

  $Qproducts->freeResult();

  $osC_Database->simpleQuery("update products set products_date_added = now() where products_date_added is null");
  $osC_Database->simpleQuery("alter table products change products_date_added products_date_added datetime not null");
  $osC_Database->simpleQuery("alter table products change products_price products_price decimal(15,4) not null");
  $osC_Database->simpleQuery("alter table products add index idx_products_date_added (products_date_added)");

  $osC_Database->simpleQuery("alter table products drop index products_name");

  $osC_Database->simpleQuery("alter table products drop products_url");
  $osC_Database->simpleQuery("alter table products drop products_name");
  $osC_Database->simpleQuery("alter table products drop products_description");
  $osC_Database->simpleQuery("alter table products drop products_viewed");

  $osC_Database->simpleQuery("alter table products add products_date_available datetime");
  $osC_Database->simpleQuery("alter table products add products_last_modified datetime");

  $osC_Database->simpleQuery("alter table products add products_ordered int default '0' not null");

  $Qorders_products = $osC_Database->query('select products_id, sum(products_quantity) as products_ordered from orders_products group by products_id');
  while ($Qorders_products->next()) {
    $Qproducts_update = $osC_Database->query('update products set products_ordered = :products_ordered where products_id = :products_id');
    $Qproducts_update->bindInt(':products_ordered', $Qorders_products->value('products_ordered'));
    $Qproducts_update->bindInt(':products_id', $Qorders_products->value('products_id'));
    $Qproducts_update->execute();
  }

  $Qorders_products->freeResult();

  $osC_Database->simpleQuery("drop table products_expected");

  $osC_Database->simpleQuery("alter table products_attributes change options_values_price options_values_price decimal(15,4) not null");

  $osC_Database->simpleQuery("alter table products_options change products_options_id products_options_id int(5) not null default '0'");
  $osC_Database->simpleQuery("alter table products_options add language_id int(5) not null default '1' after products_options_id");
  $osC_Database->simpleQuery("alter table products_options drop primary key");
  $osC_Database->simpleQuery("alter table products_options add primary key (products_options_id, language_id)");

  $Qproducts_options = $osC_Database->query('select products_options_id, language_id, products_options_name from products_options order by products_options_id');
  while ($Qproducts_options->next()) {
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
      $Qproducts_options_update = $osC_Database->query('replace into products_options (products_options_id, language_id, products_options_name) values (:products_options_id, :language_id, :products_options_name)');
      $Qproducts_options_update->bindInt(':products_options_id', $Qproducts_options->value('products_options_id'));
      $Qproducts_options_update->bindInt(':language_id', $languages[$i]['id']);
      $Qproducts_options_update->bindValue(':products_options_name', $Qproducts_options->value('products_options_name'));
      $Qproducts_options_update->execute();
    }
  }

  $Qproducts_options->freeResult();

  $osC_Database->simpleQuery("alter table products_options_values change products_options_values_id products_options_values_id int(5) not null default '0'");
  $osC_Database->simpleQuery("alter table products_options_values add language_id int(5) not null default '1' after products_options_values_id");
  $osC_Database->simpleQuery("alter table products_options_values drop primary key");
  $osC_Database->simpleQuery("alter table products_options_values add primary key (products_options_values_id, language_id)");

  $Qproducts_options_values = $osC_Database->query('select products_options_values_id, language_id, products_options_values_name from products_options_values order by products_options_values_id');
  while ($Qproducts_options_values->next()) {
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
      $Qproducts_options_values_update = $osC_Database->query('replace into products_options_values (products_options_values_id, language_id, products_options_values_name) values (:products_options_values_id, :language_id, :products_options_values_name)');
      $Qproducts_options_values_update->bindInt(':products_options_values_id', $Qproducts_options_values->value('products_options_values_id'));
      $Qproducts_options_values_update->bindInt(':language_id', $languages[$i]['id']);
      $Qproducts_options_values_update->bindValue(':products_options_values_name', $Qproducts_options_values->value('products_options_values_name'));
      $Qproducts_options_values_update->execute();
    }
  }

  $Qproducts_options_values->freeResult();

  $osC_Database->simpleQuery("alter table products_to_categories change products_id products_id int(5) not null");

  $osC_Database->simpleQuery("create table products_attributes_download ( products_attributes_id int(5) not null, products_attributes_filename varchar(255) not null, products_attributes_maxdays int(2) default '0', products_attributes_maxcount int(2) default '0', primary key (products_attributes_id))");

  $osC_Database->simpleQuery("create table products_notifications ( products_id int(5) not null, customers_id int(5) not null, date_added datetime not null, primary key (products_id, customers_id))");
?>

<script type="text/javascript"><!--
changeStyle('products', 'normal');
changeText('productsMarker', '*');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING_DONE, TEXT_PRODUCTS); ?>');

changeStyle('reviews', 'bold');
changeText('reviewsMarker', '?');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING, TEXT_REVIEWS); ?>');
//--></script>

<?php
  flush();

  $osC_Database->simpleQuery("create table reviews_description ( reviews_id int(5) not null, languages_id int(5) not null, reviews_text text not null, primary key (reviews_id, languages_id))");

  $osC_Database->simpleQuery("alter table reviews add products_id int(5) not null default '0' after reviews_id");
  $osC_Database->simpleQuery("alter table reviews add customers_id int(5) after products_id");
  $osC_Database->simpleQuery("alter table reviews add customers_name varchar(64) not null default '' after customers_id");
  $osC_Database->simpleQuery("alter table reviews add date_added datetime after reviews_rating");
  $osC_Database->simpleQuery("alter table reviews add last_modified datetime after date_added");
  $osC_Database->simpleQuery("alter table reviews add reviews_read int(5) not null default '0'");

  $Qreviews = $osC_Database->query('select r.reviews_id, re.products_id, re.customers_id, r.reviews_rating, re.date_added, re.reviews_read, r.reviews_text from reviews r, reviews_extra re where r.reviews_id = re.reviews_id order by r.reviews_id');
  while ($Qreviews->next()) {
    $Qcustomers = $osC_Database->query('select customers_firstname, customers_lastname from customers where customers_id = :customers_id');
    $Qcustomers->bindInt(':customers_id', $Qreviews->value('customers_id'));
    $Qcustomers->execute();

    if ($Qcustomers->numberOfRows() > 0) {
      $customers_name = $Qcustomers->value('customers_firstname') . ' ' . $Qcustomers->value('customers_lastname');
    } else {
      $customers_name = '';
    }

    $Qreviews_update = $osC_Database->query("update reviews set products_id = :products_id, customers_id = :customers_id, customers_name = :customers_name, date_added = :date_added, last_modified = '', reviews_read = :reviews_read where reviews_id = :reviews_id");
    $Qreviews_update->bindInt(':products_id', $Qreviews->value('products_id'));
    $Qreviews_update->bindInt(':customers_id', $Qreviews->value('customers_id'));
    $Qreviews_update->bindValue(':customers_name', $customers_name);
    $Qreviews_update->bindValue(':date_added', $Qreviews->value('date_added'));
    $Qreviews_update->bindInt(':reviews_read', $Qreviews->value('reviews_read'));
    $Qreviews_update->bindInt(':reviews_id', $Qreviews->value('reviews_id'));
    $Qreviews_update->execute();

    $Qreviews_update = $osC_Database->query('insert into reviews_description (reviews_id, languages_id, reviews_text) values (:reviews_id, :languages_id, :reviews_text)');
    $Qreviews_update->bindInt(':reviews_id', $Qreviews->value('reviews_id'));
    $Qreviews_update->bindInt(':languages_id', $languages[0]['id']);
    $Qreviews_update->bindValue(':reviews_text', $Qreviews->value('reviews_text'));
    $Qreviews_update->execute();
  }

  $Qreviews->freeResult();

  $osC_Database->simpleQuery("alter table reviews drop reviews_text");

  $osC_Database->simpleQuery("drop table reviews_extra");
?>

<script type="text/javascript"><!--
changeStyle('reviews', 'normal');
changeText('reviewsMarker', '*');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING_DONE, TEXT_REVIEWS); ?>');

changeStyle('sessions', 'bold');
changeText('sessionsMarker', '?');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING, TEXT_SESSIONS); ?>');
//--></script>

<?php
  flush();

  $osC_Database->simpleQuery("create table sessions (sesskey varchar(32) not null, expiry int(11) unsigned not null, value text not null, primary key (sesskey))");
?>

<script type="text/javascript"><!--
changeStyle('sessions', 'normal');
changeText('sessionsMarker', '*');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING_DONE, TEXT_SESSIONS); ?>');

changeStyle('specials', 'bold');
changeText('specialsMarker', '?');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING, TEXT_SPECIALS); ?>');
//--></script>

<?php
  flush();

  $osC_Database->simpleQuery("alter table specials change specials_date_added specials_date_added datetime");
  $osC_Database->simpleQuery("alter table specials change specials_new_products_price specials_new_products_price decimal(15,4) not null");

  $osC_Database->simpleQuery("alter table specials add specials_last_modified datetime");
  $osC_Database->simpleQuery("alter table specials add expires_date datetime");
  $osC_Database->simpleQuery("alter table specials add date_status_change datetime");
  $osC_Database->simpleQuery("alter table specials add status int(1) NOT NULL default '1'");
?>

<script type="text/javascript"><!--
changeStyle('specials', 'normal');
changeText('specialsMarker', '*');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING_DONE, TEXT_SPECIALS); ?>');

changeStyle('taxes', 'bold');
changeText('taxesMarker', '?');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING, TEXT_TAXES); ?>');
//--></script>

<?php
  flush();

  $osC_Database->simpleQuery("alter table tax_class change date_added date_added datetime not null");
  $osC_Database->simpleQuery("alter table tax_class change last_modified last_modified datetime");

  $osC_Database->simpleQuery("alter table tax_rates change date_added date_added datetime not null");
  $osC_Database->simpleQuery("alter table tax_rates change last_modified last_modified datetime");

  $osC_Database->simpleQuery("alter table tax_rates add tax_priority int(5) default '1' after tax_class_id");

  $osC_Database->simpleQuery("create table geo_zones (geo_zone_id int(5) not null auto_increment, geo_zone_name varchar(32) not null, geo_zone_description varchar(255) not null, last_modified datetime, date_added datetime not null, primary key (geo_zone_id))");
  $osC_Database->simpleQuery("create table zones_to_geo_zones (association_id int(5) not null auto_increment, zone_country_id int(5) not null, zone_id int(5), geo_zone_id int(5), last_modified datetime, date_added datetime not null, primary key (association_id))");

  $osC_Database->simpleQuery("alter table zones change zone_code zone_code varchar(32) not null");

  $osC_Database->simpleQuery("INSERT INTO geo_zones (geo_zone_id,geo_zone_name,geo_zone_description,last_modified,date_added) SELECT tr.tax_zone_id,zone_name,zone_name,NULL,now() from tax_rates tr,zones z,countries c WHERE tr.tax_zone_id=z.zone_id AND c.countries_id=z.zone_country_id GROUP BY tr.tax_zone_id");

  $osC_Database->simpleQuery("INSERT INTO zones_to_geo_zones (zone_country_id,zone_id,geo_zone_id,date_added) SELECT z.zone_country_id, z.zone_id,tr.tax_zone_id,now() FROM tax_rates tr, zones z WHERE z.zone_id=tr.tax_zone_id GROUP BY tr.tax_zone_id");
?>

<script type="text/javascript"><!--
changeStyle('taxes', 'normal');
changeText('taxesMarker', '*');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING_DONE, TEXT_TAXES); ?>');

changeStyle('whosOnline', 'bold');
changeText('whosOnlineMarker', '?');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING, TEXT_WHOS_ONLINE); ?>');
//--></script>

<?php
  flush();

  $osC_Database->simpleQuery("create table whos_online (customer_id int(5),  full_name varchar(64) not null, session_id varchar(128) not null, ip_address varchar(15) not null, time_entry varchar(14) not null, time_last_click varchar(14) not null, last_page_url varchar(255) not null)");
?>

<script type="text/javascript"><!--
changeStyle('whosOnline', 'normal');
changeText('whosOnlineMarker', '*');
changeText('statusText', '<?php echo sprintf(TEXT_UPDATING_DONE, TEXT_WHOS_ONLINE); ?>');

changeStyle('statusText', 'bold');
changeText('statusText', '<?php echo TEXT_UPDATING_COMPLETE; ?>');
//--></script>

<?php
  flush();

  echo TEXT_SUCCESSFUL_DATABASE_UPGRADE;
?>

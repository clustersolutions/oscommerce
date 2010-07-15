<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Database;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\DirectoryListing;

  define('DB_TABLE_PREFIX', $_POST['DB_TABLE_PREFIX']);

  Registry::set('Database', Database::initialize($_POST['DB_SERVER'], $_POST['DB_SERVER_USERNAME'], $_POST['DB_SERVER_PASSWORD'], $_POST['DB_DATABASE'], $_POST['DB_SERVER_PORT'], str_replace('_', '\\', $_POST['DB_DATABASE_CLASS'])));
//  Registry::set('osC_Database', Registry::get('Database')); // HPDL to delete

  $OSCOM_Database = Registry::get('Database');

  $Qupdate = $OSCOM_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
  $Qupdate->bindValue(':configuration_value', $_POST['CFG_STORE_NAME']);
  $Qupdate->bindValue(':configuration_key', 'STORE_NAME');
  $Qupdate->execute();

  $Qupdate = $OSCOM_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
  $Qupdate->bindValue(':configuration_value', $_POST['CFG_STORE_OWNER_NAME']);
  $Qupdate->bindValue(':configuration_key', 'STORE_OWNER');
  $Qupdate->execute();

  $Qupdate = $OSCOM_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
  $Qupdate->bindValue(':configuration_value', $_POST['CFG_STORE_OWNER_EMAIL_ADDRESS']);
  $Qupdate->bindValue(':configuration_key', 'STORE_OWNER_EMAIL_ADDRESS');
  $Qupdate->execute();

  if ( !empty($_POST['CFG_STORE_OWNER_NAME']) && !empty($_POST['CFG_STORE_OWNER_EMAIL_ADDRESS']) ) {
    $Qupdate = $OSCOM_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
    $Qupdate->bindValue(':configuration_value', '"' . $_POST['CFG_STORE_OWNER_NAME'] . '" <' . $_POST['CFG_STORE_OWNER_EMAIL_ADDRESS'] . '>');
    $Qupdate->bindValue(':configuration_key', 'EMAIL_FROM');
    $Qupdate->execute();
  }

  $Qcheck = $OSCOM_Database->query('select user_name from :table_administrators where user_name = :user_name');
  $Qcheck->bindValue(':user_name', $_POST['CFG_ADMINISTRATOR_USERNAME']);
  $Qcheck->execute();

  if ( $Qcheck->numberOfRows() ) {
    $Qadmin = $OSCOM_Database->query('update :table_administrators set user_password = :user_password where user_name = :user_name');
  } else {
    $Qadmin = $OSCOM_Database->query('insert into :table_administrators (user_name, user_password) values (:user_name, :user_password)');
  }
  $Qadmin->bindValue(':user_password', osc_encrypt_string(trim($_POST['CFG_ADMINISTRATOR_PASSWORD'])));
  $Qadmin->bindValue(':user_name', $_POST['CFG_ADMINISTRATOR_USERNAME']);
  $Qadmin->execute();

  $Qadmin = $OSCOM_Database->query('select id from :table_administrators where user_name = :user_name');
  $Qadmin->bindValue(':user_name', $_POST['CFG_ADMINISTRATOR_USERNAME']);
  $Qadmin->execute();

  $Qcheck = $OSCOM_Database->query('select module from :table_administrators_access where administrators_id = :administrators_id limit 1');
  $Qcheck->bindInt(':administrators_id', $Qadmin->valueInt('id'));
  $Qcheck->execute();

  if ( $Qcheck->numberOfRows() ) {
    $Qdel = $OSCOM_Database->query('delete from :table_administrators_access where administrators_id = :administrators_id');
    $Qdel->bindInt(':administrators_id', $Qadmin->valueInt('id'));
    $Qdel->execute();
  }

  $Qaccess = $OSCOM_Database->query('insert into :table_administrators_access (administrators_id, module) values (:administrators_id, :module)');
  $Qaccess->bindInt(':administrators_id', $Qadmin->valueInt('id'));
  $Qaccess->bindValue(':module', '*');
  $Qaccess->execute();
?>

<div class="mainBlock">
  <div class="stepsBox">
    <ol>
      <li><?php echo OSCOM::getDef('box_steps_step_1'); ?></li>
      <li><?php echo OSCOM::getDef('box_steps_step_2'); ?></li>
      <li style="font-weight: bold;"><?php echo OSCOM::getDef('box_steps_step_3'); ?></li>
    </ol>
  </div>

  <h1><?php echo OSCOM::getDef('page_title_installation'); ?></h1>

  <p><?php echo OSCOM::getDef('text_installation'); ?></p>
</div>

<div class="contentBlock">
  <div class="infoPane">
    <h3><?php echo OSCOM::getDef('box_info_step_3_title'); ?></h3>

    <div class="infoPaneContents">
      <p><?php echo OSCOM::getDef('box_info_step_3_text'); ?></p>
    </div>
  </div>

  <div class="contentPane">
    <h2><?php echo OSCOM::getDef('page_heading_finished'); ?></h2>

<?php
  $http_url = parse_url($_POST['HTTP_WWW_ADDRESS']);
  $http_server = $http_url['scheme'] . '://' . $http_url['host'];
  $http_catalog = $http_url['path'];

  if ( isset($http_url['port']) && !empty($http_url['port']) ) {
    $http_server .= ':' . $http_url['port'];
  }

  if ( substr($http_catalog, -1) != '/' ) {
    $http_catalog .= '/';
  }

  $dir_fs_document_root = realpath(OSCOM::BASE_DIRECTORY . '../../') . '/';

  $OSCOM_DirectoryListing = new DirectoryListing(OSCOM::BASE_DIRECTORY . 'Work/Cache');
  $OSCOM_DirectoryListing->setIncludeDirectories(false);
  $OSCOM_DirectoryListing->setCheckExtension('cache');

  foreach ( $OSCOM_DirectoryListing->getFiles() as $files ) {
    @unlink($OSCOM_DirectoryListing->getDirectory() . '/' . $files['name']);
  }

  $file_contents = 'OSCOM_BOOTSTRAP_FILE = "index.php"' . "\n" .
                   'OSCOM_DEFAULT_SITE = "Shop"' . "\n" .
                   'HTTP_SERVER = "' . $http_server . '"' . "\n" .
                   'HTTPS_SERVER = "' . $http_server . '"' . "\n" .
                   'ENABLE_SSL = "false"' . "\n" .
                   'HTTP_COOKIE_DOMAIN = ""' . "\n" .
                   'HTTPS_COOKIE_DOMAIN = ""' . "\n" .
                   'HTTP_COOKIE_PATH = "' . $http_catalog . '"' . "\n" .
                   'HTTPS_COOKIE_PATH = "' . $http_catalog . '"' . "\n" .
                   'DIR_WS_HTTP_CATALOG = "' . $http_catalog . '"' . "\n" .
                   'DIR_WS_HTTPS_CATALOG = "' . $http_catalog . '"' . "\n" .
                   'DIR_WS_IMAGES = "images/"' . "\n" .
                   'DIR_WS_DOWNLOAD_PUBLIC = "pub/"' . "\n" .
                   'DIR_FS_CATALOG = "' . $dir_fs_document_root . '"' . "\n" .
                   'DIR_FS_WORK = "' . OSCOM::BASE_DIRECTORY . 'work/' . '"' . "\n" .
                   'DIR_FS_DOWNLOAD = "' . $dir_fs_document_root . 'download/"' . "\n" .
                   'DIR_FS_DOWNLOAD_PUBLIC = "' . $dir_fs_document_root . 'pub/"' . "\n" .
                   'DIR_FS_BACKUP = "' . OSCOM::BASE_DIRECTORY . 'Core/Site/Admin/backups/"' . "\n" .
                   'DB_SERVER = "' . $_POST['DB_SERVER'] . '"' . "\n" .
                   'DB_SERVER_USERNAME = "' . $_POST['DB_SERVER_USERNAME'] . '"' . "\n" .
                   'DB_SERVER_PASSWORD = "' . $_POST['DB_SERVER_PASSWORD'] . '"' . "\n" .
                   'DB_SERVER_PORT = "' . $_POST['DB_SERVER_PORT'] . '"' . "\n" .
                   'DB_DATABASE = "' . $_POST['DB_DATABASE']. '"' . "\n" .
                   'DB_DATABASE_CLASS = "' . str_replace('_', '\\', $_POST['DB_DATABASE_CLASS']) . '"' . "\n" .
                   'DB_TABLE_PREFIX = "' . $_POST['DB_TABLE_PREFIX']. '"' . "\n" .
                   'DB_SERVER_PERSISTENT_CONNECTIONS = "false"' . "\n" .
                   'STORE_SESSIONS = "Database"' . "\n";

  if ( is_writable(OSCOM::BASE_DIRECTORY . 'Config/settings.ini') ) {
    file_put_contents(OSCOM::BASE_DIRECTORY . 'Config/settings.ini', $file_contents);
?>

    <p><?php echo OSCOM::getDef('text_successful_installation'); ?></p>

<?php
  } else {
?>

    <form name="install" action="<?php echo OSCOM::getLink(null, null, 'step=3'); ?>" method="post">

    <div class="noticeBox">
      <p><?php echo sprintf(OSCOM::getDef('error_configuration_file_not_writeable'), OSCOM::BASE_DIRECTORY . 'Config/settings.ini'); ?></p>

      <p align="right"><?php echo osc_draw_button(array('icon' => 'refresh', 'title' => OSCOM::getDef('button_retry'))); ?></p>

      <p><?php echo OSCOM::getDef('error_configuration_file_alternate_method'); ?></p>

      <?php echo osc_draw_textarea_field('contents', $file_contents, 60, 5, 'readonly="readonly" style="width: 100%; height: 120px;"', false); ?>
    </div>

<?php
    foreach ( $_POST as $key => $value ) {
      if ( ($key != 'x') && ($key != 'y') ) {
        if ( is_array($value) ) {
          for ( $i=0, $n=sizeof($value); $i<$n; $i++ ) {
            echo osc_draw_hidden_field($key . '[]', $value[$i]);
          }
        } else {
          echo osc_draw_hidden_field($key, $value);
        }
      }
    }
?>

    </form>

    <p><?php echo OSCOM::getDef('text_go_to_shop_after_cfg_file_is_saved'); ?></p>

<?php
  }
?>

    <br />

    <p align="center"><?php echo osc_draw_button(array('href' => $http_server . $http_catalog . 'index.php?Shop', 'icon' => 'cart', 'title' => OSCOM::getDef('button_shop'))) . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . osc_draw_button(array('href' => $http_server . $http_catalog . 'index.php?Admin', 'icon' => 'gear', 'title' => OSCOM::getDef('button_admin'))); ?></p>
  </div>
</div>

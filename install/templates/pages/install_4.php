<?php
/*
  $Id:install_5.php 261 2005-11-17 12:52:42Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  define('DB_TABLE_PREFIX', $_POST['DB_TABLE_PREFIX']);
  include('../includes/database_tables.php');

  $osC_Database = osC_Database::connect($_POST['DB_SERVER'], $_POST['DB_SERVER_USERNAME'], $_POST['DB_SERVER_PASSWORD'], $_POST['DB_DATABASE_CLASS']);
  $osC_Database->selectDatabase($_POST['DB_DATABASE']);

  $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
  $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
  $Qupdate->bindValue(':configuration_value', $_POST['CFG_STORE_NAME']);
  $Qupdate->bindValue(':configuration_key', 'STORE_NAME');
  $Qupdate->execute();

  $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
  $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
  $Qupdate->bindValue(':configuration_value', $_POST['CFG_STORE_OWNER_NAME']);
  $Qupdate->bindValue(':configuration_key', 'STORE_OWNER');
  $Qupdate->execute();

  $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
  $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
  $Qupdate->bindValue(':configuration_value', $_POST['CFG_STORE_OWNER_EMAIL_ADDRESS']);
  $Qupdate->bindValue(':configuration_key', 'STORE_OWNER_EMAIL_ADDRESS');
  $Qupdate->execute();

  if (!empty($_POST['CFG_STORE_OWNER_NAME']) && !empty($_POST['CFG_STORE_OWNER_EMAIL_ADDRESS'])) {
    $Qupdate = $osC_Database->query('update :table_configuration set configuration_value = :configuration_value where configuration_key = :configuration_key');
    $Qupdate->bindTable(':table_configuration', TABLE_CONFIGURATION);
    $Qupdate->bindValue(':configuration_value', '"' . $_POST['CFG_STORE_OWNER_NAME'] . '" <' . $_POST['CFG_STORE_OWNER_EMAIL_ADDRESS'] . '>');
    $Qupdate->bindValue(':configuration_key', 'EMAIL_FROM');
    $Qupdate->execute();
  }

  $Qcheck = $osC_Database->query('select user_name from :table_administrators where user_name = :user_name');
  $Qcheck->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
  $Qcheck->bindValue(':user_name', $_POST['CFG_ADMINISTRATOR_USERNAME']);
  $Qcheck->execute();

  if ($Qcheck->numberOfRows()) {
    $Qadmin = $osC_Database->query('update :table_administrators set user_password = :user_password, user_full_name = :user_full_name, user_email_address = :user_email_address where user_name = :user_name');
  } else {
    $Qadmin = $osC_Database->query('insert into :table_administrators (user_name, user_password, user_full_name, user_email_address) values (:user_name, :user_password, :user_full_name, :user_email_address)');
  }
  $Qadmin->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
  $Qadmin->bindValue(':user_password', tep_encrypt_password(trim($_POST['CFG_ADMINISTRATOR_PASSWORD'])));
  $Qadmin->bindValue(':user_name', $_POST['CFG_ADMINISTRATOR_USERNAME']);
  $Qadmin->bindValue(':user_full_name', $_POST['CFG_STORE_OWNER_NAME']);
  $Qadmin->bindValue(':user_email_address', $_POST['CFG_STORE_OWNER_EMAIL_ADDRESS']);
  $Qadmin->execute();
?>

<div class="mainBlock">
  <div class="stepsBox">
    <ol>
      <li><?php echo $osC_Language->get('box_steps_step_1'); ?></li>
      <li><?php echo $osC_Language->get('box_steps_step_2'); ?></li>
      <li><?php echo $osC_Language->get('box_steps_step_3'); ?></li>
      <li style="font-weight: bold;"><?php echo $osC_Language->get('box_steps_step_4'); ?></li>
    </ol>
  </div>

  <h1><?php echo $osC_Language->get('page_title_installation'); ?></h1>

  <?php echo $osC_Language->get('text_installation'); ?>
</div>

<div class="contentBlock">
  <div class="infoPane">
    <h3><?php echo $osC_Language->get('box_info_step_4_title'); ?></h3>

    <div class="infoPaneContents">
      <?php echo $osC_Language->get('box_info_step_4_text'); ?>
    </div>
  </div>

  <div class="contentPane">
    <h2><?php echo $osC_Language->get('page_heading_step_4'); ?></h2>

<?php
  $dir_fs_document_root = $_POST['DIR_FS_DOCUMENT_ROOT'];
  if ((substr($dir_fs_document_root, -1) != '\\') && (substr($dir_fs_document_root, -1) != '/')) {
    if (strrpos($dir_fs_document_root, '\\') !== false) {
      $dir_fs_document_root .= '\\';
    } else {
      $dir_fs_document_root .= '/';
    }
  }

  $http_url = parse_url($_POST['HTTP_WWW_ADDRESS']);
  $http_server = $http_url['scheme'] . '://' . $http_url['host'];
  $http_catalog = $http_url['path'];
  if (isset($http_url['port']) && !empty($http_url['port'])) {
    $http_server .= ':' . $http_url['port'];
  }

  if (substr($http_catalog, -1) != '/') {
    $http_catalog .= '/';
  }

  $http_work_directory = $_POST['HTTP_WORK_DIRECTORY'];
  if (substr($http_work_directory, -1) != '/') {
    $http_work_directory .= '/';
  }

  include('../admin/includes/classes/directory_listing.php');
  $osC_DirectoryListing = new osC_DirectoryListing($http_work_directory);
  $osC_DirectoryListing->setIncludeDirectories(false);
  $osC_DirectoryListing->setCheckExtension('cache');

  foreach ($osC_DirectoryListing->getFiles() as $files) {
    @unlink($osC_DirectoryListing->getDirectory() . '/' . $files['name']);
  }

  $file_contents = '<?php' . "\n" .
                   '  define(\'HTTP_SERVER\', \'' . $http_server . '\');' . "\n" .
                   '  define(\'HTTPS_SERVER\', \'' . $http_server . '\');' . "\n" .
                   '  define(\'ENABLE_SSL\', false);' . "\n" .
                   '  define(\'HTTP_COOKIE_DOMAIN\', \'\');' . "\n" .
                   '  define(\'HTTPS_COOKIE_DOMAIN\', \'\');' . "\n" .
                   '  define(\'HTTP_COOKIE_PATH\', \'\');' . "\n" .
                   '  define(\'HTTPS_COOKIE_PATH\', \'\');' . "\n" .
                   '  define(\'DIR_WS_HTTP_CATALOG\', \'' . $http_catalog . '\');' . "\n" .
                   '  define(\'DIR_WS_HTTPS_CATALOG\', \'' . $http_catalog . '\');' . "\n" .
                   '  define(\'DIR_WS_IMAGES\', \'images/\');' . "\n\n" .
                   '  define(\'DIR_WS_DOWNLOAD_PUBLIC\', \'pub/\');' . "\n" .
                   '  define(\'DIR_FS_CATALOG\', \'' . $dir_fs_document_root . '\');' . "\n" .
                   '  define(\'DIR_FS_WORK\', \'' . $http_work_directory . '\');' . "\n" .
                   '  define(\'DIR_FS_DOWNLOAD\', DIR_FS_CATALOG . \'download/\');' . "\n" .
                   '  define(\'DIR_FS_DOWNLOAD_PUBLIC\', DIR_FS_CATALOG . \'pub/\');' . "\n" .
                   '  define(\'DIR_FS_BACKUP\', \'' . $dir_fs_document_root . 'admin/backups/\');' . "\n\n" .
                   '  define(\'DB_SERVER\', \'' . $_POST['DB_SERVER'] . '\');' . "\n" .
                   '  define(\'DB_SERVER_USERNAME\', \'' . $_POST['DB_SERVER_USERNAME'] . '\');' . "\n" .
                   '  define(\'DB_SERVER_PASSWORD\', \'' . $_POST['DB_SERVER_PASSWORD']. '\');' . "\n" .
                   '  define(\'DB_DATABASE\', \'' . $_POST['DB_DATABASE']. '\');' . "\n" .
                   '  define(\'DB_DATABASE_CLASS\', \'' . (($_POST['DB_DATABASE_CLASS'] == 'mysql_innodb') && ($db_has_innodb === true) ? 'mysql_innodb' : 'mysql') . '\');' . "\n" .
                   '  define(\'DB_TABLE_PREFIX\', \'' . $_POST['DB_TABLE_PREFIX']. '\');' . "\n" .
                   '  define(\'USE_PCONNECT\', \'false\');' . "\n" .
                   '  define(\'STORE_SESSIONS\', \'mysql\');' . "\n" .
                   '?>';

  if (file_exists($dir_fs_document_root . 'includes/configure.php') && !is_writeable($dir_fs_document_root . 'includes/configure.php')) {
    @chmod($dir_fs_document_root . 'includes/configure.php', 0777);
  }

  if (file_exists($dir_fs_document_root . 'includes/configure.php') && is_writeable($dir_fs_document_root . 'includes/configure.php')) {
    $fp = fopen($dir_fs_document_root . 'includes/configure.php', 'w');
    fputs($fp, $file_contents);
    fclose($fp);
  } else {
?>

    <form name="install" action="install.php?step=5" method="post">

    <div class="noticeBox">
      <?php echo sprintf($osC_Language->get('error_configuration_file_not_writeable'), $dir_fs_document_root . 'includes/configure.php'); ?>

      <p align="right"><?php echo '<input type="image" src="templates/' . $template . '/languages/' . $osC_Language->getDirectory() . '/images/buttons/retry.gif" border="0" alt="' . $osC_Language->get('image_button_retry') . '" />'; ?></p>

      <?php echo $osC_Language->get('error_configuration_file_alternate_method'); ?>

      <textarea name="contents" readonly="readonly" style="width: 100%; height: 120px;">
<?php
    echo $file_contents;
?>
      </textarea>
    </div>

<?php
    foreach ($_POST as $key => $value) {
      if ($key != 'x' && $key != 'y') {
        if (is_array($value)) {
          for ($i=0, $n=sizeof($value); $i<$n; $i++) {
            echo osc_draw_hidden_field($key . '[]', $value[$i]);
          }
        } else {
          echo osc_draw_hidden_field($key, $value);
        }
      }
    }
?>

    </form>

<?php
  }
?>

    <p><?php echo $osC_Language->get('text_successful_installation'); ?></p>

    <br />

    <table border="0" width="99%" cellspacing="0" cellpadding="0">
      <tr>
        <td align="center" width="50%"><a href="<?php echo $http_server . $http_catalog . 'index.php'; ?>" target="_blank"><img src="images/button_catalog.gif" border="0" alt="Catalog" /></a></td>
        <td align="center" width="50%"><a href="<?php echo $http_server . $http_catalog . 'admin/index.php'; ?>" target="_blank"><img src="images/button_administration_tool.gif" border="0" alt="Administration Tool" /></a></td>
      </tr>
    </table>
  </div>
</div>

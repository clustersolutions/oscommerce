<?php
/*
  $Id: install_3.php,v 1.13 2004/11/07 20:39:49 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  $script_filename = getenv('PATH_TRANSLATED');
  if (empty($script_filename)) {
    $script_filename = getenv('SCRIPT_FILENAME');
  }

  $script_filename = str_replace('\\', '/', $script_filename);
  $script_filename = str_replace('//', '/', $script_filename);

  $dir_fs_www_root_array = explode('/', dirname($script_filename));
  $dir_fs_www_root = array();
  for ($i=0, $n=sizeof($dir_fs_www_root_array)-1; $i<$n; $i++) {
    $dir_fs_www_root[] = $dir_fs_www_root_array[$i];
  }
  $dir_fs_www_root = implode('/', $dir_fs_www_root) . '/';
?>

<p class="pageTitle"><?php echo PAGE_TITLE_INSTALLATION; ?></p>

<p class="pageSubTitle"><?php echo PAGE_SUBTITLE_DATABASE_IMPORT; ?></p>

<?php
  if (in_array('database', $_POST['install'])) {
    $db = array('DB_SERVER' => trim($_POST['DB_SERVER']),
                'DB_SERVER_USERNAME' => trim($_POST['DB_SERVER_USERNAME']),
                'DB_SERVER_PASSWORD' => trim($_POST['DB_SERVER_PASSWORD']),
                'DB_DATABASE' => trim($_POST['DB_DATABASE']),
                'DB_TABLE_PREFIX' => trim($_POST['DB_TABLE_PREFIX']),
                'DB_DATABASE_CLASS' => trim($_POST['DB_DATABASE_CLASS']));

    $osC_Database = osC_Database::connect($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD'], $db['DB_DATABASE_CLASS']);

    if ($osC_Database->isError() === false) {
      $osC_Database->setErrorReporting(false);

      if ($osC_Database->selectDatabase($db['DB_DATABASE']) === false) {
        $osC_Database->setErrorReporting(true);

        $osC_Database->query('create database ' . $db['DB_DATABASE']);
      }

      $osC_Database->setErrorReporting(true);
    }

    if ($osC_Database->isError() === false) {
      $sql_file = $dir_fs_www_root . 'install/oscommerce.sql';

      $osC_Database->importSQL($sql_file, $db['DB_DATABASE'], $db['DB_TABLE_PREFIX']);
    }

    if (($osC_Database->isError() === false) && isset($_POST['DB_INSERT_SAMPLE_DATA']) && ($_POST['DB_INSERT_SAMPLE_DATA'] == 'true')) {
      $sql_file = $dir_fs_www_root . 'install/oscommerce_sample_data.sql';

      $osC_Database->importSQL($sql_file, $db['DB_DATABASE'], $db['DB_TABLE_PREFIX']);
    }

    if ($_POST['DB_DATABASE_CLASS'] == 'mysql_innodb') {
      $Qinno = $osC_Database->query('show variables like "have_innodb"');
      if (($Qinno->numberOfRows() === 1) && (strtolower($Qinno->value('Value')) == 'yes')) {
        $database_tables = array('address_book', 'categories', 'categories_description', 'customers', 'customers_basket', 'customers_basket_attributes', 'customers_info', 'manufacturers', 'manufacturers_info', 'orders', 'orders_products', 'orders_status', 'orders_status_history', 'orders_products_attributes', 'orders_products_download', 'orders_total', 'products', 'products_attributes', 'products_attributes_download', 'products_description', 'products_options', 'products_options_values', 'products_options_values_to_products_options', 'products_to_categories', 'reviews', 'weight_classes', 'weight_classes_rules');

        foreach ($database_tables as $table) {
          $osC_Database->simpleQuery('alter table ' . $db['DB_TABLE_PREFIX'] . $table . ' type = innodb');
        }
      }
    }

    if ($osC_Database->isError()) {
?>
<form name="install" action="install.php?step=3" method="post">

<table width="95%" border="0" cellpadding="2" class="formPage">
  <tr>
    <td><?php echo sprintf(ERROR_UNSUCCESSFUL_DATABASE_IMPORT, $osC_Database->getError()); ?></td>
  </tr>
</table>

<?php
      foreach ($_POST as $key => $value) {
        if (($key != 'x') && ($key != 'y') && ($key != 'DB_TEST_CONNECTION')) {
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

<p>&nbsp;</p>

<table width="95%" border="0" cellspacing="2">
  <tr>
    <td align="right"><input type="image" src="templates/<?php echo $template; ?>/languages/<?php echo $language; ?>/images/buttons/retry.gif" border="0" alt="<?php echo IMAGE_BUTTON_RETRY; ?>">&nbsp;&nbsp;<a href="index.php"><img src="templates/<?php echo $template; ?>/languages/<?php echo $language; ?>/images/buttons/cancel.gif" border="0" alt="<?php echo IMAGE_BUTTON_CANCEL; ?>"></a></td>
  </tr>
</table>

</form>

<?php
    } else {
?>
<form name="install" action="install.php?step=4" method="post">

<table width="95%" border="0" cellpadding="2" class="formPage">
  <tr>
    <td>
      <p><?php echo TEXT_SUCCESSFUL_DATABASE_IMPORT; ?></p>
    </td>
  </tr>
</table>

<?php
      foreach ($_POST as $key => $value) {
        if (($key != 'x') && ($key != 'y') && ($key != 'DB_TEST_CONNECTION')) {
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

<p>&nbsp;</p>

<table width="95%" border="0" cellspacing="2">
  <tr>
<?php
      if (in_array('configure', $_POST['install'])) {
?>
    <td align="right"><input type="image" src="templates/<?php echo $template; ?>/languages/<?php echo $language; ?>/images/buttons/continue.gif" border="0" alt="<?php echo IMAGE_BUTTON_CONTINUE; ?>"></td>
<?php
      } else {
?>
    <td align="right"><a href="index.php"><img src="templates/<?php echo $template; ?>/languages/<?php echo $language; ?>/images/buttons/continue.gif" border="0" alt="<?php echo IMAGE_BUTTON_CONTINUE; ?>"></a></td>
<?php
      }
?>
  </tr>
</table>

</form>

<?php
    }
  }
?>

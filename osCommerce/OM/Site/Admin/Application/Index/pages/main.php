<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\DirectoryListing;
  use osCommerce\OM\OSCOM;
  use osCommerce\OM\Registry;

  $OSCOM_DirectoryListing = new DirectoryListing(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSite() . '/Module/IndexModules');
  $OSCOM_DirectoryListing->setIncludeDirectories(false);
  $files = $OSCOM_DirectoryListing->getFiles();

  $Qonline = Registry::get('Database')->query('select count(*) as total from :table_whos_online where time_last_click >= :time_last_click');
  $Qonline->bindInt(':time_last_click', (time() - 900));
  $Qonline->execute();
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<p><?php echo osc_link_object(OSCOM::getLink(null, 'OnlineCustomers'), osc_icon('people.png') . '&nbsp;' . sprintf(OSCOM::getDef('total_customers_online'), $Qonline->valueInt('total'))); ?></p>

<table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  $col = 0;

  foreach ($files as $file) {
    $module = substr($file['name'], 0, strrpos($file['name'], '.'));
    $module_class = 'osCommerce\\OM\\Site\\Admin\\Module\\IndexModules\\' . $module;

    $OSCOM_Admin_IndexModule= new $module_class();

    if ( $OSCOM_Admin_IndexModule->hasData() ) {
      if ( $col === 0 ) {
        echo '  <tr>' . "\n";
      }

      $col++;

      if ( $col <= 2 ) {
        echo '    <td width="50%" valign="top">' . "\n";
      }

      echo '<h2>';

      if ( $OSCOM_Admin_IndexModule->hasTitleLink() ) {
        echo '<a href="' . $OSCOM_Admin_IndexModule->getTitleLink() . '">';
      }

      echo $OSCOM_Admin_IndexModule->getTitle();

      if ( $OSCOM_Admin_IndexModule->hasTitleLink() ) {
        echo '</a>';
      }

      echo '</h2>';

      echo $OSCOM_Admin_IndexModule->getData();

      if ( $col <= 2 ) {
        echo '    </td>' . "\n";
      }

      if ( (next($files) === false) || ($col === 2) ) {
        $col = 0;

        echo '  </tr>' . "\n";
      }
    }

    unset($OSCOM_Admin_IndexModule);
  }
?>

</table>

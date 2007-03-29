<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $osC_DirectoryListing = new osC_DirectoryListing('includes/modules/summary');
  $osC_DirectoryListing->setIncludeDirectories(false);
  $files = $osC_DirectoryListing->getFiles();

  $Qonline = $osC_Database->query('select count(*) as total from :table_whos_online where time_last_click >= :time_last_click');
  $Qonline->bindTable(':table_whos_online', TABLE_WHOS_ONLINE);
  $Qonline->bindInt(':time_last_click', (time() - 900));
  $Qonline->execute();
?>

<p><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, 'whos_online'), osc_icon('people.png') . '&nbsp;' . sprintf($osC_Language->get('total_customers_online'), $Qonline->valueInt('total'))); ?></p>

<table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  $col = 0;

  foreach ($files as $file) {
    include('includes/modules/summary/' . $file['name']);

    $module = substr($file['name'], 0, strrpos($file['name'], '.'));
    $module_class = 'osC_Summary_' . $module;

    $osC_Summary = new $module_class();

    if ($osC_Summary->hasData()) {
      if ($col === 0) {
        echo '  <tr>' . "\n";
      }

      $col++;

      if ($col <= 2) {
        echo '    <td width="50%" valign="top">' . "\n";
      }

      if ($osC_Summary->hasTitleLink()) {
        echo '<a href="' . $osC_Summary->getTitleLink() . '">';
      }

      echo '<h1>' . $osC_Summary->getTitle() . '</h1>';

      if ($osC_Summary->hasTitleLink()) {
        echo '</a>';
      }

      echo $osC_Summary->getData();

      if ($col <= 2) {
        echo '    </td>' . "\n";
      }

      if ( (next($files) === false) || ($col === 2) ) {
        $col = 0;

        echo '  </tr>' . "\n";
      }
    }

    unset($osC_Summary);
  }
?>

</table>

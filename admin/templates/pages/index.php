<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/directory_listing.php');
  $osC_DirectoryListing = new osC_DirectoryListing('includes/modules/summary');
  $osC_DirectoryListing->setIncludeDirectories(false);
  $files = $osC_DirectoryListing->getFiles();

  $Qonline = $osC_Database->query('select count(*) as total from :table_whos_online where time_last_click >= :time_last_click');
  $Qonline->bindTable(':table_whos_online', TABLE_WHOS_ONLINE);
  $Qonline->bindInt(':time_last_click', (time() - 900));
  $Qonline->execute();
?>

<p><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, 'whos_online'), osc_icon('people.png', ICON_PREVIEW) . '&nbsp;' . sprintf(TEXT_NUMBER_OF_CUSTOMERS_ONLINE, $Qonline->valueInt('total'))); ?></p>

<table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  $col = 0;

  foreach ($files as $file) {
    include('includes/modules/summary/' . $file['name']);

    $module = 'osC_Summary_' . substr($file['name'], 0, strrpos($file['name'], '.'));

    $$module = new $module();

    if ($$module->hasData()) {
      if ($col === 0) {
        echo '  <tr>' . "\n";
      }

      $col++;

      if ($col <= 2) {
        echo '    <td width="50%" valign="top">' . "\n";
      }

      if ($$module->hasTitleLink()) {
        echo '<a href="' . $$module->getTitleLink() . '">';
      }

      echo '<h1>' . $$module->getTitle() . '</h1>';

      if ($$module->hasTitleLink()) {
        echo '</a>';
      }

      echo $$module->getData();

      if ($col <= 2) {
        echo '    </td>' . "\n";
      }

      if ( (next($files) === false) || ($col === 2) ) {
        $col = 0;

        echo '  </tr>' . "\n";
      }
    }

    unset($$module);
  }
?>

</table>

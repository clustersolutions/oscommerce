<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Admin\Application\Dashboard\Dashboard;
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  $col = 0;

  $modules = Dashboard::getModules();

  foreach ( $modules as $module ) {
    if ( $col === 0 ) {
      echo '  <tr>' . "\n";
    }

    $col++;

    if ( $col <= 2 ) {
      echo '    <td width="50%" valign="top">' . "\n";
    }

    echo '<h2>';

    if ( isset($module['link']) ) {
      echo '<a href="' . $module['link'] . '">';
    }

    echo $module['title'];

    if ( isset($module['link']) ) {
      echo '</a>';
    }

    echo '</h2>';

    echo $module['data'];

    if ( $col <= 2 ) {
      echo '    </td>' . "\n";
    }

    if ( (next($modules) === false) || ($col === 2) ) {
      $col = 0;

      echo '  </tr>' . "\n";
    }
  }
?>

</table>

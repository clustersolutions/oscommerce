<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  include('includes/modules/image/' . $_GET['module'] . '.php');
  $class = 'osC_Image_Admin_' . $_GET['module'];
  $osC_Images = new $class();
  $osC_Images->activate();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle() . ': ' . $osC_Images->getTitle()); ?></h1></td>
    <td align="right"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton">'; ?></td>
  </tr>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>

<?php
  foreach ($osC_Images->getHeader() as $header) {
    echo '      <th>' . $header . '</th>' . "\n";
  }
?>

    </tr>
  </thead>
  <tbody>

<?php
  foreach ($osC_Images->getData() as $data) {
    if (!isset($columns)) {
      $columns = sizeof($data);
    }

    echo '    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">' . "\n";

    for ($i=0; $i<$columns; $i++) {
      echo '      <td>' . $data[$i] . '</td>' . "\n";
    }

    echo '    </tr>' . "\n";
  }
?>

  </tbody>
</table>

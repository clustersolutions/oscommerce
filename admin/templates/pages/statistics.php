<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  include('includes/modules/statistics/' . $_GET['module'] . '.php');
  $class = 'osC_Statistics_' . str_replace(' ', '_', ucwords(str_replace('_', ' ', $_GET['module'])));
  $osC_Statistics = new $class();
  $osC_Statistics->activate();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle() . ': ' . $osC_Statistics->getTitle()); ?></h1></td>
    <td align="right"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton">'; ?></td>
  </tr>
</table>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>

<?php
  foreach ($osC_Statistics->getHeader() as $header) {
    echo '      <th>' . $header . '</th>' . "\n";
  }
?>

    </tr>
  </thead>
  <tbody>

<?php
  foreach ($osC_Statistics->getData() as $data) {
    if (!isset($columns)) {
      $columns = sizeof($data);
    }
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">

<?php
    for ($i=0; $i<$columns; $i++) {
      echo '      <td>' . $data[$i] . '</td>' . "\n";
    }
?>

    </tr>

<?php
  }
?>

  </tbody>
</table>

<?php
  if ($osC_Statistics->isBatchQuery()) {
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td class="smallText"><?php echo $osC_Statistics->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
    <td class="smallText" align="right"><?php echo $osC_Statistics->displayBatchLinksPullDown('page', $osC_Template->getModule() . '&module=' . $_GET['module']); ?></td>
  </tr>
</table>

<?php
  }
?>

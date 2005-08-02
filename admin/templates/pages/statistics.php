<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  include('includes/modules/statistics/' . $module . '.php');
  $class = 'osC_Statistics_' . str_replace(' ', '_', ucwords(str_replace('_', ' ', $module)));
  $osC_Statistics = new $class();
  $osC_Statistics->activate();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><h1><?php echo HEADING_TITLE . ': ' . $osC_Statistics->getTitle(); ?></h1></td>
    <td align="right"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onClick="document.location.href=\'' . tep_href_link(FILENAME_STATISTICS) . '\';" class="operationButton">'; ?></td>
  </tr>
</table>

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

    echo '    <tr onMouseOver="rowOverEffect(this);" onMouseOut="rowOutEffect(this);">' . "\n";

    for ($i=0; $i<$columns; $i++) {
      echo '      <td>' . $data[$i] . '</td>' . "\n";
    }

    echo '    </tr>' . "\n";
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
    <td class="smallText" align="right"><?php echo $osC_Statistics->displayBatchLinksPullDown('page', 'module=' . $module); ?></td>
  </tr>
</table>

<?php
  }
?>

<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/directory_listing.php');
  $osC_DirectoryListing = new osC_DirectoryListing(DIR_FS_WORK);
  $osC_DirectoryListing->setIncludeDirectories(false);
  $osC_DirectoryListing->setCheckExtension('cache');
  $files = $osC_DirectoryListing->getFiles();

  $cached_files = array();

  foreach ($files as $file) {
    $last_modified = filemtime(DIR_FS_WORK . '/' . $file['name']);

    if (strpos($file['name'], '-') !== false) {
      $code = substr($file['name'], 0, strpos($file['name'], '-'));
    } else {
      $code = substr($file['name'], 0, strpos($file['name'], '.'));
    }

    if (isset($cached_files[$code])) {
      $cached_files[$code]['total']++;

      if ($last_modified > $cached_files[$code]['last_modified']) {
        $cached_files[$code]['last_modified'] = $last_modified;
      }
    } else {
      $cached_files[$code] = array('total' => 1,
                                   'last_modified' => $last_modified);
    }
  }
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo TABLE_HEADING_CACHE; ?></th>
      <th><?php echo TABLE_HEADING_TOTAL; ?></th>
      <th><?php echo TABLE_HEADING_LAST_MODIFIED; ?></th>
      <th><?php echo TABLE_HEADING_ACTION; ?></th>
    </tr>
  </thead>
  <tbody>
<?php
  foreach($cached_files as $cache => $stats) {
?>
    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td><?php echo $cache; ?></td>
      <td align="center"><?php echo $stats['total']; ?></td>
      <td align="right"><?php echo strftime(DATE_TIME_FORMAT, $stats['last_modified']); ?></td>
      <td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_CACHE, 'cache=' . $cache . '&action=reset') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/delete.png', IMAGE_DELETE, '16', '16') . '</a>'; ?></td>
    </tr>
<?php
  }
?>
  </tbody>
</table>

<p><?php echo TEXT_CACHE_DIRECTORY . ' ' . DIR_FS_WORK; ?></p>

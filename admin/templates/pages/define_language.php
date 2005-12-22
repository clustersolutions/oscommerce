<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/directory_listing.php');

  $osC_DirectoryListing = new osC_DirectoryListing(realpath('../includes/languages/' . $lng));
  $osC_DirectoryListing->setRecursive(true);
  $osC_DirectoryListing->setAddDirectoryToFilename(true);
  $osC_DirectoryListing->setCheckExtension('php');
  $osC_DirectoryListing->setIncludeDirectories(false);
  $osC_DirectoryListing->setExcludeEntries('CVS');
  $files = $osC_DirectoryListing->getFiles();

  array_unshift($files, array('name' => '../' . $lng . '.php', 'is_directory' => false));
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><h1><?php echo HEADING_TITLE; ?></h1></td>
    <td class="smallText" align="right">
<?php
  echo tep_draw_form('language', FILENAME_DEFINE_LANGUAGE, '', 'get') .
       tep_draw_pull_down_menu('lng', $languages_array, $lng, 'onchange="this.form.submit();"') .
       '</form>';
?>
    </td>
  </tr>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo TABLE_HEADING_FILES; ?></th>
      <th><?php echo TABLE_HEADING_FILE_SIZE; ?></th>
      <th><?php echo TABLE_HEADING_WRITEABLE; ?></th>
      <th><?php echo TABLE_HEADING_LAST_MODIFIED; ?></th>
    </tr>
  </thead>
  <tbody>
<?php
  $subdirectories = array();

  for ($i=0, $n=sizeof($files); $i<$n; $i++) {
    if (($files[$i]['name'] != '../' . $lng . '.php') && (strpos($files[$i]['name'], '/') !== false)) {
      $subdirectories[] = $files[$i]['name'];
    } else {
      echo '    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">' . "\n" .
           '      <td><a href="' . tep_href_link(FILENAME_DEFINE_LANGUAGE, 'lng=' . $lng . '&file=' . $files[$i]['name'] . '&action=edit') . '">'. tep_image('templates/' . $template . '/images/icons/16x16/file.png', ICON_FILES, '16', '16') . '&nbsp;' . $files[$i]['name'] . '</a></td>' . "\n" .
           '      <td align="right">' . number_format(filesize($osC_DirectoryListing->getDirectory() . '/' . $files[$i]['name'])) . '</td>' . "\n" .
           '      <td align="center">' . tep_image('templates/' . $template . '/images/icons/' . (is_writable($osC_DirectoryListing->getDirectory() . '/' . $files[$i]['name']) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif')) . '</td>' . "\n" .
           '      <td align="right">' . date('F d Y H:i:s', filemtime($osC_DirectoryListing->getDirectory() . '/' . $files[$i]['name'])) . '</td>' . "\n" .
           '    </tr>' . "\n";
    }
  }

  for ($i=0, $n=sizeof($subdirectories); $i<$n; $i++) {
    echo '    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">' . "\n" .
         '      <td><a href="' . tep_href_link(FILENAME_DEFINE_LANGUAGE, 'lng=' . $lng . '&file=' . $subdirectories[$i] . '&action=edit') . '">'. tep_image('templates/' . $template . '/images/icons/16x16/file.png', ICON_FILES, '16', '16') . '&nbsp;' . $subdirectories[$i] . '</a></td>' . "\n" .
         '      <td align="right">' . number_format(filesize($osC_DirectoryListing->getDirectory() . '/' . $subdirectories[$i])) . '</td>' . "\n" .
         '      <td align="center">' . tep_image('templates/' . $template . '/images/icons/' . (is_writable($osC_DirectoryListing->getDirectory() . '/' . $subdirectories[$i]) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif')) . '</td>' . "\n" .
         '      <td align="right">' . date('F d Y H:i:s', filemtime($osC_DirectoryListing->getDirectory() . '/' . $subdirectories[$i])) . '</td>' . "\n" .
         '    </tr>' . "\n";
  }
?>
  </tbody>
</table>

<p><?php echo TEXT_LANGUAGE_DIRECTORY . ' ' . $osC_DirectoryListing->getDirectory(); ?></p>

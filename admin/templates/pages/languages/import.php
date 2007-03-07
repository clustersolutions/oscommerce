<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $languages_array = array();

  $osC_DirectoryListing = new osC_DirectoryListing('../includes/languages');
  $osC_DirectoryListing->setIncludeDirectories(false);
  $osC_DirectoryListing->setCheckExtension('xml');

  foreach ($osC_DirectoryListing->getFiles() as $file) {
    $languages_array[] = array('id' => substr($file['name'], 0, strrpos($file['name'], '.')), 'text' => substr($file['name'], 0, strrpos($file['name'], '.')));
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_IMPORT_LANGUAGE; ?></div>
<div class="infoBoxContent">
  <form name="lImport" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=import'); ?>" method="post">

  <p><?php echo TEXT_INFO_IMPORT_INTRO; ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_SELECT_LANGUAGE . '</b>'; ?></td>
      <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('language_import', $languages_array, null, 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_SELECT_IMPORT_TYPE . '</b>'; ?></td>
      <td class="smallText" width="60%"><?php echo osc_draw_radio_field('import_type', array(array('id' => 'add', 'text' => 'Only Add New Records'), array('id' => 'update', 'text' => 'Only Update Existing Records'), array('id' => 'replace', 'text' => 'Replace Completely')), 'add', null, '<br />'); ?></td>
    </tr>
  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_IMPORT . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

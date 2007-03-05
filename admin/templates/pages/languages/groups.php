<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<p align="right"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&lID=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page']) . '\';" class="infoBoxButton" /> <input type="button" value="' . IMAGE_INSERT . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&action=insertDefinition') . '\';" class="infoBoxButton" />'; ?></p>

<?php
  $Qgroups = $osC_Database->query('select distinct content_group, count(*) as total_entries from :table_languages_definitions where languages_id = :languages_id group by content_group order by content_group');
  $Qgroups->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
  $Qgroups->bindInt(':languages_id', $_GET[$osC_Template->getModule()]);
  $Qgroups->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php echo sprintf(TEXT_DISPLAY_NUMBER_OF_ENTRIES, ($Qgroups->numberOfRows() > 0 ? 1 : 0), $Qgroups->numberOfRows(), $Qgroups->numberOfRows()); ?></td>
  </tr>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo TABLE_HEADING_DEFINITION_GROUPS; ?></th>
      <th><?php echo TABLE_HEADING_TOTAL_DEFINITIONS; ?></th>
      <th width="150"><?php echo TABLE_HEADING_ACTION; ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th colspan="3">&nbsp;</th>
    </tr>
  </tfoot>
  <tbody>

<?php
  while ($Qgroups->next()) {
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&group=' . $Qgroups->value('content_group') . '&action=definitions'), osc_image('images/icons/folder.gif', ICON_FOLDER) . '&nbsp;' . $Qgroups->value('content_group')); ?></td>
      <td align="right"><?php echo $Qgroups->value('total_entries'); ?></td>
      <td align="right">

<?php
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&group=' . $Qgroups->value('content_group') . '&action=deleteDefinitions'), osc_icon('trash.png', IMAGE_DELETE));
?>

      </td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . TEXT_LEGEND . '</b> ' . osc_icon('trash.png', IMAGE_DELETE) . '&nbsp;' . IMAGE_DELETE; ?></td>
  </tr>
</table>

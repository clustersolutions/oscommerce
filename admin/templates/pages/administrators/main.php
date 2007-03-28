<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<p align="right"><?php echo '<input type="button" value="' . IMAGE_INSERT . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=save') . '\';" class="infoBoxButton" />'; ?></p>

<?php
  $Qadmin = $osC_Database->query('select id, user_name from :table_administrators order by user_name');
  $Qadmin->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
  $Qadmin->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qadmin->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php echo $Qadmin->getBatchTotalPages(TEXT_DISPLAY_NUMBER_OF_ENTRIES); ?></td>
    <td align="right"><?php echo $Qadmin->getBatchPageLinks('page', $osC_Template->getModule(), false); ?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo $osC_Language->get('table_heading_administrators'); ?></th>
      <th width="150"><?php echo $osC_Language->get('table_heading_action'); ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="2"><?php echo '<input type="image" src="' . osc_icon_raw('configure.png') . '" title="' . IMAGE_EDIT . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchSave') . '\';" />&nbsp;<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . IMAGE_DELETE . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchDelete') . '\';" />'; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>

<?php
  while ($Qadmin->next()) {
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
      <td onclick="document.getElementById('batch<?php echo $Qadmin->valueInt('id'); ?>').checked = !document.getElementById('batch<?php echo $Qadmin->valueInt('id'); ?>').checked;"><?php echo $Qadmin->value('user_name'); ?></td>
      <td align="right">

<?php
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&aID=' . $Qadmin->valueInt('id') . '&action=save'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;' .
         osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&aID=' . $Qadmin->valueInt('id') . '&action=delete'), osc_icon('trash.png', IMAGE_DELETE));
?>

      </td>
      <td align="center"><?php echo osc_draw_checkbox_field('batch[]', $Qadmin->valueInt('id'), null, 'id="batch' . $Qadmin->valueInt('id') . '"'); ?></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

</form>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . TEXT_LEGEND . '</b> ' . osc_icon('configure.png', IMAGE_EDIT) . '&nbsp;' . IMAGE_EDIT . '&nbsp;&nbsp;' . osc_icon('trash.png', IMAGE_DELETE) . '&nbsp;' . IMAGE_DELETE; ?></td>
    <td align="right"><?php echo $Qadmin->getBatchPagesPullDownMenu('page', $osC_Template->getModule()); ?></td>
  </tr>
</table>

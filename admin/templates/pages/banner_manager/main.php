<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<p align="right"><?php echo '<input type="button" value="' . IMAGE_NEW_BANNER . '" onclick="document.location.href=\'' . osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=save') . '\';" class="infoBoxButton" />'; ?></p>

<?php
  $Qbanners = $osC_Database->query('select banners_id, banners_title, banners_group, status from :table_banners order by banners_title, banners_group');
  $Qbanners->bindTable(':table_banners', TABLE_BANNERS);
  $Qbanners->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qbanners->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php echo $Qbanners->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_ENTRIES); ?></td>
    <td align="right"><?php echo $Qbanners->displayBatchLinksPullDown('page', $osC_Template->getModule()); ?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th><?php echo TABLE_HEADING_BANNERS; ?></th>
      <th><?php echo TABLE_HEADING_GROUPS; ?></th>
      <th><?php echo TABLE_HEADING_STATISTICS; ?></th>
      <th width="150"><?php echo TABLE_HEADING_ACTION; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="4"><?php echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . IMAGE_DELETE . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchDelete') . '\';" />'; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>

<?php
  while ( $Qbanners->next() ) {
    $Qstats = $osC_Database->query('select sum(banners_shown) as banners_shown, sum(banners_clicked) as banners_clicked from :table_banners_history where banners_id = :banners_id');
    $Qstats->bindTable(':table_banners_history', TABLE_BANNERS_HISTORY);
    $Qstats->bindInt(':banners_id', $Qbanners->valueInt('banners_id'));
    $Qstats->execute();
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" <?php echo (($Qbanners->valueInt('status') !== 1) ? 'class="deactivatedRow"' : '') ?>>
      <td onclick="document.getElementById('batch<?php echo $Qbanners->valueInt('banners_id'); ?>').checked = !document.getElementById('batch<?php echo $Qbanners->valueInt('banners_id'); ?>').checked;"><?php echo $Qbanners->value('banners_title'); ?></td>
      <td><?php echo $Qbanners->valueProtected('banners_group'); ?></td>
      <td><?php echo $Qstats->valueInt('banners_shown') . ' / ' . $Qstats->valueInt('banners_clicked'); ?></td>
      <td align="right">

<?php
    echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&bID=' . $Qbanners->valueInt('banners_id') . '&action=preview'), osc_icon('windows.png', IMAGE_PREVIEW)) . '&nbsp;' .
         osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&bID=' . $Qbanners->valueInt('banners_id') . '&action=statistics'), osc_icon('graph.png', ICON_STATISTICS)) . '&nbsp;' .
         osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&bID=' . $Qbanners->valueInt('banners_id') . '&action=save'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;' .
         osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&bID=' . $Qbanners->valueInt('banners_id') . '&action=delete'), osc_icon('trash.png', IMAGE_DELETE));
?>

      </td>
      <td align="center"><?php echo osc_draw_checkbox_field('batch[]', $Qbanners->valueInt('banners_id'), null, 'id="batch' . $Qbanners->valueInt('banners_id') . '"'); ?></td>
    </tr>

<?php
  }
?>

  </tbody>
</table>

</form>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . TEXT_LEGEND . '</b> ' . osc_icon('windows.png', IMAGE_PREVIEW) . '&nbsp;' . IMAGE_PREVIEW . '&nbsp;&nbsp;' . osc_icon('graph.png', ICON_STATISTICS) . '&nbsp;' . ICON_STATISTICS . '&nbsp;&nbsp;' . osc_icon('configure.png', IMAGE_EDIT) . '&nbsp;' . IMAGE_EDIT . '&nbsp;&nbsp;' . osc_icon('trash.png', IMAGE_DELETE) . '&nbsp;' . IMAGE_DELETE; ?></td>
    <td align="right"><?php echo $Qbanners->displayBatchLinksPullDown('page', $osC_Template->getModule()); ?></td>
  </tr>
</table>

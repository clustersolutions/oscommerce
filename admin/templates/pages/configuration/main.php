<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&gID=' . $_GET['gID']), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }

  $Qcfg = $osC_Database->query('select configuration_id, configuration_title, configuration_description, configuration_value, use_function from :table_configuration where configuration_group_id = :configuration_group_id order by sort_order');
  $Qcfg->bindTable(':table_configuration', TABLE_CONFIGURATION);
  $Qcfg->bindInt(':configuration_group_id', $_GET['gID']);
  $Qcfg->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php echo sprintf(TEXT_DISPLAY_NUMBER_OF_ENTRIES, ($Qcfg->numberOfRows() > 0 ? 1 : 0), $Qcfg->numberOfRows(), $Qcfg->numberOfRows()); ?></td>
  </tr>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th width="35%;"><?php echo TABLE_HEADING_CONFIGURATION_TITLE; ?></th>
      <th><?php echo TABLE_HEADING_CONFIGURATION_VALUE; ?></th>
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
  while ($Qcfg->next()) {
    if ( !osc_empty($Qcfg->value('use_function')) ) {
      $cfgValue = osc_call_user_func($Qcfg->value('use_function'), $Qcfg->value('configuration_value'));
    } else {
      $cfgValue = $Qcfg->value('configuration_value');
    }
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" title="<?php echo $Qcfg->valueProtected('configuration_description'); ?>">
      <td><?php echo $Qcfg->value('configuration_title'); ?></td>
      <td><?php echo nl2br(htmlspecialchars($cfgValue)); ?></td>
      <td align="right">

<?php
    echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&gID=' . $_GET['gID'] . '&cID=' . $Qcfg->valueInt('configuration_id') . '&action=save'), osc_icon('configure.png', IMAGE_EDIT));
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
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . TEXT_LEGEND . '</b> ' . osc_icon('configure.png', IMAGE_EDIT) . '&nbsp;' . IMAGE_EDIT; ?></td>
  </tr>
</table>

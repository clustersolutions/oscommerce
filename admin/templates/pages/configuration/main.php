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
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div id="infoBox_cDefault" <?php if (!empty($_GET['action'])) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th width="35%;"><?php echo TABLE_HEADING_CONFIGURATION_TITLE; ?></th>
        <th><?php echo TABLE_HEADING_CONFIGURATION_VALUE; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>

<?php
  $Qcfg = $osC_Database->query('select configuration_id, configuration_title, configuration_description, configuration_value, use_function from :table_configuration where configuration_group_id = :configuration_group_id order by sort_order');
  $Qcfg->bindTable(':table_configuration', TABLE_CONFIGURATION);
  $Qcfg->bindInt(':configuration_group_id', $_GET['gID']);
  $Qcfg->execute();

  while ($Qcfg->next()) {
    if (!osc_empty($Qcfg->value('use_function'))) {
      $cfgValue = osc_call_user_func($Qcfg->value('use_function'), $Qcfg->value('configuration_value'));
    } else {
      $cfgValue = $Qcfg->value('configuration_value');
    }

    if (!isset($cInfo) && (!isset($_GET['cID']) || (isset($_GET['cID']) && ($_GET['cID'] == $Qcfg->value('configuration_id'))))) {
      $Qcv = $osC_Database->query('select configuration_key, date_added, last_modified, set_function from :table_configuration where configuration_id = :configuration_id');
      $Qcv->bindTable(':table_configuration', TABLE_CONFIGURATION);
      $Qcv->bindInt(':configuration_id', $Qcfg->valueInt('configuration_id'));
      $Qcv->execute();

      $cInfo = new objectInfo(array_merge($Qcfg->toArray(), $Qcv->toArray()));
    }
?>

      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" title="<?php echo $Qcfg->valueProtected('configuration_description'); ?>">
        <td><?php echo $Qcfg->value('configuration_title'); ?></td>
        <td><?php echo htmlspecialchars($cfgValue); ?></td>
        <td align="right">

<?php
    if (isset($cInfo) && ($Qcfg->valueInt('configuration_id') == $cInfo->configuration_id)) {
      echo osc_link_object('#', osc_icon('configure.png', IMAGE_EDIT), 'onclick="toggleInfoBox(\'cEdit\');"');
    } else {
      echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&gID=' . $_GET['gID'] . '&cID=' . $Qcfg->valueInt('configuration_id') . '&action=cEdit'), osc_icon('configure.png', IMAGE_EDIT));
    }
?>

        </td>
      </tr>

<?php
  }
?>

    </tbody>
  </table>
</div>

<?php
  if (isset($cInfo)) {
    if (!empty($cInfo->set_function)) {
      $value_field = osc_call_user_func($cInfo->set_function, $cInfo->configuration_value);
    } else {
      $value_field = osc_draw_input_field('configuration_value', $cInfo->configuration_value, 'style="width: 100%;"');
    }
?>

<div id="infoBox_cEdit" <?php if ($_GET['action'] != 'cEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $cInfo->configuration_title; ?></div>
  <div class="infoBoxContent">
    <form name="cEdit" action="<?php echo osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&gID=' . $_GET['gID'] . '&cID=' . $cInfo->configuration_id . '&action=save'); ?>" method="post">

    <p><?php echo $cInfo->configuration_description; ?></p>

    <p><?php echo '<b>' . $cInfo->configuration_title . ':</b><br />' . $value_field; ?></p>

    <p><?php echo TEXT_INFO_LAST_MODIFIED . ' ' . (!empty($cInfo->last_modified) ? osC_DateTime::getShort($cInfo->last_modified) : osC_DateTime::getShort($cInfo->date_added)); ?></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'cDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  }
?>

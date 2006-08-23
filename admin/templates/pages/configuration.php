<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  $Qcg = $osC_Database->query('select configuration_group_title from :table_configuration_group where configuration_group_id = :configuration_group_id');
  $Qcg->bindTable(':table_configuration_group', TABLE_CONFIGURATION_GROUP);
  $Qcg->bindInt(':configuration_group_id', $_GET['gID']);
  $Qcg->execute();
?>

<h1><?php echo $Qcg->value('configuration_group_title'); ?></h1>

<div id="infoBox_cDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_CONFIGURATION_TITLE; ?></th>
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

    if (isset($cInfo) && ($Qcfg->valueInt('configuration_id') == $cInfo->configuration_id)) {
      echo '      <tr class="selected" title="' . $Qcfg->valueProtected('configuration_description') . '">' . "\n";
    } else {
      echo '      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $Qcfg->valueInt('configuration_id')) . '\';" title="' . $Qcfg->valueProtected('configuration_description') . '">' . "\n";
    }
?>
        <td><?php echo $Qcfg->value('configuration_title'); ?></td>
        <td><?php echo htmlspecialchars($cfgValue); ?></td>
        <td align="right">
<?php
    if (isset($cInfo) && ($Qcfg->valueInt('configuration_id') == $cInfo->configuration_id)) {
      echo '<a href="#" onclick="toggleInfoBox(\'cEdit\');">' . osc_icon('configure.png', IMAGE_EDIT) . '</a>';
    } else {
      echo osc_link_object(osc_href_link_admin(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $Qcfg->valueInt('configuration_id') . '&action=cEdit'), osc_icon('configure.png', IMAGE_EDIT));
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

<div id="infoBox_cEdit" <?php if ($action != 'cEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $cInfo->configuration_title; ?></div>
  <div class="infoBoxContent">
    <form name="cEdit" action="<?php echo osc_href_link_admin(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $cInfo->configuration_id . '&action=save'); ?>" method="post">

    <p><?php echo $cInfo->configuration_description; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . $cInfo->configuration_title . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo $value_field; ?></td>
      </tr>
    </table>

    <p><?php echo TEXT_INFO_LAST_MODIFIED . ' ' . (!empty($cInfo->last_modified) ? osC_DateTime::getShort($cInfo->last_modified) : osC_DateTime::getShort($cInfo->date_added)); ?></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'cDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  }
?>

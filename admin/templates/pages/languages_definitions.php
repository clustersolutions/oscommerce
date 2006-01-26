<?php
/*
  $Id: languages.php 387 2006-01-18 16:49:58Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $Qgroups = $osC_Database->query('select distinct content_group from :table_languages_definitions where languages_id = :languages_id order by content_group');
  $Qgroups->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
  $Qgroups->bindInt(':languages_id', $_GET['lID']);
  $Qgroups->execute();

  $groups_array = array();
  while ($Qgroups->next()) {
    $groups_array[] = array('id' => $Qgroups->value('content_group'), 'text' => $Qgroups->value('content_group'));
  }
?>

<div>
  <div style="float: right; margin-top: 10px;"><?php echo '<a href="' . tep_href_link(FILENAME_LANGUAGES, 'lID=' . $_GET['lID']) . '">' . tep_image('templates/' . $template . '/images/icons/16x16/back.png', IMAGE_BACK, '16', '16') . ' ' . TEXT_BACK_TO_LANGUAGES . '</a>'; ?></div>

  <h1><?php echo HEADING_TITLE; ?></h1>
</div>

<div id="infoBox_lDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_DEFINITION_GROUPS; ?></th>
        <th><?php echo TABLE_HEADING_TOTAL_DEFINITIONS; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>
<?php
  $Qgroups = $osC_Database->query('select distinct content_group, count(*) as total_entries from :table_languages_definitions where languages_id = :languages_id group by content_group order by content_group');
  $Qgroups->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
  $Qgroups->bindInt(':languages_id', $_GET['lID']);
  $Qgroups->execute();

  while ($Qgroups->next()) {
    if (!isset($dInfo) && (!isset($_GET['group']) || (isset($_GET['group']) && ($_GET['group'] == $Qgroups->value('content_group'))))) {
      $dInfo = new objectInfo($Qgroups->toArray());
    }

    if (isset($dInfo) && ($Qgroups->value('content_group') == $dInfo->content_group)) {
      echo '      <tr class="selected">' . "\n";
    } else {
      echo '      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" onclick="document.location.href=\'' . tep_href_link(FILENAME_LANGUAGES_DEFINITIONS, 'lID=' . $_GET['lID'] . '&group=' . $Qgroups->value('content_group')) . '\';">' . "\n";
    }
?>
        <td><?php echo '<a href="' . tep_href_link(FILENAME_LANGUAGES_DEFINITIONS, 'lID=' . $_GET['lID'] . '&group=' . $Qgroups->value('content_group') . '&action=lDefine') . '">' . tep_image('images/icons/folder.gif', ICON_FOLDER) . '&nbsp;' . $Qgroups->value('content_group') . '</a>'; ?></td>
        <td align="right"><?php echo $Qgroups->value('total_entries'); ?></td>
        <td align="right">
<?php
    if (isset($dInfo) && ($Qgroups->value('content_group') == $dInfo->content_group)) {
      echo '<a href="#" onclick="toggleInfoBox(\'lDelete\');">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
    } else {
      echo '<a href="' . tep_href_link(FILENAME_LANGUAGES_DEFINITIONS, 'lID=' . $_GET['lID'] . '&group=' . $Qgroups->value('content_group') . '&action=lDelete') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
    }
?>
        </td>
      </tr>
<?php
  }
?>
    </tbody>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_INSERT . '" onclick="toggleInfoBox(\'lNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_lNew" <?php if ($action != 'lNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/new.png', IMAGE_INSERT, '16', '16') . ' ' . TEXT_INFO_HEADING_NEW_LANGUAGE_DEFINITION; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('lNew', FILENAME_LANGUAGES_DEFINITIONS, 'lID=' . $_GET['lID'] . '&action=insert'); ?>

    <p><?php echo TEXT_INFO_INSERT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_DEFINITION_KEY . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('key', '', 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_DEFINITION_VALUE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('value', '', 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_DEFINITION_GROUP . '</b>'; ?></td>
        <td class="smallText" width="60%">
<?php
  if (empty($groups_array) === false) {
    echo osc_draw_pull_down_menu('group', $groups_array, '', 'style="width: 30%;"') . '&nbsp;&nbsp;<b>' . TEXT_INFO_LANGUAGE_DEFINITION_GROUP_NEW . '</b>&nbsp;';
  }

  echo osc_draw_input_field('group_new', '', 'style="width: ' . (empty($groups_array) ? '100%' : '40%') . ';"');
?>
        </td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'lDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($dInfo)) {
?>

<div id="infoBox_lDelete" <?php if ($action != 'lDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . ' ' . $dInfo->content_group; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('lDelete', FILENAME_LANGUAGES_DEFINITIONS, 'lID=' . $_GET['lID'] . '&group=' . $dInfo->content_group . '&action=deleteconfirm'); ?>

    <p><?php echo TEXT_INFO_DELETE_INTRO; ?></p>
    <p><?php echo '<b>' . $dInfo->content_group . '</b>'; ?></p>
<?php
    $Qdefs = $osC_Database->query('select id, definition_key from :table_languages_definitions where languages_id = :languages_id and content_group = :content_group order by definition_key');
    $Qdefs->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
    $Qdefs->bindInt(':languages_id', $_GET['lID']);
    $Qdefs->bindValue(':content_group', $dInfo->content_group);
    $Qdefs->execute();

    $defs_array = array();

    while ($Qdefs->next()) {
      $defs_array[] = array('id' => $Qdefs->valueInt('id'), 'text' => $Qdefs->value('definition_key'));
    }
?>
    <p>(<a href="javascript:selectAllFromPullDownMenu('delDefs');"><u>select all</u></a> | <a href="javascript:resetPullDownMenuSelection('delDefs');"><u>select none</u></a>)<br /><?php echo osc_draw_pull_down_menu('defs[]', $defs_array, '', 'id="delDefs" size="10" multiple="multiple" style="width: 100%;"'); ?></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="resetPullDownMenuSelection(\'delDefs\'); toggleInfoBox(\'lDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  }
?>

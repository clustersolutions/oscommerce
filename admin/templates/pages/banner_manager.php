<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  $groups_array = array();
  $Qgroups = $osC_Database->query('select distinct banners_group from :table_banners order by banners_group');
  $Qgroups->bindTable(':table_banners', TABLE_BANNERS);
  $Qgroups->execute();

  while ($Qgroups->next()) {
    $groups_array[] = array('id' => $Qgroups->value('banners_group'), 'text' => $Qgroups->value('banners_group'));
  }
?>

<style type="text/css">@import url('external/jscalendar/calendar-win2k-1.css');</style>
<script type="text/javascript" src="external/jscalendar/calendar.js"></script>
<script type="text/javascript" src="external/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="external/jscalendar/calendar-setup.js"></script>

<h1><?php echo HEADING_TITLE; ?></h1>

<div id="infoBox_bDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_BANNERS; ?></th>
        <th><?php echo TABLE_HEADING_GROUPS; ?></th>
        <th><?php echo TABLE_HEADING_STATISTICS; ?></th>
        <th><?php echo TABLE_HEADING_STATUS; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>
<?php
  $Qbanners = $osC_Database->query('select banners_id, banners_title, banners_group, status from :table_banners order by banners_title, banners_group');
  $Qbanners->bindTable(':table_banners', TABLE_BANNERS);
  $Qbanners->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qbanners->execute();

  while ($Qbanners->next()) {
    $Qinfo = $osC_Database->query('select banners_url, banners_image, banners_html_text, date_format(date_scheduled, "%Y-%m-%d") as date_scheduled, date_format(expires_date, "%Y-%m-%d") as expires_date, expires_impressions from :table_banners where banners_id = :banners_id');
    $Qinfo->bindTable(':table_banners', TABLE_BANNERS);
    $Qinfo->bindInt(':banners_id', $Qbanners->valueInt('banners_id'));
    $Qinfo->execute();

    $Qstats = $osC_Database->query('select sum(banners_shown) as banners_shown, sum(banners_clicked) as banners_clicked from :table_banners_history where banners_id = :banners_id');
    $Qstats->bindTable(':table_banners_history', TABLE_BANNERS_HISTORY);
    $Qstats->bindInt(':banners_id', $Qbanners->valueInt('banners_id'));
    $Qstats->execute();

    if (!isset($bInfo) && (!isset($_GET['bID']) || (isset($_GET['bID']) && ($_GET['bID'] == $Qbanners->valueInt('banners_id'))))) {
      $bInfo = new objectInfo(array_merge($Qbanners->toArray(), $Qinfo->toArray(), $Qstats->toArray()));
    }

    if (isset($bInfo) && ($Qbanners->valueInt('banners_id') == $bInfo->banners_id)) {
      echo '      <tr class="selected">' . "\n";
    } else {
      echo '      <tr onMouseOver="rowOverEffect(this);" onMouseOut="rowOutEffect(this);" onClick="document.location.href=\'' . tep_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $Qbanners->valueInt('banners_id')) . '\';">' . "\n";
    }
?>
        <td><?php echo $Qbanners->valueProtected('banners_title'); ?></td>
        <td><?php echo $Qbanners->valueProtected('banners_group'); ?></td>
        <td><?php echo $Qstats->valueInt('banners_shown') . ' / ' . $Qstats->valueInt('banners_clicked'); ?></td>
        <td align="center"><?php echo tep_image('templates/' . $template . '/images/icons/' . (($Qbanners->valueInt('status') === 1) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif')); ?></td>
        <td align="right">
<?php
    if (isset($bInfo) && ($Qbanners->valueInt('banners_id') == $bInfo->banners_id)) {
      echo '<a href="#" onClick="toggleInfoBox(\'bPreview\');">' . tep_image('templates/' . $template . '/images/icons/16x16/windows.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;';
    } else {
      echo '<a href="' . tep_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $Qbanners->valueInt('banners_id') . '&action=bPreview') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/windows.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;';
    }

    echo '<a href="#" onClick="document.location.href=\'' . tep_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $Qbanners->valueInt('banners_id') . '&action=statistics') . '\';">' . tep_image('templates/' . $template . '/images/icons/16x16/graph.png', ICON_STATISTICS, '16', '16') . '</a>&nbsp;';

    if (isset($bInfo) && ($Qbanners->valueInt('banners_id') == $bInfo->banners_id)) {
      echo '<a href="#" onClick="toggleInfoBox(\'bEdit\');">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="#" onClick="toggleInfoBox(\'bDelete\');">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
    } else {
      echo '<a href="' . tep_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $Qbanners->valueInt('banners_id') . '&action=bEdit') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;' .
           '<a href="' . tep_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $Qbanners->valueInt('banners_id') . '&action=bDelete') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>';
    }
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
      <td class="smallText"><?php echo $Qbanners->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_BANNERS); ?></td>
      <td class="smallText" align="right"><?php echo $Qbanners->displayBatchLinksPullDown(); ?></td>
    </tr>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_NEW_BANNER . '" class="infoBoxButton" onClick="toggleInfoBox(\'bNew\');">'; ?></p>
</div>

<div id="infoBox_bNew" <?php if ($action != 'bNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/new.png', IMAGE_INSERT, '16', '16') . ' ' . TEXT_INFO_HEADING_NEW_BANNER; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('bNew', FILENAME_BANNER_MANAGER, 'action=save', 'post', 'enctype="multipart/form-data"'); ?>

    <p><?php echo TEXT_INFO_INSERT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_BANNERS_TITLE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('banners_title', '', 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_BANNERS_URL . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('banners_url', '', 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_BANNERS_GROUP . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('banners_group', $groups_array) . TEXT_BANNERS_NEW_GROUP . '<br>' . osc_draw_input_field('new_banners_group', '', 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_BANNERS_IMAGE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_file_field('banners_image') . ' ' . TEXT_BANNERS_IMAGE_LOCAL . '<br>' . realpath('../images/') . '/' . osc_draw_input_field('banners_image_local'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_BANNERS_IMAGE_TARGET . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo realpath('../images') . '/' . osc_draw_input_field('banners_image_target'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_BANNERS_HTML_TEXT . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo tep_draw_textarea_field('banners_html_text', 'soft', '60', '5'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_BANNERS_SCHEDULED_AT . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('date_scheduled', '', 'id="calendarValueDS"'); ?><input type="button" value="..." id="calendarTriggerDS" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "calendarValueDS", ifFormat: "%Y-%m-%d", button: "calendarTriggerDS" } );</script></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_BANNERS_EXPIRES_ON . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('date_expires', '', 'id="calendarValueDE"'); ?><input type="button" value="..." id="calendarTriggerDE" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "calendarValueDE", ifFormat: "%Y-%m-%d", button: "calendarTriggerDE" } );</script><?php echo TEXT_BANNERS_OR_AT . '<br>' . osc_draw_input_field('expires_impressions', '', 'maxlength="7" size="7"') . ' ' . TEXT_BANNERS_IMPRESSIONS; ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_BANNERS_STATUS . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_checkbox_field('status'); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onClick="toggleInfoBox(\'bDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>

  <p><?php echo TEXT_BANNERS_BANNER_NOTE . '<br>' . TEXT_BANNERS_INSERT_NOTE . '<br>' . TEXT_BANNERS_EXPIRCY_NOTE . '<br>' . TEXT_BANNERS_SCHEDULE_NOTE; ?></p>
</div>

<?php
  if (isset($bInfo)) {
?>

<div id="infoBox_bPreview" <?php if ($action != 'bPreview') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . ' ' . $bInfo->banners_title; ?></div>
  <div class="infoBoxContent">

<?php
    if (!empty($bInfo->banners_html_text)) {
      echo $bInfo->banners_html_text;
    } else {
      echo tep_image('../images/' . $bInfo->banners_image, $bInfo->banners_title);
    }
?>

    <p align="center"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onClick="toggleInfoBox(\'bDefault\');" class="operationButton">'; ?></p>
  </div>
</div>

<div id="infoBox_bDelete" <?php if ($action != 'bDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . ' ' . $bInfo->banners_title; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('bDelete', FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $bInfo->banners_id . '&action=deleteconfirm'); ?>

    <p><?php echo TEXT_INFO_DELETE_INTRO; ?></p>
    <p><?php echo '<b>' . $bInfo->banners_title . '</b>'; ?></p>

<?php
    if (!empty($bInfo->banners_image)) {
      echo '    <p>' . osc_draw_checkbox_field('delete_image', array(array('id' => 'on', 'text' => TEXT_INFO_DELETE_IMAGE)), true) . '</p>';
    }
?>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onClick="toggleInfoBox(\'bDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>


<div id="infoBox_bEdit" <?php if ($action != 'bEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . ' ' . $bInfo->banners_title; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('bEdit', FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $bInfo->banners_id . '&action=save', 'post', 'enctype="multipart/form-data"'); ?>

    <p><?php echo TEXT_INFO_INSERT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_BANNERS_TITLE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('banners_title', $bInfo->banners_title, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_BANNERS_URL . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('banners_url', $bInfo->banners_url, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_BANNERS_GROUP . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('banners_group', $groups_array, $bInfo->banners_group) . TEXT_BANNERS_NEW_GROUP . '<br>' . osc_draw_input_field('new_banners_group', '', 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_BANNERS_IMAGE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_file_field('banners_image') . ' ' . TEXT_BANNERS_IMAGE_LOCAL . '<br>' . realpath('../images/') . '/' . osc_draw_input_field('banners_image_local', $bInfo->banners_image); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_BANNERS_IMAGE_TARGET . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo realpath('../images') . '/' . osc_draw_input_field('banners_image_target'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_BANNERS_HTML_TEXT . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo tep_draw_textarea_field('banners_html_text', 'soft', '60', '5', $bInfo->banners_html_text); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_BANNERS_SCHEDULED_AT . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('date_scheduled', $bInfo->date_scheduled, 'id="calendarValueDSE"'); ?><input type="button" value="..." id="calendarTriggerDSE" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "calendarValueDSE", ifFormat: "%Y-%m-%d", button: "calendarTriggerDSE" } );</script></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_BANNERS_EXPIRES_ON . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('date_expires', $bInfo->expires_date, 'id="calendarValueDEE"'); ?><input type="button" value="..." id="calendarTriggerDEE" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "calendarValueDEE", ifFormat: "%Y-%m-%d", button: "calendarTriggerDEE" } );</script><?php echo TEXT_BANNERS_OR_AT . '<br>' . osc_draw_input_field('expires_impressions', $bInfo->expires_impressions, 'maxlength="7" size="7"') . ' ' . TEXT_BANNERS_IMPRESSIONS; ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_BANNERS_STATUS . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_checkbox_field('status', 'on', (($bInfo->status == 1) ? true : false)); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onClick="toggleInfoBox(\'bDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>

  <p><?php echo TEXT_BANNERS_BANNER_NOTE . '<br>' . TEXT_BANNERS_INSERT_NOTE . '<br>' . TEXT_BANNERS_EXPIRCY_NOTE . '<br>' . TEXT_BANNERS_SCHEDULE_NOTE; ?></p>
</div>

<?php
  }
?>

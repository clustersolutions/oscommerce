<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

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

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div id="infoBox_bDefault" <?php if (!empty($_GET['action'])) { echo 'style="display: none;"'; } ?>>
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
?>

      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
        <td><?php echo $Qbanners->valueProtected('banners_title'); ?></td>
        <td><?php echo $Qbanners->valueProtected('banners_group'); ?></td>
        <td><?php echo $Qstats->valueInt('banners_shown') . ' / ' . $Qstats->valueInt('banners_clicked'); ?></td>
        <td align="center"><?php echo osc_icon(($Qbanners->valueInt('status') === 1) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif', null, null); ?></td>
        <td align="right">

<?php
    if (isset($bInfo) && ($Qbanners->valueInt('banners_id') == $bInfo->banners_id)) {
      echo osc_link_object('#', osc_icon('windows.png', IMAGE_PREVIEW), 'onclick="toggleInfoBox(\'bPreview\');"') . '&nbsp;';
    } else {
      echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&bID=' . $Qbanners->valueInt('banners_id') . '&action=bPreview'), osc_icon('windows.png', IMAGE_PREVIEW)) . '&nbsp;';
    }

    echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&bID=' . $Qbanners->valueInt('banners_id') . '&action=statistics'), osc_icon('graph.png', ICON_STATISTICS)) . '&nbsp;';

    if (isset($bInfo) && ($Qbanners->valueInt('banners_id') == $bInfo->banners_id)) {
      echo osc_link_object('#', osc_icon('configure.png', IMAGE_EDIT), 'onclick="toggleInfoBox(\'bEdit\');"') . '&nbsp;' .
           osc_link_object('#', osc_icon('trash.png', IMAGE_DELETE), 'onclick="toggleInfoBox(\'bDelete\');"');
    } else {
      echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&bID=' . $Qbanners->valueInt('banners_id') . '&action=bEdit'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;' .
           osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&bID=' . $Qbanners->valueInt('banners_id') . '&action=bDelete'), osc_icon('trash.png', IMAGE_DELETE));
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
      <td><?php echo $Qbanners->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_BANNERS); ?></td>
      <td align="right"><?php echo $Qbanners->displayBatchLinksPullDown('page', $osC_Template->getModule()); ?></td>
    </tr>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_NEW_BANNER . '" class="infoBoxButton" onclick="toggleInfoBox(\'bNew\');">'; ?></p>
</div>

<div id="infoBox_bNew" <?php if ($_GET['action'] != 'bNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_NEW_BANNER; ?></div>
  <div class="infoBoxContent">
    <form name="bNew" action="<?php echo osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=save'); ?>" method="post" enctype="multipart/form-data">

    <p><?php echo TEXT_INFO_INSERT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_BANNERS_TITLE . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_input_field('banners_title', null, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_BANNERS_URL . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_input_field('banners_url', null, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_BANNERS_GROUP . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_pull_down_menu('banners_group', $groups_array) . TEXT_BANNERS_NEW_GROUP . '<br />' . osc_draw_input_field('new_banners_group', null, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_BANNERS_IMAGE . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_file_field('banners_image', true) . ' ' . TEXT_BANNERS_IMAGE_LOCAL . '<br />' . realpath('../images/') . '/' . osc_draw_input_field('banners_image_local'); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_BANNERS_IMAGE_TARGET . '</b>'; ?></td>
        <td width="60%"><?php echo realpath('../images') . '/' . osc_draw_input_field('banners_image_target'); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_BANNERS_HTML_TEXT . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_textarea_field('banners_html_text'); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_BANNERS_SCHEDULED_AT . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_input_field('date_scheduled'); ?><input type="button" value="..." id="calendarTriggerDS" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "date_scheduled", ifFormat: "%Y-%m-%d", button: "calendarTriggerDS" } );</script></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_BANNERS_EXPIRES_ON . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_input_field('date_expires'); ?><input type="button" value="..." id="calendarTriggerDE" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "date_expires", ifFormat: "%Y-%m-%d", button: "calendarTriggerDE" } );</script><?php echo TEXT_BANNERS_OR_AT . '<br />' . osc_draw_input_field('expires_impressions', null, 'maxlength="7" size="7"') . ' ' . TEXT_BANNERS_IMPRESSIONS; ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_BANNERS_STATUS . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_checkbox_field('status'); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'bDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>

  <p><?php echo TEXT_BANNERS_BANNER_NOTE . '<br />' . TEXT_BANNERS_INSERT_NOTE . '<br />' . TEXT_BANNERS_EXPIRCY_NOTE . '<br />' . TEXT_BANNERS_SCHEDULE_NOTE; ?></p>
</div>

<?php
  if (isset($bInfo)) {
?>

<div id="infoBox_bPreview" <?php if ($_GET['action'] != 'bPreview') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $bInfo->banners_title; ?></div>
  <div class="infoBoxContent">

<?php
    if (!empty($bInfo->banners_html_text)) {
      echo $bInfo->banners_html_text;
    } else {
      echo osc_image('../images/' . $bInfo->banners_image, $bInfo->banners_title);
    }
?>

    <p align="center"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="toggleInfoBox(\'bDefault\');" class="operationButton">'; ?></p>
  </div>
</div>

<div id="infoBox_bDelete" <?php if ($_GET['action'] != 'bDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $bInfo->banners_title; ?></div>
  <div class="infoBoxContent">
    <form name="bDelete" action="<?php echo osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&bID=' . $bInfo->banners_id . '&action=deleteconfirm'); ?>" method="post">

    <p><?php echo TEXT_INFO_DELETE_INTRO; ?></p>
    <p><?php echo '<b>' . $bInfo->banners_title . '</b>'; ?></p>

<?php
    if (!empty($bInfo->banners_image)) {
      echo '    <p>' . osc_draw_checkbox_field('delete_image', array(array('id' => 'on', 'text' => TEXT_INFO_DELETE_IMAGE)), true) . '</p>';
    }
?>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'bDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>


<div id="infoBox_bEdit" <?php if ($_GET['action'] != 'bEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $bInfo->banners_title; ?></div>
  <div class="infoBoxContent">
    <form name="bEdit" action="<?php echo osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&bID=' . $bInfo->banners_id . '&action=save'); ?>" method="post" enctype="multipart/form-data">

    <p><?php echo TEXT_INFO_EDIT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_BANNERS_TITLE . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_input_field('banners_title', $bInfo->banners_title, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_BANNERS_URL . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_input_field('banners_url', $bInfo->banners_url, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_BANNERS_GROUP . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_pull_down_menu('banners_group', $groups_array, $bInfo->banners_group) . TEXT_BANNERS_NEW_GROUP . '<br />' . osc_draw_input_field('new_banners_group', null, 'style="width: 100%;"'); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_BANNERS_IMAGE . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_file_field('banners_image', true) . ' ' . TEXT_BANNERS_IMAGE_LOCAL . '<br />' . realpath('../images/') . '/' . osc_draw_input_field('banners_image_local', $bInfo->banners_image); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_BANNERS_IMAGE_TARGET . '</b>'; ?></td>
        <td width="60%"><?php echo realpath('../images') . '/' . osc_draw_input_field('banners_image_target'); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_BANNERS_HTML_TEXT . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_textarea_field('banners_html_text', $bInfo->banners_html_text); ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_BANNERS_SCHEDULED_AT . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_input_field('date_scheduled', $bInfo->date_scheduled); ?><input type="button" value="..." id="calendarTriggerDSE" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "date_scheduled", ifFormat: "%Y-%m-%d", button: "calendarTriggerDSE" } );</script></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_BANNERS_EXPIRES_ON . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_input_field('date_expires', $bInfo->expires_date); ?><input type="button" value="..." id="calendarTriggerDEE" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "date_expires", ifFormat: "%Y-%m-%d", button: "calendarTriggerDEE" } );</script><?php echo TEXT_BANNERS_OR_AT . '<br />' . osc_draw_input_field('expires_impressions', $bInfo->expires_impressions, 'maxlength="7" size="7"') . ' ' . TEXT_BANNERS_IMPRESSIONS; ?></td>
      </tr>
      <tr>
        <td width="40%"><?php echo '<b>' . TEXT_BANNERS_STATUS . '</b>'; ?></td>
        <td width="60%"><?php echo osc_draw_checkbox_field('status', 'on', ($bInfo->status == 1)); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'bDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>

  <p><?php echo TEXT_BANNERS_BANNER_NOTE . '<br />' . TEXT_BANNERS_INSERT_NOTE . '<br />' . TEXT_BANNERS_EXPIRCY_NOTE . '<br />' . TEXT_BANNERS_SCHEDULE_NOTE; ?></p>
</div>

<?php
  }
?>

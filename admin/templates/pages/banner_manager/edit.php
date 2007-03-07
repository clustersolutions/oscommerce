<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $Qgroups = $osC_Database->query('select distinct banners_group from :table_banners order by banners_group');
  $Qgroups->bindTable(':table_banners', TABLE_BANNERS);
  $Qgroups->execute();

  $groups_array = array();

  while ( $Qgroups->next() ) {
    $groups_array[] = array('id' => $Qgroups->value('banners_group'),
                            'text' => $Qgroups->value('banners_group'));
  }

  $osC_ObjectInfo = new osC_ObjectInfo(osC_BannerManager_Admin::getData($_GET['bID']));
?>

<style type="text/css">@import url('external/jscalendar/calendar-win2k-1.css');</style>
<script type="text/javascript" src="external/jscalendar/calendar.js"></script>
<script type="text/javascript" src="external/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="external/jscalendar/calendar-setup.js"></script>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $osC_ObjectInfo->get('banners_title'); ?></div>
<div class="infoBoxContent">
  <form name="bEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&bID=' . $osC_ObjectInfo->get('banners_id') . '&action=save'); ?>" method="post" enctype="multipart/form-data">

  <p><?php echo TEXT_INFO_EDIT_INTRO; ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_BANNERS_TITLE . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('title', $osC_ObjectInfo->get('banners_title'), 'style="width: 100%;"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_BANNERS_URL . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('url', $osC_ObjectInfo->get('banners_url'), 'style="width: 100%;"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_BANNERS_GROUP . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_pull_down_menu('group', $groups_array, $osC_ObjectInfo->get('banners_group')) . TEXT_BANNERS_NEW_GROUP . '<br />' . osc_draw_input_field('group_new', null, 'style="width: 100%;"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_BANNERS_IMAGE . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_file_field('image', true) . ' ' . TEXT_BANNERS_IMAGE_LOCAL . '<br />' . realpath('../images/') . '/' . osc_draw_input_field('image_local', $osC_ObjectInfo->get('banners_image')); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_BANNERS_IMAGE_TARGET . '</b>'; ?></td>
      <td width="60%"><?php echo realpath('../images') . '/' . osc_draw_input_field('image_target'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_BANNERS_HTML_TEXT . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_textarea_field('html_text', $osC_ObjectInfo->get('banners_html_text')); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_BANNERS_SCHEDULED_AT . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('date_scheduled', $osC_ObjectInfo->get('date_scheduled')); ?><input type="button" value="..." id="calendarTriggerDS" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "date_scheduled", ifFormat: "%Y-%m-%d", button: "calendarTriggerDS" } );</script></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_BANNERS_EXPIRES_ON . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('date_expires', $osC_ObjectInfo->get('expires_date')); ?><input type="button" value="..." id="calendarTriggerDE" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "date_expires", ifFormat: "%Y-%m-%d", button: "calendarTriggerDE" } );</script><?php echo TEXT_BANNERS_OR_AT . '<br />' . osc_draw_input_field('expires_impressions', null, 'maxlength="7" size="7"') . ' ' . TEXT_BANNERS_IMPRESSIONS; ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_BANNERS_STATUS . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_checkbox_field('status', null, (($osC_ObjectInfo->get('status') == '1') ? true : false)); ?></td>
    </tr>
  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

<p><?php echo TEXT_BANNERS_BANNER_NOTE . '<br />' . TEXT_BANNERS_INSERT_NOTE . '<br />' . TEXT_BANNERS_EXPIRCY_NOTE . '<br />' . TEXT_BANNERS_SCHEDULE_NOTE; ?></p>

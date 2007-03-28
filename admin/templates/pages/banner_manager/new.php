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

<div class="infoBoxHeading"><?php echo osc_icon('new.png') . ' ' . $osC_Language->get('action_heading_new_banner'); ?></div>
<div class="infoBoxContent">
  <form name="bNew" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=save'); ?>" method="post" enctype="multipart/form-data">

  <p><?php echo $osC_Language->get('introduction_new_banner'); ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_title') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('title', null, 'style="width: 100%;"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_url') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('url', null, 'style="width: 100%;"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_group') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_pull_down_menu('group', $groups_array) . $osC_Language->get('field_group_new') . '<br />' . osc_draw_input_field('group_new', null, 'style="width: 100%;"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_image') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_file_field('image', true) . ' ' . $osC_Language->get('field_image_local') . '<br />' . realpath('../images/') . '/' . osc_draw_input_field('image_local'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_image_target') . '</b>'; ?></td>
      <td width="60%"><?php echo realpath('../images') . '/' . osc_draw_input_field('image_target'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_html_text') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_textarea_field('html_text'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_scheduled_date') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('date_scheduled'); ?><input type="button" value="..." id="calendarTriggerDS" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "date_scheduled", ifFormat: "%Y-%m-%d", button: "calendarTriggerDS" } );</script></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_expiry_date') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('date_expires'); ?><input type="button" value="..." id="calendarTriggerDE" class="operationButton"><script type="text/javascript">Calendar.setup( { inputField: "date_expires", ifFormat: "%Y-%m-%d", button: "calendarTriggerDE" } );</script></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_maximum_impressions') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('expires_impressions', null, 'maxlength="7" size="7"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_status') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_checkbox_field('status'); ?></td>
    </tr>
  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

<p><?php echo $osC_Language->get('info_banner_fields'); ?></p>

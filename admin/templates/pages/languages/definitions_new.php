<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $Qgroups = $osC_Database->query('select distinct content_group from :table_languages_definitions where languages_id = :languages_id order by content_group');
  $Qgroups->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
  $Qgroups->bindInt(':languages_id', $_GET[$osC_Template->getModule()]);
  $Qgroups->execute();

  $groups_array = array();

  while ($Qgroups->next()) {
    $groups_array[] = array('id' => $Qgroups->value('content_group'), 'text' => $Qgroups->value('content_group'));
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . $osC_Language->get('action_heading_new_language_definition'); ?></div>
<div class="infoBoxContent">
  <form name="lNew" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&action=insertDefinition'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_new_language_definition'); ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_definition_key') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('key', null, 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_definition_value') . '</b>'; ?></td>
      <td width="60%">

<?php
  foreach ($osC_Language->getAll() as $l) {
    echo $osC_Language->showImage($l['code'], null, null, 'style="vertical-align: top; padding-top: 5px; margin-left: -20px;"') . '&nbsp;' . osc_draw_textarea_field('value[' . $l['id'] . ']', null, 60, 4, 'style="width: 99%;"') . '<br />';
  }
?>

      </td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_definition_group') . '</b>'; ?></td>
      <td width="60%">

<?php
  if (!empty($groups_array)) {
    echo osc_draw_pull_down_menu('group', $groups_array, null, 'style="width: 30%;"') . '&nbsp;&nbsp;<b>' . $osC_Language->get('field_definition_new_group') . '</b>&nbsp;';
  }

  echo osc_draw_input_field('group_new', null, 'style="width: ' . (empty($groups_array) ? '100%' : '40%') . ';"');
?>

      </td>
    </tr>
  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

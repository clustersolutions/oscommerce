<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

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

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_NEW_LANGUAGE_DEFINITION; ?></div>
<div class="infoBoxContent">
  <form name="lNew" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&action=insertDefinition'); ?>" method="post">

  <p><?php echo TEXT_INFO_INSERT_DEFINITION_INTRO; ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_DEFINITION_KEY . '</b>'; ?></td>
      <td class="smallText" width="60%"><?php echo osc_draw_input_field('key', null, 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_DEFINITION_VALUE . '</b>'; ?></td>
      <td class="smallText" width="60%">

<?php
  foreach ($osC_Language->getAll() as $l) {
    echo osc_image('../includes/languages/' . $l['code'] . '/images/' . $l['image'], $l['name'], null, null, 'style="vertical-align: top; padding-top: 5px; margin-left: -20px;"') . '&nbsp;' . osc_draw_textarea_field('value[' . $l['id'] . ']', null, 60, 4, 'style="width: 99%;"') . '<br />';
  }
?>

      </td>
    </tr>
    <tr>
      <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_DEFINITION_GROUP . '</b>'; ?></td>
      <td class="smallText" width="60%">

<?php
  if (!empty($groups_array)) {
    echo osc_draw_pull_down_menu('group', $groups_array, null, 'style="width: 30%;"') . '&nbsp;&nbsp;<b>' . TEXT_INFO_LANGUAGE_DEFINITION_GROUP_NEW . '</b>&nbsp;';
  }

  echo osc_draw_input_field('group_new', null, 'style="width: ' . (empty($groups_array) ? '100%' : '40%') . ';"');
?>

      </td>
    </tr>
  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

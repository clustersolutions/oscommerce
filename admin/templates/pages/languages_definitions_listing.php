<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&lID=' . $_GET['lID']), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div id="infoBox_lDefine">
  <div class="infoBoxHeading"><?php echo osc_icon('edit.png', IMAGE_EDIT) . ' ' . $_GET['group']; ?></div>
  <div class="infoBoxContent">
    <form name="lDefine" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&lID=' . $_GET['lID'] . '&group=' . $_GET['group'] . '&action=save'); ?>" method="post">

    <p><?php echo TEXT_INFO_EDIT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
    $Qdefs = $osC_Database->query('select definition_key, definition_value from :table_languages_definitions where languages_id = :languages_id and content_group = :content_group order by definition_key');
    $Qdefs->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
    $Qdefs->bindInt(':languages_id', $_GET['lID']);
    $Qdefs->bindValue(':content_group', $_GET['group']);
    $Qdefs->execute();

    while ($Qdefs->next()) {
?>

      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . $Qdefs->value('definition_key') . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_textarea_field('def[' . $Qdefs->value('definition_key') . ']', $Qdefs->value('definition_value'), 60, 4, 'style="width: 100%"'); ?></td>
      </tr>

<?php
  }
?>

    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&lID=' . $_GET['lID'] . '&group=' . $_GET['group']) . '\';" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

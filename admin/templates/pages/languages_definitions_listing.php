<?php
/*
  $Id: languages.php 387 2006-01-18 16:49:58Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<div id="infoBox_lDefine">
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/edit.png', IMAGE_EDIT, '16', '16') . ' ' . $_GET['group']; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('lDefine', FILENAME_LANGUAGES_DEFINITIONS, 'lID=' . $_GET['lID'] . '&group=' . $_GET['group'] . '&action=save'); ?>

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
        <td class="smallText" width="60%"><?php echo osc_draw_textarea_field('def[' . $Qdefs->value('definition_key') . ']', $Qdefs->value('definition_value'), '60', '4', 'soft', 'style="width: 100%"'); ?></td>
      </tr>

<?php
  }
?>

    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . tep_href_link(FILENAME_LANGUAGES_DEFINITIONS, 'lID=' . $_GET['lID'] . '&group=' . $_GET['group']) . '\';" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

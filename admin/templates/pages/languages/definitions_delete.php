<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $_GET['group']; ?></div>
<div class="infoBoxContent">
  <form name="lDelete" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page'] . '&group=' . $_GET['group'] . '&action=deleteDefinitions'); ?>" method="post">

  <p><?php echo TEXT_INFO_DELETE_DEFINITION_INTRO; ?></p>
  <p><?php echo '<b>' . $_GET['group'] . '</b>'; ?></p>

<?php
  $Qdefs = $osC_Database->query('select id, definition_key from :table_languages_definitions where languages_id = :languages_id and content_group = :content_group order by definition_key');
  $Qdefs->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
  $Qdefs->bindInt(':languages_id', $_GET[$osC_Template->getModule()]);
  $Qdefs->bindValue(':content_group', $_GET['group']);
  $Qdefs->execute();

  $defs_array = array();

  while ($Qdefs->next()) {
    $defs_array[] = array('id' => $Qdefs->valueInt('id'), 'text' => $Qdefs->value('definition_key'));
  }
?>

  <p>(<a href="javascript:selectAllFromPullDownMenu('defs');"><u>select all</u></a> | <a href="javascript:resetPullDownMenuSelection('defs');"><u>select none</u></a>)<br /><?php echo osc_draw_pull_down_menu('defs[]', $defs_array, null, 'id="defs" size="10" multiple="multiple" style="width: 100%;"'); ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

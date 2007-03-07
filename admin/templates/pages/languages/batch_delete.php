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

<div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' Batch Delete'; ?></div>
<div class="infoBoxContent">
  <form name="aDeleteBatch" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchDelete'); ?>" method="post">

  <p><?php echo TEXT_DELETE_BATCH_INTRO; ?></p>

<?php
  $check_default_flag = false;

  $Qlanguages = $osC_Database->query('select languages_id, name, code from :table_languages where languages_id in (":languages_id") order by name');
  $Qlanguages->bindTable(':table_languages', TABLE_LANGUAGES);
  $Qlanguages->bindRaw(':languages_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qlanguages->execute();

  $names_string = '';

  while ($Qlanguages->next()) {
    if ( $Qlanguages->value('code') == DEFAULT_LANGUAGE ) {
      $check_default_flag = true;
    }

    $names_string .= osc_draw_hidden_field('batch[]', $Qlanguages->valueInt('languages_id')) . '<b>' . $Qlanguages->value('name') . ' (' . $Qlanguages->value('code') . ')</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2) . osc_draw_hidden_field('subaction', 'confirm');
  }

  echo '<p>' . $names_string . '</p>';

  if ( $check_default_flag === false ) {
    echo '<p align="center"><input type="submit" value="' . IMAGE_DELETE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton"></p>';
  } else {
    echo '<p><b>' . TEXT_INFO_DELETE_PROHIBITED . '</b></p>';

    echo '<p align="center"><input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton"></p>';
  }
?>

  </form>
</div>

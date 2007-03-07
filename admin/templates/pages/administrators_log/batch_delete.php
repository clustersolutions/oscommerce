<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' Batch Delete'; ?></div>
<div class="infoBoxContent">
  <form name="lDeleteBatch" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu'] . '&action=batchDelete'); ?>" method="post">

  <p><?php echo TEXT_DELETE_BATCH_INTRO; ?></p>

<?php
  $Qlog = $osC_Database->query('select al.id, al.module, al.module_action, al.module_id, a.user_name from :table_administrators_log al, :table_administrators a where al.id in (":id") and al.administrators_id = a.id group by id order by id');
  $Qlog->bindTable(':table_administrators_log', TABLE_ADMINISTRATORS_LOG);
  $Qlog->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
  $Qlog->bindRaw(':id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qlog->execute();

  $names_string = '';

  while ( $Qlog->next() ) {
    $names_string .= osc_draw_hidden_field('batch[]', $Qlog->valueInt('id')) . '<b>' . $Qlog->valueProtected('user_name') . ' &raquo; ' . $Qlog->value('module_action') . ' &raquo; ' . $Qlog->value('module') . ' &raquo; ' . $Qlog->value('module_id') . '<br />';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -6);
  }

  echo '<p>' . $names_string . '</p>';
?>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&fm=' . $_GET['fm'] . '&fu=' . $_GET['fu']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

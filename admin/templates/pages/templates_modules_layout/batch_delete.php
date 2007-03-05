<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' Batch Delete'; ?></div>
<div class="infoBoxContent">
  <form name="lDeleteBatch" action="<?php echo osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter'] . '&action=batchDelete'); ?>" method="post">

  <p><?php echo TEXT_DELETE_BATCH_INTRO; ?></p>

<?php
  $Qlayout = $osC_Database->query('select b2p.*, b.title as box_title from :table_templates_boxes_to_pages b2p, :table_templates_boxes b where b2p.id in (":id") and b2p.templates_boxes_id = b.id order by b.title');
  $Qlayout->bindTable(':table_templates_boxes_to_pages', TABLE_TEMPLATES_BOXES_TO_PAGES);
  $Qlayout->bindTable(':table_templates_boxes', TABLE_TEMPLATES_BOXES);
  $Qlayout->bindRaw(':id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qlayout->execute();

  $names_string = '';

  while ( $Qlayout->next() ) {
    $names_string .= osc_draw_hidden_field('batch[]', $Qlayout->valueInt('id')) . '<b>' . $Qlayout->value('box_title') . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2);
  }

  echo '<p>' . $names_string . '</p>';
?>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&set=' . $_GET['set'] . '&filter=' . $_GET['filter']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

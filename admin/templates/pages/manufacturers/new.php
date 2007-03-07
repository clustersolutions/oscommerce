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
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_HEADING_NEW_MANUFACTURER; ?></div>
<div class="infoBoxContent">
  <form name="mNew" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=save'); ?>" method="post" enctype="multipart/form-data">

  <p><?php echo TEXT_NEW_INTRO; ?></p>

  <p><?php echo TEXT_MANUFACTURERS_NAME . '<br />' . osc_draw_input_field('manufacturers_name'); ?></p>
  <p><?php echo TEXT_MANUFACTURERS_IMAGE . '<br />' . osc_draw_file_field('manufacturers_image', true); ?></p>

  <p>

<?php
  echo TEXT_MANUFACTURERS_URL;

  foreach ( $osC_Language->getAll() as $l ) {
    echo '<br />' . osc_image('../includes/languages/' . $l['code'] . '/images/' . $l['image'], $l['name']) . '&nbsp;' . osc_draw_input_field('manufacturers_url[' . $l['id'] . ']');
  }
?>

  </p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Tax_Admin::getData($_GET['tcID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $osC_ObjectInfo->get('tax_class_title'); ?></div>
<div class="infoBoxContent">
  <form name="tcEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&tcID=' . $osC_ObjectInfo->get('tax_class_id') . '&action=save'); ?>" method="post">

  <p><?php echo TEXT_INFO_EDIT_INTRO; ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_INFO_CLASS_TITLE . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('tax_class_title', $osC_ObjectInfo->get('tax_class_title'), 'style="width: 100%;"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_INFO_CLASS_DESCRIPTION . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('tax_class_description', $osC_ObjectInfo->get('tax_class_description'), 'style="width: 100%;"'); ?></td>
    </tr>
  </table>

  <p><?php echo TEXT_INFO_LAST_MODIFIED . ' ' . (($osC_ObjectInfo->get('last_modified') > $osC_ObjectInfo->get('date_added')) ? osC_DateTime::getShort($osC_ObjectInfo->get('last_modified')) : osC_DateTime::getShort($osC_ObjectInfo->get('date_added'))); ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

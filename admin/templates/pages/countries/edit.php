<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Countries_Admin::getData($_GET['cID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->get('countries_name'); ?></div>
<div class="infoBoxContent">
  <form name="cEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cID=' . $osC_ObjectInfo->get('countries_id') . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_edit_country'); ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_name') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('countries_name', $osC_ObjectInfo->get('countries_name'), 'style="width: 100%;"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_iso_code_2') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('countries_iso_code_2', $osC_ObjectInfo->get('countries_iso_code_2'), 'style="width: 100%;"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_iso_code_3') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('countries_iso_code_3', $osC_ObjectInfo->get('countries_iso_code_3'), 'style="width: 100%;"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_address_format') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_textarea_field('address_format', $osC_ObjectInfo->get('address_format')); ?></td>
    </tr>
  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

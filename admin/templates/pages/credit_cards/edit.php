<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_CreditCards_Admin::getData($_GET['ccID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->get('credit_card_name'); ?></div>
<div class="infoBoxContent">
  <form name="ccEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&ccID=' . $osC_ObjectInfo->get('id') . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_edit_card'); ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_name') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('credit_card_name', $osC_ObjectInfo->get('credit_card_name'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_pattern') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('pattern', $osC_ObjectInfo->get('pattern'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_sort_order') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('sort_order', $osC_ObjectInfo->get('sort_order'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_status') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_checkbox_field('credit_card_status', '1', $osC_ObjectInfo->get('credit_card_status')); ?></td>
    </tr>
  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

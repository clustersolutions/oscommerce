<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo($osC_Currencies->getData($_GET['cID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $osC_ObjectInfo->get('title'); ?></div>
<div class="infoBoxContent">
  <form name="cEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&cID=' . $osC_ObjectInfo->get('id') . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_edit_currency'); ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_title') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('title', $osC_ObjectInfo->get('title'), 'style="width: 100%;"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_code') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('code', $osC_ObjectInfo->get('code'), 'style="width: 100%;"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_symbol_left') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('symbol_left', $osC_ObjectInfo->get('symbol_left'), 'style="width: 100%;"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_symbol_right') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('symbol_right', $osC_ObjectInfo->get('symbol_right'), 'style="width: 100%;"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_decimal_places') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('decimal_places', $osC_ObjectInfo->get('decimal_places'), 'style="width: 100%;"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_currency_value') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('value', $osC_ObjectInfo->get('value'), 'style="width: 100%;"'); ?></td>
    </tr>

<?php
    if ( $osC_ObjectInfo->get('code') != DEFAULT_CURRENCY ) {
?>

    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_set_default') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_checkbox_field('default'); ?></td>
    </tr>

<?php
    }
?>

  </table>

  <p align="center">

<?php
  if ( $osC_ObjectInfo->get('code') == DEFAULT_CURRENCY ) {
    echo osc_draw_hidden_field('is_default', 'true');
  }

  echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />';
?>

  </p>

  </form>
</div>

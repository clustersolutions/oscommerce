<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $currencies_array = array();

  foreach ($osC_Currencies->getData() as $currency) {
    $currencies_array[] = array('id' => $currency['id'],
                                'text' => $currency['title']);
  }

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Language_Admin::getData($_GET['lID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->get('name'); ?></div>
<div class="infoBoxContent">
  <form name="lEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&lID=' . $osC_ObjectInfo->get('languages_id') . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_edit_language'); ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_name') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('name', $osC_ObjectInfo->get('name'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_code') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('code', $osC_ObjectInfo->get('code'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_locale') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('locale', $osC_ObjectInfo->get('locale'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_character_set') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('charset', $osC_ObjectInfo->get('charset'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_text_direction') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_pull_down_menu('text_direction', array(array('id' => 'ltr', 'text' => 'ltr'), array('id' => 'rtl', 'text' => 'rtl')), $osC_ObjectInfo->get('text_direction'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_date_format_short') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('date_format_short', $osC_ObjectInfo->get('date_format_short'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_date_format_long') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('date_format_long', $osC_ObjectInfo->get('date_format_long'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_time_format') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('time_format', $osC_ObjectInfo->get('time_format'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_currency') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_pull_down_menu('currencies_id', $currencies_array, $osC_ObjectInfo->get('currencies_id'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_currency_separator_decimal') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('numeric_separator_decimal', $osC_ObjectInfo->get('numeric_separator_decimal'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_currency_separator_thousands') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('numeric_separator_thousands', $osC_ObjectInfo->get('numeric_separator_thousands'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_sort_order') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('sort_order', $osC_ObjectInfo->get('sort_order'), 'style="width: 100%"'); ?></td>
    </tr>

<?php
    if ($osC_ObjectInfo->get('code') != DEFAULT_LANGUAGE) {
?>

    <tr>
      <td width="40%"><?php echo '<b>' . $osC_Language->get('field_set_default') . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_checkbox_field('default'); ?></td>
    </tr>

<?php
    }
?>

  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

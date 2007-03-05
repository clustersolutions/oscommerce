<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $currencies_array = array();

  foreach ($osC_Currencies->getData() as $currency) {
    $currencies_array[] = array('id' => $currency['id'],
                                'text' => $currency['title']);
  }

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Language_Admin::getData($_GET['lID']));
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $osC_ObjectInfo->get('name'); ?></div>
<div class="infoBoxContent">
  <form name="lEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&lID=' . $osC_ObjectInfo->get('languages_id') . '&action=save'); ?>" method="post">

  <p><?php echo TEXT_INFO_EDIT_INTRO; ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_NAME . '</b>'; ?></td>
      <td class="smallText" width="60%"><?php echo osc_draw_input_field('name', $osC_ObjectInfo->get('name'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_CODE . '</b>'; ?></td>
      <td class="smallText" width="60%"><?php echo osc_draw_input_field('code', $osC_ObjectInfo->get('code'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_LOCALE . '</b>'; ?></td>
      <td class="smallText" width="60%"><?php echo osc_draw_input_field('locale', $osC_ObjectInfo->get('locale'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_CHARSET . '</b>'; ?></td>
      <td class="smallText" width="60%"><?php echo osc_draw_input_field('charset', $osC_ObjectInfo->get('charset'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_TEXT_DIRECTION . '</b>'; ?></td>
      <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('text_direction', array(array('id' => 'ltr', 'text' => 'ltr'), array('id' => 'rtl', 'text' => 'rtl')), $osC_ObjectInfo->get('text_direction'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_DATE_FORMAT_SHORT . '</b>'; ?></td>
      <td class="smallText" width="60%"><?php echo osc_draw_input_field('date_format_short', $osC_ObjectInfo->get('date_format_short'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_DATE_FORMAT_LONG . '</b>'; ?></td>
      <td class="smallText" width="60%"><?php echo osc_draw_input_field('date_format_long', $osC_ObjectInfo->get('date_format_long'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_TIME_FORMAT . '</b>'; ?></td>
      <td class="smallText" width="60%"><?php echo osc_draw_input_field('time_format', $osC_ObjectInfo->get('time_format'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_DEFAULT_CURRENCY . '</b>'; ?></td>
      <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('currencies_id', $currencies_array, $osC_ObjectInfo->get('currencies_id'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_NUMERIC_SEPARATOR_DECIMAL . '</b>'; ?></td>
      <td class="smallText" width="60%"><?php echo osc_draw_input_field('numeric_separator_decimal', $osC_ObjectInfo->get('numeric_separator_decimal'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_NUMERIC_SEPARATOR_THOUSANDS . '</b>'; ?></td>
      <td class="smallText" width="60%"><?php echo osc_draw_input_field('numeric_separator_thousands', $osC_ObjectInfo->get('numeric_separator_thousands'), 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_SORT_ORDER . '</b>'; ?></td>
      <td class="smallText" width="60%"><?php echo osc_draw_input_field('sort_order', $osC_ObjectInfo->get('sort_order'), 'style="width: 100%"'); ?></td>
    </tr>

<?php
    if ($osC_ObjectInfo->get('code') != DEFAULT_LANGUAGE) {
?>

    <tr>
      <td class="smallText" width="40%"><?php echo '<b>' . TEXT_SET_DEFAULT . '</b>'; ?></td>
      <td class="smallText" width="60%"><?php echo osc_draw_checkbox_field('default'); ?></td>
    </tr>

<?php
    }
?>

  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

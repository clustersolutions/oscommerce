<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $languages_array = array(array('id' => '0',
                                 'text' => $osC_Language->get('none')));

  foreach ( osc_toObjectInfo(osC_Languages_Admin::getAll(-1))->get('entries') as $l ) {
    if ( $l['languages_id'] != $_GET['lID'] ) {
      $languages_array[] = array('id' => $l['languages_id'],
                                 'text' => $l['name'] . ' (' . $l['code'] . ')');
    }
  }

  $currencies_array = array();

  foreach ( osc_toObjectInfo(osC_Currencies_Admin::getAll(-1))->get('entries') as $c ) {
    $currencies_array[] = array('id' => $c['currencies_id'],
                                'text' => $c['title']);
  }

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Languages_Admin::get($_GET['lID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->exists($osC_Template->getModule()) ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . $osC_ObjectInfo->getProtected('name'); ?></div>
<div class="infoBoxContent">
  <form name="lEdit" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&lID=' . $osC_ObjectInfo->getInt('languages_id') . '&action=save'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_edit_language'); ?></p>

  <fieldset>
    <div><label for="name"><?php echo $osC_Language->get('field_name'); ?></label><?php echo osc_draw_input_field('name', $osC_ObjectInfo->get('name')); ?></div>
    <div><label for="code"><?php echo $osC_Language->get('field_code'); ?></label><?php echo osc_draw_input_field('code', $osC_ObjectInfo->get('code')); ?></div>
    <div><label for="locale"><?php echo $osC_Language->get('field_locale'); ?></label><?php echo osc_draw_input_field('locale', $osC_ObjectInfo->get('locale')); ?></div>
    <div><label for="charset"><?php echo $osC_Language->get('field_character_set'); ?></label><?php echo osc_draw_input_field('charset', $osC_ObjectInfo->get('charset')); ?></div>
    <div><label for="text_direction"><?php echo $osC_Language->get('field_text_direction'); ?></label><?php echo osc_draw_pull_down_menu('text_direction', array(array('id' => 'ltr', 'text' => 'ltr'), array('id' => 'rtl', 'text' => 'rtl')), $osC_ObjectInfo->get('text_direction')); ?></div>
    <div><label for="date_format_short"><?php echo $osC_Language->get('field_date_format_short'); ?></label><?php echo osc_draw_input_field('date_format_short', $osC_ObjectInfo->get('date_format_short')); ?></div>
    <div><label for="date_format_long"><?php echo $osC_Language->get('field_date_format_long'); ?></label><?php echo osc_draw_input_field('date_format_long', $osC_ObjectInfo->get('date_format_long')); ?></div>
    <div><label for="time_format"><?php echo $osC_Language->get('field_time_format'); ?></label><?php echo osc_draw_input_field('time_format', $osC_ObjectInfo->get('time_format')); ?></div>
    <div><label for="currencies_id"><?php echo $osC_Language->get('field_currency'); ?></label><?php echo osc_draw_pull_down_menu('currencies_id', $currencies_array, $osC_ObjectInfo->get('currencies_id')); ?></div>
    <div><label for="numeric_separator_decimal"><?php $osC_Language->get('field_currency_separator_decimal'); ?></label><?php echo osc_draw_input_field('numeric_separator_decimal', $osC_ObjectInfo->get('numeric_separator_decimal')); ?></div>
    <div><label for="numeric_separator_thousands"><?php $osC_Language->get('field_currency_separator_thousands'); ?></label><?php echo osc_draw_input_field('numeric_separator_thousands', $osC_ObjectInfo->get('numeric_separator_thousands')); ?></div>
    <div><label for="parent_id"><?php echo $osC_Language->get('field_parent_language'); ?></label><?php echo osc_draw_pull_down_menu('parent_id', $languages_array, $osC_ObjectInfo->get('parent_id')); ?></div>
    <div><label for="sort_order"><?php echo $osC_Language->get('field_sort_order'); ?></label><?php echo osc_draw_input_field('sort_order', $osC_ObjectInfo->get('sort_order')); ?></div>

<?php
    if ( $osC_ObjectInfo->get('code') != DEFAULT_LANGUAGE ) {
?>

    <div><label for="default"><?php echo $osC_Language->get('field_set_default'); ?></label><?php echo osc_draw_checkbox_field('default'); ?></div>

<?php
    }
?>

  </fieldset>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>

<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\OSCOM;
?>

<?php echo osc_image(DIR_WS_IMAGES . $OSCOM_Template->getPageImage(), $OSCOM_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists('Search') ) {
    echo $OSCOM_MessageStack->get('Search');
  }
?>

<form name="search" action="<?php echo OSCOM::getLink(null, null, null, 'NONSSL', false); ?>" method="get" onsubmit="return check_form(this);">

<?php
  echo osc_draw_hidden_field('Search', null);
?>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('search_criteria_title'); ?></h6>

  <div class="content">
    <?php echo osc_draw_input_field('Q', null, 'style="width: 99%;"'); ?>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo osc_draw_image_submit_button('button_search.gif', OSCOM::getDef('button_search')); ?></span>

  <?php echo osc_link_object('javascript:popupWindow(\'' . OSCOM::getLink(null, null, 'Help') . '\');', OSCOM::getDef('search_help_tips')); ?>
</div>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('advanced_search_heading'); ?></h6>

  <div class="content">
    <ol>
      <li>

<?php
  echo osc_draw_label(OSCOM::getDef('field_search_categories'), 'category');

  $OSCOM_CategoryTree->setSpacerString('&nbsp;', 2);

  $categories_array = array(array('id' => '',
                                  'text' => OSCOM::getDef('filter_all_categories')));

  foreach ( $OSCOM_CategoryTree->buildBranchArray(0) as $category ) {
    $categories_array[] = array('id' => $category['id'],
                                'text' => $category['title']);
  }

  echo osc_draw_pull_down_menu('category', $categories_array);
?>

      </li>
      <li><?php echo osc_draw_checkbox_field('recursive', array(array('id' => '1', 'text' => OSCOM::getDef('field_search_recursive'))), true); ?></li>
      <li>

<?php
  echo osc_draw_label(OSCOM::getDef('field_search_manufacturers'), 'manufacturer');

  $manufacturers_array = array(array('id' => '', 'text' => OSCOM::getDef('filter_all_manufacturers')));

  $Qmanufacturers = $OSCOM_Database->query('select manufacturers_id, manufacturers_name from :table_manufacturers order by manufacturers_name');
  $Qmanufacturers->execute();

  while ( $Qmanufacturers->next() ) {
    $manufacturers_array[] = array('id' => $Qmanufacturers->valueInt('manufacturers_id'),
                                   'text' => $Qmanufacturers->value('manufacturers_name'));
  }

  echo osc_draw_pull_down_menu('manufacturer', $manufacturers_array);
?>

      </li>
      <li><?php echo osc_draw_label(OSCOM::getDef('field_search_price_from'), 'pfrom') . osc_draw_input_field('pfrom'); ?></li>
      <li><?php echo osc_draw_label(OSCOM::getDef('field_search_price_to'), 'pto') . osc_draw_input_field('pto'); ?></li>
      <li><?php echo osc_draw_label(OSCOM::getDef('field_search_date_from'), 'datefrom') . osc_draw_date_pull_down_menu('datefrom', null, false, null, null, date('Y') - $OSCOM_Search->getMinYear(), 0); ?></li>
      <li><?php echo osc_draw_label(OSCOM::getDef('field_search_date_to'), 'dateto') . osc_draw_date_pull_down_menu('dateto', null, null, null, null, date('Y') - $OSCOM_Search->getMaxYear(), 0); ?></li>
    </ol>
  </div>
</div>

<?php
  echo osc_draw_hidden_session_id_field();
?>

</form>

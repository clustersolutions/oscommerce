<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo tep_image(DIR_WS_IMAGES . $osC_Template->getPageImage(), $osC_Template->getPageTitle(), null, null, 'id="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<?php
  if ($messageStack->size('search') > 0) {
    echo $messageStack->output('search');
  }
?>

<form name="search" action="<?php echo tep_href_link(FILENAME_SEARCH, '', 'NONSSL', false); ?>" method="get" onsubmit="return check_form(this);">

<div class="moduleBox">
  <h6><?php echo $osC_Language->get('search_criteria_title'); ?></h6>

  <div class="content">
    <?php echo osc_draw_input_field('keywords', null, 'style="width: 99%;"'); ?>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo tep_hide_session_id() . tep_image_submit('button_search.gif', $osC_Language->get('button_search')); ?></span>

  <?php echo osc_link_object('javascript:popupWindow(\'' . tep_href_link(FILENAME_SEARCH, 'help') . '\');', $osC_Language->get('search_help_tips')); ?>
</div>

<div class="moduleBox">
  <h6><?php echo $osC_Language->get('advanced_search_heading'); ?></h6>

  <div class="content">
    <ol>
      <li><?php echo osc_draw_label($osC_Language->get('field_search_categories'), 'category') . osc_draw_pull_down_menu('category', tep_get_categories(array(array('id' => '', 'text' => $osC_Language->get('filter_all_categories'))))); ?></li>
      <li><?php echo osc_draw_checkbox_field('recursive', array(array('id' => '1', 'text' => $osC_Language->get('field_search_recursive'))), true); ?></li>
      <li><?php echo osc_draw_label($osC_Language->get('field_search_manufacturers'), 'manufacturer') . osc_draw_pull_down_menu('manufacturer', tep_get_manufacturers(array(array('id' => '', 'text' => $osC_Language->get('filter_all_manufacturers'))))); ?></li>
      <li><?php echo osc_draw_label($osC_Language->get('field_search_price_from'), 'pfrom') . osc_draw_input_field('pfrom'); ?></li>
      <li><?php echo osc_draw_label($osC_Language->get('field_search_price_to'), 'pto') . osc_draw_input_field('pto'); ?></li>
      <li><?php echo osc_draw_label($osC_Language->get('field_search_date_from'), 'datefrom') . tep_draw_date_pull_down_menu('datefrom', '', false, true, true, date('Y') - $osC_Search->getMinYear(), 0); ?></li>
      <li><?php echo osc_draw_label($osC_Language->get('field_search_date_to'), 'dateto') . tep_draw_date_pull_down_menu('dateto', '', true, true, true, date('Y') - $osC_Search->getMaxYear(), 0); ?></li>
    </ol>
  </div>
</div>

</form>

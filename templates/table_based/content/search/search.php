<?php
/*
  $Id: advanced_search.php 199 2005-09-22 17:56:13 +0200 (Do, 22 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo tep_image(DIR_WS_IMAGES . $osC_Template->getPageImage(), $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<?php
  if ($messageStack->size('search') > 0) {
    echo $messageStack->output('search');
  }
?>

<form name="search" action="<?php echo tep_href_link(FILENAME_SEARCH, '', 'NONSSL', false); ?>" method="get" onsubmit="return check_form(this);">

<div class="moduleBox">
  <div class="outsideHeading"><?php echo $osC_Language->get('search_criteria_title'); ?></div>

  <div class="content">
    <table border="0" width="100%" cellspacing="2" cellpadding="2">
      <tr>
        <td><?php echo osc_draw_input_field('keywords', '', 'style="width: 99%;"'); ?></td>
      </tr>
    </table>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo tep_hide_session_id() . tep_image_submit('button_search.gif', $osC_Language->get('button_search')); ?></span>

  <?php echo '<a href="javascript:popupWindow(\'' . tep_href_link(FILENAME_POPUP_SEARCH_HELP) . '\')">' . $osC_Language->get('search_help_tips') . '</a>'; ?>
</div>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo $osC_Language->get('advanced_search_heading'); ?></div>

  <div class="content">
    <table border="0" cellspacing="2" cellpadding="2">
      <tr>
        <td><?php echo $osC_Language->get('field_search_categories'); ?></td>
        <td><?php echo osc_draw_pull_down_menu('category', tep_get_categories(array(array('id' => '', 'text' => $osC_Language->get('filter_all_categories'))))); ?></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><?php echo osc_draw_checkbox_field('recursive', '1', true) . ' ' . $osC_Language->get('field_search_recursive'); ?></td>
      </tr>
      <tr>
       <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><?php echo $osC_Language->get('field_search_manufacturers'); ?></td>
        <td><?php echo osc_draw_pull_down_menu('manufacturer', tep_get_manufacturers(array(array('id' => '', 'text' => $osC_Language->get('filter_all_manufacturers'))))); ?></td>
      </tr>
      <tr>
       <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><?php echo $osC_Language->get('field_search_price_from'); ?></td>
        <td><?php echo osc_draw_input_field('pfrom'); ?></td>
      </tr>
      <tr>
        <td><?php echo $osC_Language->get('field_search_price_to'); ?></td>
        <td><?php echo osc_draw_input_field('pto'); ?></td>
      </tr>
      <tr>
       <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><?php echo $osC_Language->get('field_search_date_from'); ?></td>
        <td><?php echo tep_draw_date_pull_down_menu('datefrom', '', false, true, true, date('Y') - $osC_Search->getMinYear(), 0); ?></td>
      </tr>
      <tr>
        <td><?php echo $osC_Language->get('field_search_date_to'); ?></td>
        <td><?php echo tep_draw_date_pull_down_menu('dateto', '', true, true, true, date('Y') - $osC_Search->getMaxYear(), 0); ?></td>
      </tr>
    </table>
  </div>
</div>

</form>

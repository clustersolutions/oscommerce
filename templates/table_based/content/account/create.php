<?php
/*
  $Id:account_edit.php 187 2005-09-14 14:22:13 +0200 (Mi, 14 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_account.gif', $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<?php
  if ($messageStack->size('create') > 0) {
    echo $messageStack->output('create');
  }
?>

<form name="create" action="<?php echo tep_href_link(FILENAME_ACCOUNT, 'create=save', 'SSL'); ?>" method="post" onsubmit="return check_form(create);">

<div class="moduleBox">
  <div class="outsideHeading">
    <span class="inputRequirement" style="float: right;"><?php echo $osC_Language->get('form_required_information'); ?></span>

    <?php echo $osC_Language->get('my_account_title'); ?>
  </div>

  <div class="content">
    <table border="0" cellspacing="2" cellpadding="2">

<?php
  if (ACCOUNT_GENDER > -1) {
    $gender_array = array(array('id' => 'm', 'text' => $osC_Language->get('gender_male')),
                          array('id' => 'f', 'text' => $osC_Language->get('gender_female')));
?>
      <tr>
        <td><?php echo $osC_Language->get('field_customer_gender'); ?></td>
        <td><?php echo osc_draw_radio_field('gender', $gender_array, '', '', (ACCOUNT_GENDER > 0)); ?></td>
      </tr>
      <tr>
       <td colspan="2">&nbsp;</td>
      </tr>
<?php
  }
?>

      <tr>
        <td><?php echo $osC_Language->get('field_customer_first_name'); ?></td>
        <td><?php echo osc_draw_input_field('firstname', '', '', true); ?></td>
      </tr>
      <tr>
        <td><?php echo $osC_Language->get('field_customer_last_name'); ?></td>
        <td><?php echo osc_draw_input_field('lastname', '', '', true); ?></td>
      </tr>

<?php
  if (ACCOUNT_DATE_OF_BIRTH > -1) {
?>
      <tr>
       <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><?php echo $osC_Language->get('field_customer_date_of_birth'); ?></td>
        <td><?php echo tep_draw_date_pull_down_menu('dob', '', false, true, true, date('Y')-1901, -5) . '&nbsp;<span class="inputRequirement">*</span>'; ?></td>
      </tr>
<?php
  }
?>

      <tr>
       <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><?php echo $osC_Language->get('field_customer_email_address'); ?></td>
        <td><?php echo osc_draw_input_field('email_address', '', '', true); ?></td>
      </tr>

<?php
  if (ACCOUNT_NEWSLETTER > -1) {
?>
      <tr>
        <td><?php echo $osC_Language->get('field_customer_newsletter'); ?></td>
        <td><?php echo osc_draw_checkbox_field('newsletter', '1'); ?></td>
      </tr>
<?php
  }
?>

      <tr>
       <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><?php echo $osC_Language->get('field_customer_password'); ?></td>
        <td><?php echo osc_draw_password_field('password', '', '', true); ?></td>
      </tr>
      <tr>
        <td><?php echo $osC_Language->get('field_customer_password_confirmation'); ?></td>
        <td><?php echo osc_draw_password_field('confirmation', '', '', true); ?></td>
      </tr>
    </table>
  </div>
</div>

<?php
  if (DISPLAY_PRIVACY_CONDITIONS == 'true') {
?>

<div class="moduleBox">
  <div class="outsideHeading">
    <?php echo $osC_Language->get('create_account_terms_heading'); ?>
  </div>

  <div class="content">
    <table border="0" cellspacing="2" cellpadding="2">
      <tr>
        <td class="main"><?php echo sprintf($osC_Language->get('create_account_terms_description'), tep_href_link(FILENAME_INFO, 'privacy', 'AUTO')) . '<br /><br />' . osc_draw_checkbox_field('privacy_conditions', '1', false, 'id="privacy"') . '<label for="privacy">&nbsp;' . $osC_Language->get('create_account_terms_confirm') . '</label>'; ?></td>
      </tr>
    </table>
  </div>
</div>

<?php
  }
?>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo tep_image_submit('button_continue.gif', $osC_Language->get('button_continue')); ?></span>

  <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image_button('button_back.gif', $osC_Language->get('button_back')) . '</a>'; ?>
</div>

</form>

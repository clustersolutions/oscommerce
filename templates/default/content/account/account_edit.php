<?php
/*
  $Id:account_edit.php 187 2005-09-14 14:22:13 +0200 (Mi, 14 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  $Qaccount = osC_Account::getEntry();
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_account.gif', $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<?php
  if ($messageStack->size('account_edit') > 0) {
    echo $messageStack->output('account_edit');
  }
?>

<form name="account_edit" action="<?php echo tep_href_link(FILENAME_ACCOUNT, 'edit=save', 'SSL'); ?>" method="post" onsubmit="return check_form(account_edit);">

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
        <td><?php echo osc_draw_radio_field('gender', $gender_array, $Qaccount->value('customers_gender'), '', (ACCOUNT_GENDER > 0)); ?></td>
      </tr>
      <tr>
       <td colspan="2">&nbsp;</td>
      </tr>
<?php
  }
?>

      <tr>
        <td><?php echo $osC_Language->get('field_customer_first_name'); ?></td>
        <td><?php echo osc_draw_input_field('firstname', $Qaccount->value('customers_firstname'), '', true); ?></td>
      </tr>
      <tr>
        <td><?php echo $osC_Language->get('field_customer_last_name'); ?></td>
        <td><?php echo osc_draw_input_field('lastname', $Qaccount->value('customers_lastname'), '', true); ?></td>
      </tr>

<?php
  if (ACCOUNT_DATE_OF_BIRTH == '1') {
?>
      <tr>
       <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><?php echo $osC_Language->get('field_customer_date_of_birth'); ?></td>
        <td><?php echo tep_draw_date_pull_down_menu('dob', $Qaccount->value('customers_dob'), false, true, true, date('Y')-1901, -5) . '&nbsp;<span class="inputRequirement">*</span>'; ?></td>
      </tr>
<?php
  }
?>

      <tr>
       <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><?php echo $osC_Language->get('field_customer_email_address'); ?></td>
        <td><?php echo osc_draw_input_field('email_address', $Qaccount->value('customers_email_address'), '', true); ?></td>
      </tr>
    </table>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo tep_image_submit('button_continue.gif', $osC_Language->get('button_continue')); ?></span>

  <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image_button('button_back.gif', $osC_Language->get('button_back')) . '</a>'; ?>
</div>

</form>

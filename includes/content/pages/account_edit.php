<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/form_check.js.php');

  $Qaccount = $osC_Database->query('select customers_gender, customers_firstname, customers_lastname, unix_timestamp(customers_dob) as customers_dob, customers_email_address from :table_customers where customers_id = :customers_id');
  $Qaccount->bindTable(':table_customers', TABLE_CUSTOMERS);
  $Qaccount->bindInt(':customers_id', $osC_Customer->id);
  $Qaccount->execute();
?>

<div class="pageHeading">
  <span class="pageHeadingImage"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_account.gif', HEADING_TITLE_ACCOUNT_EDIT, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></span>

  <h1><?php echo HEADING_TITLE_ACCOUNT_EDIT; ?></h1>
</div>

<?php
  if ($messageStack->size('account_edit') > 0) {
    echo $messageStack->output('account_edit');
  }
?>

<form name="account_edit" action="<?php echo tep_href_link(FILENAME_ACCOUNT, 'edit=save', 'SSL'); ?>" method="post" onSubmit="return check_form(account_edit);">

<div class="moduleBox">
  <div class="outsideHeading">
    <span class="inputRequirement" style="float: right;"><?php echo FORM_REQUIRED_INFORMATION; ?></span>

    <?php echo MY_ACCOUNT_TITLE; ?>
  </div>

  <div class="content">
    <table border="0" cellspacing="2" cellpadding="2">

<?php
  if (ACCOUNT_GENDER > -1) {
    $gender_array = array(array('id' => 'm', 'text' => MALE),
                          array('id' => 'f', 'text' => FEMALE));
?>
      <tr>
        <td><?php echo ENTRY_GENDER; ?></td>
        <td><?php echo osc_draw_radio_field('gender', $gender_array, $Qaccount->value('customers_gender'), '', (ACCOUNT_GENDER > 0)); ?></td>
      </tr>
      <tr>
       <td colspan="2">&nbsp;</td>
      </tr>
<?php
  }
?>

      <tr>
        <td><?php echo ENTRY_FIRST_NAME; ?></td>
        <td><?php echo osc_draw_input_field('firstname', $Qaccount->value('customers_firstname'), '', true); ?></td>
      </tr>
      <tr>
        <td><?php echo ENTRY_LAST_NAME; ?></td>
        <td><?php echo osc_draw_input_field('lastname', $Qaccount->value('customers_lastname'), '', true); ?></td>
      </tr>

<?php
  if (ACCOUNT_DATE_OF_BIRTH > -1) {
?>
      <tr>
       <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><?php echo ENTRY_DATE_OF_BIRTH; ?></td>
        <td><?php echo tep_draw_date_pull_down_menu('dob', $Qaccount->value('customers_dob'), false, true, true, date('Y')-1901, -5) . '&nbsp;<span class="inputRequirement">*</span>'; ?></td>
      </tr>
<?php
  }
?>

      <tr>
       <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
        <td><?php echo osc_draw_input_field('email_address', $Qaccount->value('customers_email_address'), '', true); ?></td>
      </tr>
    </table>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></span>

  <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?>
</div>

</form>

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

  if (empty($_GET['create'])) {
?>

<p><?php echo sprintf(TEXT_CREATE_ORIGIN_LOGIN, tep_href_link(FILENAME_ACCOUNT, 'login', 'SSL')); ?></p>

<?php
  }
?>

<form name="create" action="<?php echo tep_href_link(FILENAME_ACCOUNT, 'create=save', 'SSL'); ?>" method="post" onsubmit="return check_form(create);">

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
        <td><?php echo osc_draw_radio_field('gender', $gender_array, '', '', (ACCOUNT_GENDER > 0)); ?></td>
      </tr>
      <tr>
       <td colspan="2">&nbsp;</td>
      </tr>
<?php
  }
?>

      <tr>
        <td><?php echo ENTRY_FIRST_NAME; ?></td>
        <td><?php echo osc_draw_input_field('firstname', '', '', true); ?></td>
      </tr>
      <tr>
        <td><?php echo ENTRY_LAST_NAME; ?></td>
        <td><?php echo osc_draw_input_field('lastname', '', '', true); ?></td>
      </tr>

<?php
  if (ACCOUNT_DATE_OF_BIRTH > -1) {
?>
      <tr>
       <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><?php echo ENTRY_DATE_OF_BIRTH; ?></td>
        <td><?php echo tep_draw_date_pull_down_menu('dob', '', false, true, true, date('Y')-1901, -5) . '&nbsp;<span class="inputRequirement">*</span>'; ?></td>
      </tr>
<?php
  }
?>

      <tr>
       <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
        <td><?php echo osc_draw_input_field('email_address', '', '', true); ?></td>
      </tr>

<?php
  if (ACCOUNT_NEWSLETTER > -1) {
?>
      <tr>
        <td><?php echo ENTRY_NEWSLETTER; ?></td>
        <td><?php echo osc_draw_checkbox_field('newsletter', '1'); ?></td>
      </tr>
<?php
  }
?>

      <tr>
       <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><?php echo ENTRY_PASSWORD; ?></td>
        <td><?php echo osc_draw_password_field('password', '', '', true); ?></td>
      </tr>
      <tr>
        <td><?php echo ENTRY_PASSWORD_CONFIRMATION; ?></td>
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
    <?php echo HEADING_PRIVACY_CONDITIONS; ?>
  </div>

  <div class="content">
    <table border="0" cellspacing="2" cellpadding="2">
      <tr>
        <td class="main"><?php echo TEXT_PRIVACY_CONDITIONS_DESCRIPTION . '<br /><br />' . osc_draw_checkbox_field('privacy_conditions', '1', false, 'id="privacy"') . '<label for="privacy">&nbsp;' . TEXT_PRIVACY_CONDITIONS_CONFIRM . '</label>'; ?></td>
      </tr>
    </table>
  </div>
</div>

<?php
  }
?>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></span>

  <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?>
</div>

</form>

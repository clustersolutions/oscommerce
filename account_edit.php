<?php
/*
  $Id: account_edit.php,v 1.69 2004/05/24 10:53:11 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if ($osC_Customer->isLoggedOn() == false) {
    $navigation->set_snapshot();

    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

// needs to be included earlier to set the success message in the messageStack
  require(DIR_WS_LANGUAGES . $osC_Session->value('language') . '/' . FILENAME_ACCOUNT_EDIT);

  if (isset($_POST['action']) && ($_POST['action'] == 'process')) {
    if (ACCOUNT_GENDER > 0) {
      if (!isset($_POST['gender']) || (($_POST['gender'] != 'm') && ($_POST['gender'] != 'f'))) {
        $messageStack->add('account_edit', ENTRY_GENDER_ERROR);
      }
    }

    if (!isset($_POST['firstname']) || (strlen(trim($_POST['firstname'])) < ACCOUNT_FIRST_NAME)) {
      $messageStack->add('account_edit', ENTRY_FIRST_NAME_ERROR);
    }

    if (!isset($_POST['lastname']) || (strlen(trim($_POST['lastname'])) < ACCOUNT_LAST_NAME)) {
      $messageStack->add('account_edit', ENTRY_LAST_NAME_ERROR);
    }

    if (ACCOUNT_DATE_OF_BIRTH > -1) {
      if (isset($_POST['dob_days']) && isset($_POST['dob_months']) && isset($_POST['dob_years']) && checkdate($_POST['dob_months'], $_POST['dob_days'], $_POST['dob_years'])) {
        $dob = mktime(0, 0, 0, $_POST['dob_months'], $_POST['dob_days'], $_POST['dob_years']);
      } else {
        $messageStack->add('account_edit', ENTRY_DATE_OF_BIRTH_ERROR);
      }
    }

    if (!isset($_POST['email_address']) || (strlen(trim($_POST['email_address'])) < ACCOUNT_EMAIL_ADDRESS)) {
      $messageStack->add('account_edit', ENTRY_EMAIL_ADDRESS_ERROR);
    } else {
      if (tep_validate_email($_POST['email_address']) == false) {
        $messageStack->add('account_edit', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
      } else {
        $Qcheck = $osC_Database->query('select customers_id from :table_customers where customers_email_address = :customers_email_address and customers_id != :customers_id limit 1');
        $Qcheck->bindRaw(':table_customers', TABLE_CUSTOMERS);
        $Qcheck->bindValue(':customers_email_address', trim($_POST['email_address']));
        $Qcheck->bindInt(':customers_id', $osC_Customer->id);
        $Qcheck->execute();

        if ($Qcheck->numberOfRows() > 0) {
          $messageStack->add('account_edit', ENTRY_EMAIL_ADDRESS_ERROR_EXISTS);
        }

        $Qcheck->freeResult();
      }
    }

    if ($messageStack->size('account_edit') === 0) {
      $Qcustomer = $osC_Database->query('update :table_customers set customers_gender = :customers_gender, customers_firstname = :customers_firstname, customers_lastname = :customers_lastname, customers_email_address = :customers_email_address, customers_dob = :customers_dob where customers_id = :customers_id');
      $Qcustomer->bindRaw(':table_customers', TABLE_CUSTOMERS);
      $Qcustomer->bindValue(':customers_gender', (((ACCOUNT_GENDER > -1) && isset($_POST['gender']) && (($_POST['gender'] == 'm') || ($_POST['gender'] == 'f'))) ? $_POST['gender'] : ''));
      $Qcustomer->bindValue(':customers_firstname', trim($_POST['firstname']));
      $Qcustomer->bindValue(':customers_lastname', trim($_POST['lastname']));
      $Qcustomer->bindValue(':customers_email_address', trim($_POST['email_address']));
      $Qcustomer->bindValue(':customers_dob', ((ACCOUNT_DATE_OF_BIRTH > -1) ? date('Ymd', $dob) : ''));
      $Qcustomer->bindInt(':customers_id', $osC_Customer->id);
      $Qcustomer->execute();

      $Qupdate = $osC_Database->query('update :table_customers_info set customers_info_date_account_last_modified = now() where customers_info_id = :customers_info_id');
      $Qupdate->bindRaw(':table_customers_info', TABLE_CUSTOMERS_INFO);
      $Qupdate->bindInt(':customers_info_id', $osC_Customer->id);
      $Qupdate->execute();

// reset the session variables
      if (ACCOUNT_GENDER > -1) {
        $osC_Customer->setGender($_POST['gender']);
      }
      $osC_Customer->setFirstName(trim($_POST['firstname']));
      $osC_Customer->setLastName(trim($_POST['lastname']));
      $osC_Customer->setFullName();
      $osC_Customer->setEmailAddress(trim($_POST['email_address']));

      $messageStack->add_session('account', SUCCESS_ACCOUNT_UPDATED, 'success');

      tep_redirect(tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
    }
  }

  $Qaccount = $osC_Database->query('select customers_gender, customers_firstname, customers_lastname, unix_timestamp(customers_dob) as customers_dob, customers_email_address from :table_customers where customers_id = :customers_id');
  $Qaccount->bindRaw(':table_customers', TABLE_CUSTOMERS);
  $Qaccount->bindInt(':customers_id', $osC_Customer->id);
  $Qaccount->execute();

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'));
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
<?php require('includes/form_check.js.php'); ?>
<script language="javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="3" cellpadding="3">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><?php echo tep_draw_form('account_edit', tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'), 'post', 'onSubmit="return check_form(account_edit);"') . osc_draw_hidden_field('action', 'process'); ?><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_account.gif', HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  if ($messageStack->size('account_edit') > 0) {
?>
      <tr>
        <td><?php echo $messageStack->output('account_edit'); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  }
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main"><b><?php echo MY_ACCOUNT_TITLE; ?></b></td>
                <td class="inputRequirement" align="right"><?php echo FORM_REQUIRED_INFORMATION; ?></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
              <tr class="infoBoxContents">
                <td><table border="0" cellspacing="2" cellpadding="2">
<?php
  if (ACCOUNT_GENDER > -1) {
    $gender_array = array(array('id' => 'm', 'text' => MALE),
                          array('id' => 'f', 'text' => FEMALE));
?>
                  <tr>
                    <td class="main"><?php echo ENTRY_GENDER; ?></td>
                    <td class="main"><?php echo osc_draw_radio_field('gender', $gender_array, $Qaccount->value('customers_gender'), '', (ACCOUNT_GENDER > 0)); ?></td>
                  </tr>
<?php
  }
?>
                  <tr>
                    <td class="main"><?php echo ENTRY_FIRST_NAME; ?></td>
                    <td class="main"><?php echo osc_draw_input_field('firstname', $Qaccount->value('customers_firstname'), '', true); ?></td>
                  </tr>
                  <tr>
                    <td class="main"><?php echo ENTRY_LAST_NAME; ?></td>
                    <td class="main"><?php echo osc_draw_input_field('lastname', $Qaccount->value('customers_lastname'), '', true); ?></td>
                  </tr>
<?php
  if (ACCOUNT_DATE_OF_BIRTH > -1) {
?>
                  <tr>
                    <td class="main"><?php echo ENTRY_DATE_OF_BIRTH; ?></td>
                    <td class="main"><?php echo tep_draw_date_pull_down_menu('dob', $Qaccount->value('customers_dob'), false, true, true, date('Y')-1901, -5) . '&nbsp;<span class="inputRequirement">*</span>'; ?></td>
                  </tr>
<?php
  }
?>
                  <tr>
                   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                  </tr>
                  <tr>
                    <td class="main"><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
                    <td class="main"><?php echo osc_draw_input_field('email_address', $Qaccount->value('customers_email_address'), '', true); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td>
                <td align="right"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table></form></td>
<!-- body_text_eof //-->
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- right_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
<!-- right_navigation_eof //-->
    </table></td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>

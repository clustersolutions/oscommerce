<?php
/*
  $Id: create_account.php,v 1.79 2004/05/24 10:53:22 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

// needs to be included earlier to set the success message in the messageStack
  require(DIR_WS_LANGUAGES . $osC_Session->value('language') . '/' . FILENAME_CREATE_ACCOUNT);

  if (isset($_POST['action']) && ($_POST['action'] == 'process')) {
    if (DISPLAY_PRIVACY_CONDITIONS == 'true') {
      if (!isset($_POST['privacy_conditions']) || ($_POST['privacy_conditions'] != '1')) {
        $messageStack->add('create_account', ERROR_PRIVACY_STATEMENT_NOT_ACCEPTED);
      }
    }

    if (ACCOUNT_GENDER > 0) {
      if (!isset($_POST['gender']) || (($_POST['gender'] != 'm') && ($_POST['gender'] != 'f'))) {
        $messageStack->add('create_account', ENTRY_GENDER_ERROR);
      }
    }

    if (!isset($_POST['firstname']) || (strlen(trim($_POST['firstname'])) < ACCOUNT_FIRST_NAME)) {
      $messageStack->add('create_account', ENTRY_FIRST_NAME_ERROR);
    }

    if (!isset($_POST['lastname']) || (strlen(trim($_POST['lastname'])) < ACCOUNT_LAST_NAME)) {
      $messageStack->add('create_account', ENTRY_LAST_NAME_ERROR);
    }

    if (ACCOUNT_DATE_OF_BIRTH > -1) {
      if (isset($_POST['dob_days']) && isset($_POST['dob_months']) && isset($_POST['dob_years']) && checkdate($_POST['dob_months'], $_POST['dob_days'], $_POST['dob_years'])) {
        $dob = mktime(0, 0, 0, $_POST['dob_months'], $_POST['dob_days'], $_POST['dob_years']);
      } else {
        $messageStack->add('create_account', ENTRY_DATE_OF_BIRTH_ERROR);
      }
    }

    if (!isset($_POST['email_address']) || (strlen(trim($_POST['email_address'])) < ACCOUNT_EMAIL_ADDRESS)) {
      $messageStack->add('create_account', ENTRY_EMAIL_ADDRESS_ERROR);
    } elseif (tep_validate_email($_POST['email_address']) == false) {
      $messageStack->add('create_account', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
    } else {
      $Qcheck = $osC_Database->query('select customers_id from :table_customers where customers_email_address = :customers_email_address limit 1');
      $Qcheck->bindRaw(':table_customers', TABLE_CUSTOMERS);
      $Qcheck->bindValue(':customers_email_address', $_POST['email_address']);
      $Qcheck->execute();

      if ($Qcheck->numberOfRows() > 0) {
        $messageStack->add('create_account', ENTRY_EMAIL_ADDRESS_ERROR_EXISTS);
      }

      $Qcheck->freeResult();
    }

    if (!isset($_POST['password']) || (strlen(trim($_POST['password'])) < ACCOUNT_PASSWORD)) {
      $messageStack->add('create_account', ENTRY_PASSWORD_ERROR);
    } elseif (!isset($_POST['confirmation']) || (trim($_POST['password']) != trim($_POST['confirmation']))) {
      $messageStack->add('create_account', ENTRY_PASSWORD_ERROR_NOT_MATCHING);
    }

    if ($messageStack->size('create_account') === 0) {
      $osC_Database->startTransaction();

      $Qcustomer = $osC_Database->query('insert into :table_customers (customers_firstname, customers_lastname, customers_email_address, customers_newsletter, customers_status, customers_ip_address, customers_password, customers_gender, customers_dob) values (:customers_firstname, :customers_lastname, :customers_email_address, :customers_newsletter, :customers_status, :customers_ip_address, :customers_password, :customers_gender, :customers_dob)');
      $Qcustomer->bindRaw(':table_customers', TABLE_CUSTOMERS);
      $Qcustomer->bindValue(':customers_firstname', trim($_POST['firstname']));
      $Qcustomer->bindValue(':customers_lastname', trim($_POST['lastname']));
      $Qcustomer->bindValue(':customers_email_address', trim($_POST['email_address']));
      $Qcustomer->bindValue(':customers_newsletter', (isset($_POST['newsletter']) && ($_POST['newsletter'] == '1') ? '1' : ''));
      $Qcustomer->bindValue(':customers_status', '1');
      $Qcustomer->bindValue(':customers_ip_address', tep_get_ip_address());
      $Qcustomer->bindValue(':customers_password', tep_encrypt_password(trim($_POST['password'])));
      $Qcustomer->bindValue(':customers_gender', (((ACCOUNT_GENDER > -1) && isset($_POST['gender']) && (($_POST['gender'] == 'm') || ($_POST['gender'] == 'f'))) ? $_POST['gender'] : ''));
      $Qcustomer->bindValue(':customers_dob', ((ACCOUNT_DATE_OF_BIRTH > -1) ? date('Ymd', $dob) : ''));
      $Qcustomer->execute();

      if ($Qcustomer->affectedRows() === 1) {
        $customer_id = $osC_Database->nextID();

        $Qci = $osC_Database->query('insert into :table_customers_info (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) values (:customers_info_id, :customers_info_number_of_logons, :customers_info_date_account_created)');
        $Qci->bindRaw(':table_customers_info', TABLE_CUSTOMERS_INFO);
        $Qci->bindInt(':customers_info_id', $customer_id);
        $Qci->bindInt(':customers_info_number_of_logons', 0);
        $Qci->bindRaw(':customers_info_date_account_created', 'now()');
        $Qci->execute();

        if ($Qci->affectedRows() === 1) {
          $osC_Database->commitTransaction();

          if (SERVICE_SESSION_REGENERATE_ID == 'True') {
            $osC_Session->recreate();
          }

          $osC_Customer->setCustomerData($customer_id);

// restore cart contents
          $cart->restore_contents();

          $navigation->remove_current_page();

// build the message content
          if ((ACCOUNT_GENDER > -1) && isset($_POST['gender'])) {
             if ($_POST['gender'] == 'm') {
               $email_text = sprintf(EMAIL_GREET_MR, $osC_Customer->last_name);
             } else {
               $email_text = sprintf(EMAIL_GREET_MS, $osC_Customer->last_name);
             }
          } else {
            $email_text = sprintf(EMAIL_GREET_NONE, $osC_Customer->full_name);
          }

          $email_text .= EMAIL_WELCOME . EMAIL_TEXT . EMAIL_CONTACT . EMAIL_WARNING;
          tep_mail($osC_Customer->full_name, $osC_Customer->email_address, EMAIL_SUBJECT, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

          tep_redirect(tep_href_link(FILENAME_CREATE_ACCOUNT_SUCCESS, '', 'SSL'));
        } else {
          $osC_Database->rollbackTransaction();
        }
      } else {
        $osC_Database->rollbackTransaction();
      }
    }
  }

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL'));
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
    <td width="100%" valign="top"><?php echo tep_draw_form('create_account', tep_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL'), 'post', 'onSubmit="return check_form(create_account);"') . osc_draw_hidden_field('action', 'process'); ?><table border="0" width="100%" cellspacing="0" cellpadding="0">
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
      <tr>
        <td class="smallText"><br><?php echo sprintf(TEXT_ORIGIN_LOGIN, tep_href_link(FILENAME_LOGIN, tep_get_all_get_params(), 'SSL')); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  if ($messageStack->size('create_account') > 0) {
?>
      <tr>
        <td><?php echo $messageStack->output('create_account'); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  }

  if (DISPLAY_PRIVACY_CONDITIONS == 'true') {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><b><?php echo TABLE_HEADING_PRIVACY_CONDITIONS; ?></b></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main"><?php echo TEXT_PRIVACY_CONDITIONS_DESCRIPTION . '<br><br>' . osc_draw_checkbox_field('privacy_conditions', '1', false, 'id="privacy"') . '<label for="privacy">&nbsp;' . TEXT_PRIVACY_CONDITIONS_CONFIRM . '</label>'; ?></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
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
            <td class="main"><b><?php echo CATEGORY_PERSONAL; ?></b></td>
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
                <td class="main"><?php echo osc_draw_radio_field('gender', $gender_array, '', '', (ACCOUNT_GENDER > 0)); ?></td>
              </tr>
<?php
  }
?>
              <tr>
                <td class="main"><?php echo ENTRY_FIRST_NAME; ?></td>
                <td class="main"><?php echo osc_draw_input_field('firstname', '', '', true); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo ENTRY_LAST_NAME; ?></td>
                <td class="main"><?php echo osc_draw_input_field('lastname', '', '', true); ?></td>
              </tr>
<?php
  if (ACCOUNT_DATE_OF_BIRTH > -1) {
?>
              <tr>
                <td class="main"><?php echo ENTRY_DATE_OF_BIRTH; ?></td>
                <td class="main"><?php echo tep_draw_date_pull_down_menu('dob', '', false, true, true, date('Y')-1901, -5) . '&nbsp;<span class="inputRequirement">*</span>'; ?></td>
              </tr>
<?php
  }
?>
              <tr>
               <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
                <td class="main"><?php echo osc_draw_input_field('email_address', '', '', true); ?></td>
              </tr>
<?php
  if (ACCOUNT_NEWSLETTER > -1) {
?>
              <tr>
                <td class="main"><?php echo ENTRY_NEWSLETTER; ?></td>
                <td class="main"><?php echo osc_draw_checkbox_field('newsletter', '1'); ?></td>
              </tr>
<?php
  }
?>
              <tr>
               <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo ENTRY_PASSWORD; ?></td>
                <td class="main"><?php echo osc_draw_password_field('password', '', '', true); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo ENTRY_PASSWORD_CONFIRMATION; ?></td>
                <td class="main"><?php echo osc_draw_password_field('confirmation', '', '', true); ?></td>
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
<?php include(DIR_WS_INCLUDES . 'column_right.php'); ?>
<!-- right_navigation_eof //-->
    </table></td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php include(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>

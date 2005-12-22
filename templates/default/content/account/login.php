<?php
/*
  $Id:login.php 187 2005-09-14 14:22:13 +0200 (Mi, 14 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_login.gif', $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<?php
  if ($messageStack->size('login') > 0) {
    echo $messageStack->output('login');
  }

  if ($_SESSION['cart']->count_contents() > 0) {
    echo '<p>' . sprintf(TEXT_LOGIN_VISITORS_CART, 'popupWindow(\'' . tep_href_link(FILENAME_INFO_SHOPPING_CART, '', 'AUTO', false) . '\', \'info_shopping_cart\', \'height=460,width=430,toolbar=no,statusbar=no,scrollbars=yes\')') . '</p>';
  }
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td width="50%" valign="top">
      <div class="moduleBox">
        <div class="outsideHeading"><?php echo HEADING_LOGIN_NEW_CUSTOMER; ?></div>

        <div class="content">
          <p><?php echo TEXT_LOGIN_NEW_CUSTOMER; ?></p>

          <p><?php echo TEXT_LOGIN_NEW_CUSTOMER_INTRODUCTION; ?></p>

          <p align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'create', 'SSL') . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></p>
        </div>
      </div>
    </td>
    <td width="50%" valign="top">
      <form name="login" action="<?php echo tep_href_link(FILENAME_ACCOUNT, 'login=process', 'SSL'); ?>" method="post">

      <div class="moduleBox">
        <div class="outsideHeading"><?php echo HEADING_LOGIN_RETURNING_CUSTOMER; ?></div>

        <div class="content">
          <p><?php echo TEXT_LOGIN_RETURNING_CUSTOMER; ?></p>

          <table border="0" cellspacing="2" cellpadding="2">
            <tr>
              <td><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
              <td><?php echo osc_draw_input_field('email_address'); ?></td>
            </tr>
            <tr>
              <td><?php echo ENTRY_PASSWORD; ?></td>
              <td><?php echo osc_draw_password_field('password'); ?></td>
            </tr>
          </table>

          <p><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'password_forgotten', 'SSL') . '">' . TEXT_LOGIN_PASSWORD_FORGOTTEN . '</a>'; ?></p>

          <p align="right"><?php echo tep_image_submit('button_login.gif', IMAGE_BUTTON_LOGIN); ?></p>
        </div>
      </div>

      </form>
    </td>
  </tr>
</table>

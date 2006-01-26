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
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td width="50%" valign="top">
      <div class="moduleBox">
        <div class="outsideHeading"><?php echo $osC_Language->get('login_new_customer_heading'); ?></div>

        <div class="content">
          <p><?php echo $osC_Language->get('login_new_customer_text'); ?></p>

          <p align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'create', 'SSL') . '">' . tep_image_button('button_continue.gif', $osC_Language->get('button_continue')) . '</a>'; ?></p>
        </div>
      </div>
    </td>
    <td width="50%" valign="top">
      <form name="login" action="<?php echo tep_href_link(FILENAME_ACCOUNT, 'login=process', 'SSL'); ?>" method="post">

      <div class="moduleBox">
        <div class="outsideHeading"><?php echo $osC_Language->get('login_returning_customer_heading'); ?></div>

        <div class="content">
          <p><?php echo $osC_Language->get('login_returning_customer_text'); ?></p>

          <table border="0" cellspacing="2" cellpadding="2">
            <tr>
              <td><?php echo $osC_Language->get('field_customer_email_address'); ?></td>
              <td><?php echo osc_draw_input_field('email_address'); ?></td>
            </tr>
            <tr>
              <td><?php echo $osC_Language->get('field_customer_password'); ?></td>
              <td><?php echo osc_draw_password_field('password'); ?></td>
            </tr>
          </table>

          <p><?php echo sprintf($osC_Language->get('login_returning_customer_password_forgotten'), tep_href_link(FILENAME_ACCOUNT, 'password_forgotten', 'SSL')); ?></p>

          <p align="right"><?php echo tep_image_submit('button_login.gif', $osC_Language->get('button_sign_in')); ?></p>
        </div>
      </div>

      </form>
    </td>
  </tr>
</table>

<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  class osC_Newsletter_newsletter {

/* Private methods */

    var $_title,
        $_has_audience_selection = false,
        $_newsletter_title,
        $_newsletter_content,
        $_newsletter_id,
        $_audience_size = 0;

/* Class constructor */

    function osC_Newsletter_newsletter($title = '', $content = '', $newsletter_id = '') {
      $this->_title = MODULE_NEWSLETTER_NEWSLETTER_TITLE;

      $this->_newsletter_title = $title;
      $this->_newsletter_content = $content;
      $this->_newsletter_id = $newsletter_id;
    }

/* Public methods */

    function getTitle() {
      return $this->_title;
    }

    function hasAudienceSelection() {
      if ($this->_has_audience_selection === true) {
        return true;
      }

      return false;
    }

    function showAudienceSelectionForm() {
      return false;
    }

    function showConfirmation() {
      global $osC_Database;

      $Qrecipients = $osC_Database->query('select count(*) as total from :table_customers c left join :table_newsletters_log nl on (c.customers_email_address = nl.email_address and nl.newsletters_id = :newsletters_id) where c.customers_newsletter = 1 and nl.email_address is null');
      $Qrecipients->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qrecipients->bindTable(':table_newsletters_log', TABLE_NEWSLETTERS_LOG);
      $Qrecipients->bindInt(':newsletters_id', $this->_newsletter_id);
      $Qrecipients->execute();

      $this->_audience_size = $Qrecipients->valueInt('total');

      $confirmation_string = '<p><font color="#ff0000"><b>' . sprintf(MODULE_NEWSLETTER_NEWSLETTER_TEXT_TOTAL_RECIPIENTS, $this->_audience_size) . '</b></font></p>' .
                             '<p><b>' . $this->_newsletter_title . '</b></p>' .
                             '<p>' . nl2br(osc_output_string_protected($this->_newsletter_content)) . '</p>' .
                             '<p align="right">';

      if ($this->_audience_size > 0) {
        $confirmation_string .= '<input type="button" value="' . BUTTON_SEND . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, 'newsletters&page=' . $_GET['page'] . '&nmID=' . $_GET['nmID'] . '&action=nmSendConfirm') . '\';" class="operationButton">&nbsp;';
      }

      $confirmation_string .= '<input type="button" value="' . BUTTON_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, 'newsletters&page=' . $_GET['page'] . '&nmID=' . $_GET['nmID']) . '\'" class="operationButton"></p>';

      return $confirmation_string;
    }

    function sendEmail() {
      global $osC_Database;

      $max_execution_time = 0.8 * (int)ini_get('max_execution_time');
      $time_start = explode(' ', PAGE_PARSE_START_TIME);

      $Qrecipients = $osC_Database->query('select c.customers_firstname, c.customers_lastname, c.customers_email_address from :table_customers c left join :table_newsletters_log nl on (c.customers_email_address = nl.email_address and nl.newsletters_id = :newsletters_id) where c.customers_newsletter = 1 and nl.email_address is null');
      $Qrecipients->bindTable(':table_customers', TABLE_CUSTOMERS);
      $Qrecipients->bindTable(':table_newsletters_log', TABLE_NEWSLETTERS_LOG);
      $Qrecipients->bindInt(':newsletters_id', $this->_newsletter_id);
      $Qrecipients->execute();

      if ($Qrecipients->numberOfRows() > 0) {
        $mimemessage = new email(array(base64_decode('WC1NYWlsZXI6IG9zQ29tbWVyY2UgKGh0dHA6Ly93d3cub3Njb21tZXJjZS5jb20p')));
        $mimemessage->add_text($this->_newsletter_content);
        $mimemessage->build_message();

        while ($Qrecipients->next()) {
          $mimemessage->send($Qrecipients->value('customers_firstname') . ' ' . $Qrecipients->value('customers_lastname'), $Qrecipients->value('customers_email_address'), '', EMAIL_FROM, $this->_newsletter_title);

          $Qlog = $osC_Database->query('insert into :table_newsletters_log (newsletters_id, email_address, date_sent) values (:newsletters_id, :email_address, now())');
          $Qlog->bindTable(':table_newsletters_log', TABLE_NEWSLETTERS_LOG);
          $Qlog->bindInt(':newsletters_id', $this->_newsletter_id);
          $Qlog->bindValue(':email_address', $Qrecipients->value('customers_email_address'));
          $Qlog->execute();

          $time_end = explode(' ', microtime());
          $timer_total = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);

          if ($timer_total > $max_execution_time) {
            echo '<p><font color="#38BB68"><b>' . TEXT_REFRESHING_PAGE . '</b></font></p>' .
                 '<p>' . osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, 'newsletters&page=' . $_GET['page'] . '&nmID=' . $this->_newsletter_id . '&action=nmSendConfirm'), TEXT_CONTINUE_MANUALLY) . '</p>' .
                 '<META HTTP-EQUIV="refresh" content="2; URL=' . osc_href_link_admin(FILENAME_DEFAULTS, 'newsletters&page=' . $_GET['page'] . '&nmID=' . $this->_newsletter_id . '&action=nmSendConfirm') . '">';
            exit;
          }
        }

        $Qrecipients->freeResult();
      }

      $Qupdate = $osC_Database->query('update :table_newsletters set date_sent = now(), status = 1 where newsletters_id = :newsletters_id');
      $Qupdate->bindTable(':table_newsletters', TABLE_NEWSLETTERS);
      $Qupdate->bindInt(':newsletters_id', $this->_newsletter_id);
      $Qupdate->execute();
    }
  }
?>

<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

/**
 * The osC_Banner class manages the banners shown throughout the online store
 */

  class osC_Banner {

/**
 * Controls whether banners should be shown multiple times on the page
 *
 * @var boolean
 * @access private
 */

    private $_show_duplicates_in_group = false;

/**
 * A placeholder that keeps the banner ID in memory when checking if a banner exists before showing it
 *
 * @var id
 * @access private
 */

    private $_exists_id;

/**
 * An array containing the banner IDs already shown on the page
 *
 * @var array
 * @access private
 */

    private $_shown_ids = array();

/**
 * Constructor
 *
 * @access public
 */

    public function __construct() {
      if ( SERVICE_BANNER_SHOW_DUPLICATE == 'True' ) {
        $this->_show_duplicates_in_group = true;
      }
    }

/**
 * Activate a banner that has been on schedule
 *
 * @param int $id The ID of the banner to activate
 * @access public
 * @return boolean
 */

    public function activate($id) {
      return $this->_setStatus($id, true);
    }

/**
 * Activate all banners on schedule
 *
 * @access public
 */

    public function activateAll() {
      global $osC_Database;

      $Qbanner = $osC_Database->query('select banners_id, date_scheduled from :table_banners where date_scheduled != ""');
      $Qbanner->bindTable(':table_banners', TABLE_BANNERS);
      $Qbanner->execute();

      while ( $Qbanner->next() ) {
        if ( osC_DateTime::getNow() >= $Qbanner->value('date_scheduled') ) {
          $this->activate($Qbanner->valueInt('banners_id'));
        }
      }
    }

/**
 * Deactivate a banner
 *
 * @param int $id The ID of the banner to deactivate
 * @access public
 * @return boolean
 */

    public function expire($id) {
      return $this->_setStatus($id, false);
    }

/**
 * Deactivate all banners that have passed their schedule
 *
 * @access public
 */

    public function expireAll() {
      global $osC_Database;

      $Qbanner = $osC_Database->query('select b.banners_id, b.expires_date, b.expires_impressions, sum(bh.banners_shown) as banners_shown from :table_banners b, :table_banners_history bh where b.status = 1 and b.banners_id = bh.banners_id group by b.banners_id');
      $Qbanner->bindTable(':table_banners', TABLE_BANNERS);
      $Qbanner->bindTable(':table_banners_history', TABLE_BANNERS_HISTORY);
      $Qbanner->execute();

      while ( $Qbanner->next() ) {
        if ( !osc_empty($Qbanner->value('expires_date')) ) {
          if ( osC_DateTime::getNow() >= $Qbanner->value('expires_date') ) {
            $this->expire($Qbanner->valueInt('banners_id'));
          }
        } elseif ( !osc_empty($Qbanner->valueInt('expires_impressions')) ) {
          if ( ($Qbanner->valueInt('expires_impressions') > 0) && ($Qbanner->valueInt('banners_shown') >= $Qbanner->valueInt('expires_impressions')) ) {
            $this->expire($Qbanner->valueInt('banners_id'));
          }
        }
      }
    }

/**
 * Check if an existing banner is active
 *
 * @param int $id The ID of the banner to check
 * @access public
 * @return boolean
 */

    public function isActive($id) {
      global $osC_Database;

      $Qbanner = $osC_Database->query('select status from :table_banners where banners_id = :banners_id');
      $Qbanner->bindTable(':table_banners', TABLE_BANNERS);
      $Qbanner->bindInt(':banners_id', $id);
      $Qbanner->execute();

      return ( $Qbanner->valueInt('status') === 1 );
    }

/**
 * Check if banners exist in a group. If banners exists, select a random entry and assign its ID to $_exists_id.
 *
 * @param string $group The group to check in
 * @access public
 * @return boolean
 */

    public function exists($group) {
      global $osC_Database;

      $Qbanner = $osC_Database->query('select banners_id from :table_banners where status = 1 and banners_group = :banners_group');

      if ( ($this->_show_duplicates_in_group === false) && (sizeof($this->_shown_ids) > 0) ) {
        $Qbanner->appendQuery('and banners_id not in (:banner_ids)');
        $Qbanner->bindRaw(':banner_ids', implode(',', $this->_shown_ids));
      }

      $Qbanner->bindTable(':table_banners', TABLE_BANNERS);
      $Qbanner->bindValue(':banners_group', $group);
      $Qbanner->executeRandom();

      if ( $Qbanner->numberOfRows() > 0 ) {
        $this->_exists_id = $Qbanner->valueInt('banners_id');

        return true;
      }

      return false;
    }

/**
 * Display a banner. If no ID is passed, the value defined in $_exists_id is used.
 *
 * @param int $id The ID of the banner to show
 * @access public
 * @return string
 */

    public function display($id = null) {
      global $osC_Database;

      $banner_string = '';

      if ( empty($id) && isset($this->_exists_id) && is_numeric($this->_exists_id) ) {
        $id = $this->_exists_id;

        unset($this->_exists_id);
      }

      $Qbanner = $osC_Database->query('select * from :table_banners where banners_id = :banners_id and status = 1');
      $Qbanner->bindTable(':table_banners', TABLE_BANNERS);
      $Qbanner->bindInt(':banners_id', $id);
      $Qbanner->execute();

      if ( $Qbanner->numberOfRows() > 0 ) {
        if ( !osc_empty($Qbanner->value('banners_html_text')) ) {
          $banner_string = $Qbanner->value('banners_html_text');
        } else {
          $banner_string = osc_link_object(osc_href_link(FILENAME_REDIRECT, 'action=banner&goto=' . $Qbanner->valueInt('banners_id')), osc_image(DIR_WS_IMAGES . $Qbanner->value('banners_image'), $Qbanner->value('banners_title')), 'target="_blank"');
        }

        $this->_updateDisplayCount($Qbanner->valueInt('banners_id'));

        if ( $this->_show_duplicates_in_group === false ) {
          $this->_shown_ids[] = $Qbanner->valueInt('banners_id');
        }
      }

      return $banner_string;
    }

/**
 * Return the URL assigned to the banner
 *
 * @param int $id The ID of the banner
 * @param boolean $increment_click_flag A flag to state if the banner click count should be incremented
 * @access public
 * @return string
 */

    public function getURL($id, $increment_click_flag = false) {
      global $osC_Database;

      $url = '';

      $Qbanner = $osC_Database->query('select banners_url from :table_banners where banners_id = :banners_id and status = 1');
      $Qbanner->bindTable(':table_banners', TABLE_BANNERS);
      $Qbanner->bindInt(':banners_id', $id);
      $Qbanner->execute();

      if ( $Qbanner->numberOfRows() > 0 ) {
        $url = $Qbanner->value('banners_url');

        if ( $increment_click_flag === true ) {
          $this->_updateClickCount($id);
        }
      }

      return $url;
    }

/**
 * Sets the status of a banner
 *
 * @param int $id The ID of the banner to set the status to
 * @param boolean $active_flag A flag that enables or disables the banner
 * @access private
 * @return boolean
 */

    private function _setStatus($id, $active_flag) {
      global $osC_Database;

      if ( $active_flag === true ) {
        $Qbanner = $osC_Database->query('update :table_banners set status = 1, date_status_change = now(), date_scheduled = NULL where banners_id = :banners_id');
      } else {
        $Qbanner = $osC_Database->query('update :table_banners set status = 0, date_status_change = now() where banners_id = :banners_id');
      }

      $Qbanner->bindTable(':table_banners', TABLE_BANNERS);
      $Qbanner->bindInt(':banners_id', $id);
      $Qbanner->execute();

      return ( $Qbanner->affectedRows() === 1 );
    }

/**
 * Increment the display count of the banner
 *
 * @param int $id The ID of the banner
 * @access private
 */

    private function _updateDisplayCount($id) {
      global $osC_Database;

      $Qcheck = $osC_Database->query('select count(*) as count from :table_banners_history where banners_id = :banners_id and date_format(banners_history_date, "%Y%m%d") = date_format(now(), "%Y%m%d")');
      $Qcheck->bindTable(':table_banners_history', TABLE_BANNERS_HISTORY);
      $Qcheck->bindInt(':banners_id', $id);
      $Qcheck->execute();

      if ( $Qcheck->valueInt('count') > 0 ) {
        $Qbanner = $osC_Database->query('update :table_banners_history set banners_shown = banners_shown + 1 where banners_id = :banners_id and date_format(banners_history_date, "%Y%m%d") = date_format(now(), "%Y%m%d")');
      } else {
        $Qbanner = $osC_Database->query('insert into :table_banners_history (banners_id, banners_shown, banners_history_date) values (:banners_id, 1, now())');
      }

      $Qbanner->bindTable(':table_banners_history', TABLE_BANNERS_HISTORY);
      $Qbanner->bindInt(':banners_id', $id);
      $Qbanner->execute();
    }

/**
 * Increment the click count of the banner
 *
 * @param int $id The ID of the banner
 * @access private
 */

    private function _updateClickCount($id) {
      global $osC_Database;

      $Qcheck = $osC_Database->query('select count(*) as count from :table_banners_history where banners_id = :banners_id and date_format(banners_history_date, "%Y%m%d") = date_format(now(), "%Y%m%d")');
      $Qcheck->bindTable(':table_banners_history', TABLE_BANNERS_HISTORY);
      $Qcheck->bindInt(':banners_id', $id);
      $Qcheck->execute();

      if ( $Qcheck->valueInt('count') > 0 ) {
        $Qbanner = $osC_Database->query('update :table_banners_history set banners_clicked = banners_clicked + 1 where banners_id = :banners_id and date_format(banners_history_date, "%Y%m%d") = date_format(now(), "%Y%m%d")');
      } else {
        $Qbanner = $osC_Database->query('insert into :table_banners_history (banners_id, banners_clicked, banners_history_date) values (:banners_id, 1, now())');
      }

      $Qbanner->bindTable(':table_banners_history', TABLE_BANNERS_HISTORY);
      $Qbanner->bindInt(':banners_id', $id);
      $Qbanner->execute();
    }
  }
?>

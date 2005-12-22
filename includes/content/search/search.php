<?php
/*
  $Id: password.php 64 2005-03-12 16:36:16Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/search.php');

  class osC_Search_Search extends osC_Template {

/* Private variables */

    var $_module = 'search',
        $_group = 'search',
        $_page_title = HEADING_TITLE_SEARCH,
        $_page_image = 'table_background_browse.gif',
        $_page_contents = 'search.php';

/* Class constructor */

    function osC_Search_Search() {
      global $osC_Services, $breadcrumb, $osC_Search;

      $osC_Search = new osC_Search();

      if (isset($_GET['keywords'])) {
        $this->_page_title = HEADING_TITLE_SEARCH_RESULTS;
        $this->_page_contents = 'results.php';

        if ($osC_Services->isStarted('breadcrumb')) {
          $breadcrumb->add(BREADCRUMB_SEARCH_RESULTS, tep_href_link(FILENAME_SEARCH, tep_get_all_get_params()));
        }

        $this->_process();
      } else {
        $this->addJavascriptPhpFilename('templates/' . $this->getCode() . '/javascript/search/search.php');
      }
    }

/* Private methods */

    function _process() {
      global $messageStack, $osC_Search, $Qlisting;

      if (isset($_GET['datefrom_days']) && is_numeric($_GET['datefrom_days']) && isset($_GET['datefrom_months']) && is_numeric($_GET['datefrom_months']) && isset($_GET['datefrom_years']) && is_numeric($_GET['datefrom_years'])) {
        if (@checkdate($_GET['datefrom_months'], $_GET['datefrom_days'], $_GET['datefrom_years'])) {
          $osC_Search->setDateFrom(mktime(0, 0, 0, $_GET['datefrom_months'], $_GET['datefrom_days'], $_GET['datefrom_years']));
        } else {
          $messageStack->add('search', ERROR_INVALID_FROM_DATE);
        }
      }

      if (isset($_GET['dateto_days']) && is_numeric($_GET['dateto_days']) && isset($_GET['dateto_months']) && is_numeric($_GET['dateto_months']) && isset($_GET['dateto_years']) && is_numeric($_GET['dateto_years'])) {
        if (@checkdate($_GET['dateto_months'], $_GET['dateto_days'], $_GET['dateto_years'])) {
          $osC_Search->setDateTo(mktime(0, 0, 0, $_GET['dateto_months'], $_GET['dateto_days'], $_GET['dateto_years']));
        } else {
          $messageStack->add('search', ERROR_INVALID_TO_DATE);
        }
      }

      if ($osC_Search->hasDateSet()) {
        if ($osC_Search->getDateFrom() > $osC_Search->getDateTo()) {
          $messageStack->add('search', ERROR_TO_DATE_LESS_THAN_FROM_DATE);
        }
      }

      if (isset($_GET['pfrom']) && !empty($_GET['pfrom'])) {
        if (settype($_GET['pfrom'], 'double')) {
          $osC_Search->setPriceFrom($_GET['pfrom']);
        } else {
          $messageStack->add('search', ERROR_PRICE_FROM_MUST_BE_NUM);
        }
      }

      if (isset($_GET['pto']) && !empty($_GET['pto'])) {
        if (settype($_GET['pto'], 'double')) {
          $osC_Search->setPriceTo($_GET['pto']);
        } else {
          $messageStack->add('search', ERROR_PRICE_TO_MUST_BE_NUM);
        }
      }

      if ($osC_Search->hasPriceSet('from') && $osC_Search->hasPriceSet('to') && ($osC_Search->getPriceFrom() >= $osC_Search->getPriceTo())) {
        $messageStack->add('search', ERROR_PRICE_TO_LESS_THAN_PRICE_FROM);
      }

      if (isset($_GET['keywords']) && is_string($_GET['keywords']) && !empty($_GET['keywords'])) {
        $osC_Search->setKeywords(urldecode($_GET['keywords']));

        if ($osC_Search->hasKeywords() === false) {
          $messageStack->add('search', ERROR_INVALID_KEYWORDS);
        }
      }

      if (!$osC_Search->hasKeywords() && !$osC_Search->hasPriceSet('from') && !$osC_Search->hasPriceSet('to') && !$osC_Search->hasDateSet('from') && !$osC_Search->hasDateSet('to')) {
        $messageStack->add('search', ERROR_AT_LEAST_ONE_INPUT);
      }

      if (isset($_GET['category']) && is_numeric($_GET['category']) && ($_GET['category'] > 0)) {
        $osC_Search->setCategory($_GET['category'], (isset($_GET['recursive']) && ($_GET['recursive'] == '1') ? true : false));
      }

      if (isset($_GET['manufacturer']) && is_numeric($_GET['manufacturer']) && ($_GET['manufacturer'] > 0)) {
        $osC_Search->setManufacturer($_GET['manufacturer']);
      }

      if (isset($_GET['sort']) && !empty($_GET['sort'])) {
        if (strpos($_GET['sort'], '|d') !== false) {
          $osC_Search->setSortBy(substr($_GET['sort'], 0, -2), '-');
        } else {
          $osC_Search->setSortBy($_GET['sort']);
        }
      }

      if ($messageStack->size('search') > 0) {
        $this->_page_contents = 'search.php';
        $this->addJavascriptPhpFilename('templates/' . $this->getCode() . '/javascript/search/search.php');
      } else {
        $Qlisting = $osC_Search->execute();
      }
    }
  }
?>

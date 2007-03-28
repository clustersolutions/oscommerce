<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access_Credit_cards extends osC_Access {
    var $_module = 'credit_cards',
        $_group = 'configuration',
        $_icon = 'wallet.png',
        $_title,
        $_sort_order = 300;

    function osC_Access_Credit_cards() {
      global $osC_Language;

      $this->_title = $osC_Language->get('access_credit_cards_title');
    }
  }
?>

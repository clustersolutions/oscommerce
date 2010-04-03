<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Access_CreditCards extends osC_Access {
    var $_module = 'CreditCards',
        $_group = 'configuration',
        $_icon = 'wallet.png',
        $_title,
        $_sort_order = 300;

    public function __construct() {
      $this->_title = __('access_credit_cards_title');
    }
  }
?>

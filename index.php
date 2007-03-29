<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  $_SERVER['SCRIPT_FILENAME'] = __FILE__;

  require('includes/application_top.php');

  $osC_Language->load('index');

  $osC_Template = osC_Template::setup('index');

  require('templates/' . $osC_Template->getCode() . '.php');

  require('includes/application_bottom.php');
?>

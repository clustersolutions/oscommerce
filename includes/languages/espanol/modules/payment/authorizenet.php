<?php
/*
  $Id: authorizenet.php,v 1.14 2003/11/22 13:18:55 w2vy Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_TITLE', 'Authorize.net');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_DESCRIPTION', 'Tarjeta de Cr&eacute;dito para Pruebas:<br><br>Numero: 4111111111111111<br>Caducidad: Cualquiera');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_TYPE', 'Tipo de Tarjeta:');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_OWNER', 'Titular de la Tarjeta:');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_NUMBER', 'N&uacute;mero de la Tarjeta:');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_CREDIT_CARD_EXPIRES', 'Fecha de Caducidad:');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_JS_CC_OWNER', '* El nombre del titular de la tarjeta de cr&eacute;dito debe de tener al menos ' . CC_OWNER_MIN_LENGTH . ' caracteres.\n');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_JS_CC_NUMBER', '* El n&uacute;mero de la tarjeta de credito debe de tener al menos ' . CC_NUMBER_MIN_LENGTH . ' numeros.\n');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_ERROR_MESSAGE', 'Ha ocurrido un error procesando su tarjeta de cr&eacute;dito. Por favor, int&eacute;ntelo de nuevo.');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_DECLINED_MESSAGE', 'Su tarjeta ha sido denegada. Pruebe con otra tarjeta o consulte con su banco.');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_ERROR', 'Error en Tarjeta de Cr&eacute;dito!');

  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_ACCT_NAME', 'Name of Account');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_ACCT_TYPE', 'Type of Account');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_ACCT_TYPE_CHECK', 'Checking Account');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_ACCT_TYPE_SAVINGS', 'Savings Account');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_ACCT_ORG', 'Account Type');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_ACCT_ORG_PERSONAL', 'Personal Account');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_ACCT_ORG_BUSINESS', 'Business Account');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_NAME', 'Bank Name');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_ABA_CODE', 'Bank Routing Code');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_BANK_ACCT_NUM', 'Bank Account Number');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_WF_INTRO', 'Enter TAX ID or Driver\'s License');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_WF_TAXID', 'TAX ID');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_WF_DLNUM', 'Drivers License Number');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_WF_STATE', 'State (2 Letter Code)`');
  define('MODULE_PAYMENT_AUTHORIZENET_TEXT_WF_DOB', 'Date of Birth (MM/DD/YYYY)');
?>

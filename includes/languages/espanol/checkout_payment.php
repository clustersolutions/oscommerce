<?php
/*
  $Id: checkout_payment.php,v 1.19 2003/12/04 12:45:46 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE_1', 'Realizar Pedido');
define('NAVBAR_TITLE_2', 'Forma de Pago');

define('HEADING_TITLE', 'Forma de Pago');

define('TABLE_HEADING_BILLING_ADDRESS', 'Direcci&oacute;n de Facturaci&oacute;n');
define('TEXT_SELECTED_BILLING_DESTINATION', 'Elija la direcci&oacute;n de su libreta donde quiera recibir la factura.');
define('TITLE_BILLING_ADDRESS', 'Direcci&oacute;n de Facturaci&oacute;n:');

define('TABLE_HEADING_CONDITIONS', 'Terms and Conditions');
define('TEXT_CONDITIONS_DESCRIPTION', 'Please acknowledge the terms and conditions bound to this order by ticking the following box. The terms and conditions can be read <a href="' . tep_href_link(FILENAME_CONDITIONS, '', 'SSL') . '"><u>here</u></a>.');
define('TEXT_CONDITIONS_CONFIRM', 'I have read and agreed to the terms and conditions bound to this order.');

define('TABLE_HEADING_PAYMENT_METHOD', 'Forma de Pago');
define('TEXT_SELECT_PAYMENT_METHOD', 'Escoja la forma de pago preferida para este pedido.');
define('TITLE_PLEASE_SELECT', 'Seleccione');
define('TEXT_ENTER_PAYMENT_INFORMATION', 'Esta es la unica forma de pago disponible para este pedido.');

define('TABLE_HEADING_COMMENTS', 'Agregue Los Comentarios Sobre Su Orden');

define('TITLE_CONTINUE_CHECKOUT_PROCEDURE', 'Continuar con el Proceso de Compra');
define('TEXT_CONTINUE_CHECKOUT_PROCEDURE', 'para confirmar este pedido.');
?>

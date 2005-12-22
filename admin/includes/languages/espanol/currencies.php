<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'Monedas');

define('TABLE_HEADING_CURRENCY_NAME', 'Moneda');
define('TABLE_HEADING_CURRENCY_CODES', 'C&oacute;digo');
define('TABLE_HEADING_CURRENCY_VALUE', 'Valor');
define('TABLE_HEADING_CURRENCY_EXAMPLE', 'Ejemplo');
define('TABLE_HEADING_ACTION', 'Acci&oacute;n');

define('TEXT_INFO_INSERT_INTRO', 'Introduzca los datos de la nueva moneda');
define('TEXT_INFO_EDIT_INTRO', 'Haga los cambios necesarios');
define('TEXT_INFO_DELETE_INTRO', 'Seguro que quiere eliminar esta moneda?');
define('TEXT_INFO_UPDATE_SERVICE_INTRO', 'Please select the currency update service to use.');

define('TEXT_INFO_HEADING_NEW_CURRENCY', 'Nueva Moneda');

define('TEXT_INFO_CURRENCY_TITLE', 'T&iacute;tulo:');
define('TEXT_INFO_CURRENCY_CODE', 'C&oacute;digo:');
define('TEXT_INFO_CURRENCY_SYMBOL_LEFT', 'S&iacute;mbolo a la izquierda:');
define('TEXT_INFO_CURRENCY_SYMBOL_RIGHT', 'S&iacute;mbolo a la derecha:');
define('TEXT_INFO_CURRENCY_DECIMAL_PLACES', 'Lugares decimales:');
define('TEXT_INFO_CURRENCY_VALUE', 'Valor:');

define('TEXT_INFO_SET_AS_DEFAULT', TEXT_SET_DEFAULT . '<br /><small>(requiere una actualizaci&oacute;n manual de los cambios de moneda)</small>');
define('TEXT_INFO_SERVICE_TERMS', 'By using the selected currency update service you are agreeing to the terms and conditions of the service involved.');

define('TEXT_INFO_CURRENCY_UPDATED', 'El valor de %s (%s) se ha actualizado via %s.');

define('ERROR_REMOVE_DEFAULT_CURRENCY', 'Advertencia: La moneda predeterminada no se puede eliminar. Seleccione otra moneda como predeterminada y vuelva a intentarlo.');
define('ERROR_CURRENCY_INVALID', 'Error: El valor de %s (%s) no ha sido actualizado via %s. Compruebe la validez del c&oacute;digo.');
define('WARNING_PRIMARY_SERVER_FAILED', 'Advertencia: El servidor primario ha fallado (%s) actualizando %s (%s) - probando con el servidor secundario.');

define('TEXT_INFO_DELETE_PROHIBITED', 'Advertencia: La moneda predeterminada no se puede eliminar. Seleccione otra moneda como predeterminada y vuelva a intentarlo.');
?>

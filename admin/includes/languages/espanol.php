<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

// look in your $PATH_LOCALE/locale directory for available locales
// or execute 'locale -a' on the server.
// Examples:
// on Linux try 'es_ES'
// on FreeBSD try 'es_ES.ISO_8859-1'
// on Windows try 'sp', or 'Spanish'
define('LANGUAGE_LOCALE', 'es_ES');

define('DATE_FORMAT_SHORT', '%d/%m/%Y');  // this is used for strftime()
define('DATE_FORMAT_LONG', '%A %d %B, %Y'); // this is used for strftime()
define('DATE_FORMAT', 'd/m/Y');  // this is used for date()
define('PHP_DATE_TIME_FORMAT', 'd/m/Y H:i:s'); // this is used for date()
define('DATE_TIME_FORMAT', DATE_FORMAT_SHORT . ' %H:%M:%S');

////
// Return date in raw format
// $date should be in format mm/dd/yyyy
// raw date is in format YYYYMMDD, or DDMMYYYY
function tep_date_raw($date, $reverse = false) {
  if ($reverse) {
    return substr($date, 0, 2) . substr($date, 3, 2) . substr($date, 6, 4);
  } else {
    return substr($date, 6, 4) . substr($date, 3, 2) . substr($date, 0, 2);
  }
}

define('NUMERIC_DECIMAL_SEPARATOR', '.');
define('NUMERIC_THOUSANDS_SEPARATOR', ',');

// Global entries for the <html> tag
define('HTML_PARAMS','dir="ltr" lang="es"');

// charset for web pages and emails
define('CHARSET', 'iso-8859-1');

// page title
define('TITLE', 'osCommerce');

// header text in includes/header.php
define('HEADER_TITLE_HELP', 'Help');
define('HEADER_TITLE_OSCOMMERCE_SUPPORT_SITE', 'osCommerce Support Site');
define('HEADER_TITLE_ONLINE_CATALOG', 'Cat&aacute;logo');
define('HEADER_TITLE_LANGUAGES', 'Idiomas');

define('BOX_CONNECTION_PROTECTED', 'Esta protegido por una conexi&oacute;n SSL %s.');
define('BOX_CONNECTION_UNPROTECTED', '<font color="#ff0000">No</font> esta protegido por una conexi&oacute;n segura SSL.');
define('BOX_CONNECTION_UNKNOWN', 'desconocido');

// text for gender
define('MALE', 'Var&oacute;n');
define('FEMALE', 'Mujer');

// text for date of birth example
define('DOB_FORMAT_STRING', 'dd/mm/aaaa');

// configuration box text in includes/boxes/configuration.php
define('BOX_HEADING_CONFIGURATION', 'Configuraci&oacute;n');
define('BOX_CONFIGURATION_MYSTORE', 'Mi Tienda');
define('BOX_CONFIGURATION_LOGGING', 'Registro');
define('BOX_CONFIGURATION_CACHE', 'Cach&eacute;');
define('BOX_CONFIGURATION_SERVICES', 'Services');
define('BOX_CONFIGURATION_CREDIT_CARDS', 'Credit Cards');

// modules box text in includes/boxes/modules.php
define('BOX_HEADING_MODULES', 'M&oacute;dulos');
define('BOX_MODULES_PAYMENT', 'Pago');
define('BOX_MODULES_SHIPPING', 'Env&iacute;o');
define('BOX_MODULES_ORDER_TOTAL', 'Totalizaci&oacute;n');

// categories box text in includes/boxes/catalog.php
define('BOX_HEADING_CATALOG', 'Cat&aacute;logo');
define('BOX_CATALOG_CATEGORIES', 'Categorias');
define('BOX_CATALOG_PRODUCTS', 'Productos');
define('BOX_CATALOG_CATEGORIES_PRODUCTS_ATTRIBUTES', 'Atributos');
define('BOX_CATALOG_MANUFACTURERS', 'Fabricantes');
define('BOX_CATALOG_REVIEWS', 'Comentarios');
define('BOX_CATALOG_SPECIALS', 'Ofertas');
define('BOX_CATALOG_PRODUCTS_EXPECTED', 'Pr&oacute;ximamente');

// customers box text in includes/boxes/customers.php
define('BOX_HEADING_CUSTOMERS', 'Clientes');
define('BOX_CUSTOMERS_CUSTOMERS', 'Clientes');
define('BOX_CUSTOMERS_ORDERS', 'Pedidos');

// taxes box text in includes/boxes/taxes.php
define('BOX_HEADING_LOCATION_AND_TAXES', 'Zonas/Impuestos');
define('BOX_TAXES_COUNTRIES', 'Paises');
define('BOX_TAXES_ZONES', 'Provincias');
define('BOX_TAXES_GEO_ZONES', 'Zonas de Impuestos');
define('BOX_TAXES_TAX_CLASSES', 'Tipos de Impuestos');

// reports box text in includes/boxes/reports.php
define('BOX_HEADING_REPORTS', 'Informes');
define('BOX_REPORTS_STATISTICS', 'Statistics');

// tools text in includes/boxes/tools.php
define('BOX_HEADING_TOOLS', 'Herramientas');
define('BOX_TOOLS_BACKUP', 'Copia de Seguridad');
define('BOX_TOOLS_BANNER_MANAGER', 'Banners');
define('BOX_TOOLS_CACHE', 'Control de Cach&eacute;');
define('BOX_TOOLS_DEFINE_LANGUAGE', 'Definir Idiomas');
define('BOX_TOOLS_FILE_MANAGER', 'Archivos');
define('BOX_TOOLS_NEWSLETTER_MANAGER', 'Boletines');
define('BOX_TOOLS_SERVER_INFO', 'Informaci&oacute;n');
define('BOX_TOOLS_WHOS_ONLINE', 'Usuarios conectados');

// localizaion box text in includes/boxes/localization.php
define('BOX_HEADING_LOCALIZATION', 'Localizaci&oacute;n');
define('BOX_LOCALIZATION_CURRENCIES', 'Monedas');
define('BOX_LOCALIZATION_LANGUAGES', 'Idiomas');
define('BOX_LOCALIZATION_ORDERS_STATUS', 'Estado Pedidos');
define('BOX_LOCALIZATION_WEIGHT_CLASSES', 'Weight Classes');

// javascript messages
define('JS_ERROR', 'Ha habido errores procesando su formulario!\nPor favor, haga las siguientes modificaciones:\n\n');
define('JS_OPTIONS_VALUE_PRICE', '* El atributo necesita un precio\n');
define('JS_OPTIONS_VALUE_PRICE_PREFIX', '* El atributo necesita un prefijo para el precio\n');
define('JS_PRODUCTS_NAME', '* El producto necesita un nombre\n');
define('JS_PRODUCTS_DESCRIPTION', '* El producto necesita una descripci&oacute;n\n');
define('JS_PRODUCTS_PRICE', '* El producto necesita un precio\n');
define('JS_PRODUCTS_WEIGHT', '* Debe especificar el peso del producto\n');
define('JS_PRODUCTS_QUANTITY', '* Debe especificar la cantidad\n');
define('JS_PRODUCTS_MODEL', '* Debe especificar el modelo\n');
define('JS_PRODUCTS_IMAGE', '* Debe suministrar una imagen\n');
define('JS_SPECIALS_PRODUCTS_PRICE', '* Debe rellenar el precio\n');
define('JS_ORDER_DOES_NOT_EXIST', 'El n&uacute;mero de pedido %s no existe!');

define('CATEGORY_PERSONAL', 'Personal');
define('CATEGORY_ADDRESS', 'Domicilio');
define('CATEGORY_CONTACT', 'Contacto');
define('CATEGORY_COMPANY', 'Empresa');
define('CATEGORY_OPTIONS', 'Opciones');

define('ENTRY_GENDER', 'Sexo:');
define('ENTRY_GENDER_ERROR', 'Debe elegir un Sexo.');
define('ENTRY_FIRST_NAME', 'Nombre:');
define('ENTRY_FIRST_NAME_ERROR', 'El Nombre debe tener al menos ' . ACCOUNT_FIRST_NAME . ' letras.');
define('ENTRY_LAST_NAME', 'Apellidos:');
define('ENTRY_LAST_NAME_ERROR', 'El Nombre debe tener al menos ' . ENTRY_LAST_NAME . ' letras.');
define('ENTRY_DATE_OF_BIRTH', 'Fecha de Nacimiento:');
define('ENTRY_DATE_OF_BIRTH_ERROR', 'La Fecha de Nacimiento debe tener el formato dia/mes/a&ntilde;o.');
define('ENTRY_EMAIL_ADDRESS', 'E-Mail:');
define('ENTRY_EMAIL_ADDRESS_ERROR', 'El E-Mail debe tener al menos ' . ACCOUNT_EMAIL_ADDRESS . ' letras.');
define('ENTRY_EMAIL_ADDRESS_CHECK_ERROR', 'Su E-Mail no parece correcto!');
define('ENTRY_EMAIL_ADDRESS_ERROR_EXISTS', 'E-Mail ya existe!');
define('ENTRY_COMPANY', 'Nombre empresa:');
define('ENTRY_COMPANY_ERROR', 'El Nombre Empresa debe tener al menos ' . ACCOUNT_COMPANY . ' letras.');
define('ENTRY_STREET_ADDRESS', 'Direcci&oacute;n:');
define('ENTRY_STREET_ADDRESS_ERROR', 'El Direcci&oacute;n debe tener al menos ' . ACCOUNT_STREET_ADDRESS . ' letras.');
define('ENTRY_SUBURB', 'Suburb:');
define('ENTRY_SUBURB_ERROR', 'El Suburb debe tener al menos ' . ACCOUNT_SUBURB . ' letras.');
define('ENTRY_POST_CODE', 'C&oacute;digo Postal:');
define('ENTRY_POST_CODE_ERROR', 'El C&oacute;digo Postal debe tener al menos ' . ACCOUNT_POST_CODE . ' letras.');
define('ENTRY_CITY', 'Poblaci&oacute;n:');
define('ENTRY_CITY_ERROR', 'El Poblaci&oacute;n debe tener al menos ' . ACCOUNT_CITY . ' letras.');
define('ENTRY_STATE', 'Provincia:');
define('ENTRY_STATE_ERROR', 'El Provincia debe tener al menos ' . ACCOUNT_STATE . ' letras.');
define('ENTRY_STATE_ERROR_SELECT', 'Please select a state from the States pull down menu.');
define('ENTRY_COUNTRY', 'Pa&iacute;s:');
define('ENTRY_COUNTRY_ERROR', 'You must select a country from the Countries pull down menu.');
define('ENTRY_TELEPHONE_NUMBER', 'Tel&eacute;fono:');
define('ENTRY_TELEPHONE_NUMBER_ERROR', 'El Tel&eacute;fono debe tener al menos ' . ACCOUNT_TELEPHONE . ' letras.');
define('ENTRY_FAX_NUMBER', 'Fax:');
define('ENTRY_FAX_NUMBER_ERROR', 'El Fax debe tener al menos ' . ACCOUNT_FAX . ' letras.');
define('ENTRY_NEWSLETTER', 'Bolet&iacute;n:');
define('ENTRY_NEWSLETTER_YES', 'suscrito');
define('ENTRY_NEWSLETTER_NO', 'no suscrito');
define('ENTRY_PASSWORD', 'Password:');
define('ENTRY_PASSWORD_ERROR', 'Your Password must contain a minimum of ' . ACCOUNT_PASSWORD . ' characters.');
define('ENTRY_PASSWORD_ERROR_NOT_MATCHING', 'The Password Confirmation must match your Password.');
define('ENTRY_PASSWORD_CONFIRMATION', 'Password Confirmation:');

// images
define('IMAGE_ANI_SEND_EMAIL', 'Enviando E-Mail');
define('IMAGE_APPROVE', 'Approve');
define('IMAGE_BACK', 'Volver');
define('IMAGE_BACKUP', 'Copiar');
define('IMAGE_CANCEL', 'Cancelar');
define('IMAGE_CONFIRM', 'Confirmar');
define('IMAGE_COPY', 'Copiar');
define('IMAGE_COPY_TO', 'Copiar A');
define('IMAGE_DETAILS', 'Detalle');
define('IMAGE_DELETE', 'Eliminar');
define('IMAGE_EDIT', 'Editar');
define('IMAGE_EMAIL', 'Email');
define('IMAGE_FILE_MANAGER', 'Archivos');
define('IMAGE_ICON_STATUS_GREEN', 'Activado');
define('IMAGE_ICON_STATUS_GREEN_LIGHT', 'Activar');
define('IMAGE_ICON_STATUS_RED', 'Desactivado');
define('IMAGE_ICON_STATUS_RED_LIGHT', 'Desactivar');
define('IMAGE_ICON_INFO', 'Datos');
define('IMAGE_INSERT', 'Insertar');
define('IMAGE_LOCK', 'Bloqueado');
define('IMAGE_MODULE_INSTALL', 'Instalar M&oacute;dulo');
define('IMAGE_MODULE_REMOVE', 'Quitar M&oacute;dulo');
define('IMAGE_MOVE', 'Mover');
define('IMAGE_NEW_BANNER', 'Nuevo Banner');
define('IMAGE_NEW_CATEGORY', 'Nueva Categoria');
define('IMAGE_NEW_COUNTRY', 'Nuevo Pais');
define('IMAGE_NEW_CURRENCY', 'Nueva Moneda');
define('IMAGE_NEW_FILE', 'Nuevo Fichero');
define('IMAGE_NEW_FOLDER', 'Nueva Carpeta');
define('IMAGE_NEW_LANGUAGE', 'Nueva Idioma');
define('IMAGE_NEW_NEWSLETTER', 'Nuevo Bolet&iacute;n');
define('IMAGE_NEW_PRODUCT', 'Nuevo Producto');
define('IMAGE_NEW_TAX_CLASS', 'Nuevo Tipo de Impuesto');
define('IMAGE_NEW_TAX_RATE', 'Nuevo Impuesto');
define('IMAGE_NEW_TAX_ZONE', 'Nueva Zona');
define('IMAGE_NEW_ZONE', 'Nueva Zona');
define('IMAGE_ORDERS', 'Pedidos');
define('IMAGE_ORDERS_INVOICE', 'Factura');
define('IMAGE_ORDERS_PACKINGSLIP', 'Albar&aacute;n');
define('IMAGE_PREVIEW', 'Ver');
define('IMAGE_REJECT', 'Reject');
define('IMAGE_RESET', 'Resetear');
define('IMAGE_RESTORE', 'Restaurar');
define('IMAGE_SAVE', 'Grabar');
define('IMAGE_SEARCH', 'Buscar');
define('IMAGE_SELECT', 'Seleccionar');
define('IMAGE_SEND', 'Enviar');
define('IMAGE_SEND_EMAIL', 'Send Email');
define('IMAGE_UNLOCK', 'Desbloqueado');
define('IMAGE_UPDATE', 'Actualizar');
define('IMAGE_UPDATE_CURRENCIES', 'Actualizar Cambio de Moneda');
define('IMAGE_UPLOAD', 'Subir');

define('ICON_CROSS', 'Falso');
define('ICON_CURRENT_FOLDER', 'Directorio Actual');
define('ICON_DELETE', 'Eliminar');
define('ICON_ERROR', 'Error');
define('ICON_FILE', 'Fichero');
define('ICON_FILE_DOWNLOAD', 'Descargar');
define('ICON_FOLDER', 'Carpeta');
define('ICON_LOCKED', 'Bloqueado');
define('ICON_PREVIOUS_LEVEL', 'Nivel Anterior');
define('ICON_PREVIEW', 'Ver');
define('ICON_STATISTICS', 'Estadisticas');
define('ICON_SUCCESS', 'Exito');
define('ICON_TICK', 'Verdadero');
define('ICON_UNLOCKED', 'Desbloqueado');
define('ICON_WARNING', 'Advertencia');

define('BUTTON_CANCEL', 'Cancelar');
define('BUTTON_BACK', 'Volver');
define('BUTTON_DELETE', 'Eliminar');
define('BUTTON_INSERT', 'Insertar');
define('BUTTON_OK', 'OK');
define('BUTTON_SAVE', 'Grabar');
define('BUTTON_SEND', 'Send');

define('ICON_FILES', 'Files');
define('ICON_ORDERS', 'Orders');
define('ICON_PRODUCTS', 'Products');

// constants for use in tep_prev_next_display function
define('TEXT_RESULT_PAGE', 'P&aacute;gina&nbsp;%s&nbsp;de&nbsp;%d');
define('TEXT_DISPLAY_NUMBER_OF_BANNERS', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> banners)');
define('TEXT_DISPLAY_NUMBER_OF_CATEGORIES', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> categories)');
define('TEXT_DISPLAY_NUMBER_OF_COUNTRIES', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> paises)');
define('TEXT_DISPLAY_NUMBER_OF_CREDIT_CARDS', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> credit cards)');
define('TEXT_DISPLAY_NUMBER_OF_CUSTOMERS', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> clientes)');
define('TEXT_DISPLAY_NUMBER_OF_CURRENCIES', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> monedas)');
define('TEXT_DISPLAY_NUMBER_OF_LANGUAGES', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> idiomas)');
define('TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> fabricantes)');
define('TEXT_DISPLAY_NUMBER_OF_NEWSLETTERS', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> boletines)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> pedidos)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS_STATUS', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> estado de pedidos)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> productos)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS_EXPECTED', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> productos esperados)');
define('TEXT_DISPLAY_NUMBER_OF_REVIEWS', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> comentarios)');
define('TEXT_DISPLAY_NUMBER_OF_SPECIALS', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> ofertas)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_CLASSES', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> tipos de impuesto)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_ZONES', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> zonas de impuestos)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_RATES', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> porcentajes de impuestos)');
define('TEXT_DISPLAY_NUMBER_OF_WEIGHT_CLASSES', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> weight classes)');
define('TEXT_DISPLAY_NUMBER_OF_ZONES', 'Viendo del <b>%d</b> al <b>%d</b> (de <b>%d</b> zonas)');

define('PREVNEXT_BUTTON_PREV', '&lt;&lt;');
define('PREVNEXT_BUTTON_NEXT', '&gt;&gt;');

define('TEXT_DEFAULT', 'predeterminado/a');
define('TEXT_SET_DEFAULT', 'Establecer como predeterminado/a');
define('TEXT_FIELD_REQUIRED', '&nbsp;<span class="fieldRequired">* Obligatorio</span>');
define('TEXT_IMAGE_NONEXISTENT', 'NO EXISTE IMAGEN');

define('ERROR_NO_DEFAULT_CURRENCY_DEFINED', 'Error: No hay moneda predeterminada. Por favor establezca una en: Herramientas de Administracion->Localizaci&oacute;n->Monedas');

define('TEXT_CACHE_CATEGORIES', 'Categorias');
define('TEXT_CACHE_MANUFACTURERS', 'Fabricantes');
define('TEXT_CACHE_ALSO_PURCHASED', 'Tambi&eacute;n Han Comprado');

define('TEXT_NONE', '--ninguno--');
define('TEXT_TOP', 'Principio');

define('ERROR_DESTINATION_DOES_NOT_EXIST', 'Error: Destino no existe.');
define('ERROR_DESTINATION_NOT_WRITEABLE', 'Error: No se puede escribir en el destino.');
define('ERROR_FILE_NOT_REMOVEABLE', 'Error: No puedo eliminar este fichero. Asigne los permisos adecuados a: %s');
define('ERROR_FILE_NOT_SAVED', 'Error: El archivo subido no se ha guardado.');
define('ERROR_FILETYPE_NOT_ALLOWED', 'Error: Extension de fichero no permitida.');
define('SUCCESS_FILE_SAVED_SUCCESSFULLY', 'Exito: Fichero guardado con &eacute;xito.');
define('WARNING_NO_FILE_UPLOADED', 'Advertencia: No se ha subido ningun archivo.');
define('WARNING_FILE_UPLOADS_DISABLED', 'Warning: Se ha desactivado la subida de archivos en el fichero de configuraci&oacute;n php.ini.');

define('SUCCESS_DB_ROWS_UPDATED', 'Success: Entry successfully updated!');
define('WARNING_DB_ROWS_NOT_UPDATED', 'Warning: Entry not updated due to the data content being the same.');
define('ERROR_DB_ROWS_NOT_UPDATED', 'Error: Entry not updated due to an error.');

define('MAXIMUM_FILE_UPLOAD_SIZE', '(Max: %s)');
?>

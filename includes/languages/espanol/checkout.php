<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  define('NAVBAR_TITLE_CHECKOUT', 'Realizar Pedido');
  define('NAVBAR_TITLE_CHECKOUT_SHOPPING_CART', 'ontenido de la Cesta');
  define('NAVBAR_TITLE_CHECKOUT_SHIPPING', 'Forma de Env&iacute;o');
  define('NAVBAR_TITLE_CHECKOUT_SHIPPING_ADDRESS', 'Shipping Address');
  define('NAVBAR_TITLE_CHECKOUT_PAYMENT', 'Forma de Pago');
  define('NAVBAR_TITLE_CHECKOUT_PAYMENT_ADDRESS', 'Payment Address');
  define('NAVBAR_TITLE_CHECKOUT_CONFIRMATION', 'Confirmaci&oacute;n');
  define('NAVBAR_TITLE_CHECKOUT_SUCCESS', 'Realizado con Exito!');

  define('HEADING_TITLE_CHECKOUT_SHOPPING_CART', 'ontenido de la Cesta');
  define('HEADING_TITLE_CHECKOUT_SHIPPING', 'Forma de Env&iacute;o');
  define('HEADING_TITLE_CHECKOUT_SHIPPING_ADDRESS', 'Shipping Address');
  define('HEADING_TITLE_CHECKOUT_PAYMENT', 'Forma de Pago');
  define('HEADING_TITLE_CHECKOUT_PAYMENT_ADDRESS', 'Payment Address');
  define('HEADING_TITLE_CHECKOUT_CONFIRMATION', 'Confirmaci&oacute;n');
  define('HEADING_TITLE_CHECKOUT_SUCCESS', 'Su Pedido ha sido Procesado!');

  define('TABLE_HEADING_SHIPPING_ADDRESS', 'Direcci&oacute;n de Entrega');
  define('TEXT_CHOOSE_SHIPPING_DESTINATION', 'Escoja una direcci&oacute;n de su libreta para la entrega de los productos de este pedido.');
  define('TEXT_SELECTED_SHIPPING_DESTINATION', 'Esta es la direcci&oacute;n de entrega seleccionada para el env&iacute;o de los productos de su pedido.');
  define('TITLE_SHIPPING_ADDRESS', 'Direcci&oacute;n de Entrega:');

  define('TITLE_PLEASE_SELECT', 'Seleccione');

  define('TABLE_HEADING_SHIPPING_METHOD', 'Forma de Env&iacute;o');
  define('TEXT_CHOOSE_SHIPPING_METHOD', 'Seleccione la forma de env&iacute;o preferida para la entrega de este pedido.');
  define('TEXT_ENTER_SHIPPING_INFORMATION', 'Esta es la unica forma de env&iacute;o disponible para su pedido.');

  define('TABLE_HEADING_COMMENTS', 'Agregue Los Comentarios Sobre Su Orden');

  define('TABLE_HEADING_ADDRESS_BOOK_ENTRIES', 'Libreta de Direcciones');
  define('TEXT_SELECT_OTHER_SHIPPING_DESTINATION', 'Seleccione otra direcci&oacute;n de entrega para su pedido si quiere que sea entregado en un sitio diferente.');
  define('TEXT_SELECT_OTHER_PAYMENT_DESTINATION', 'Seleccione la direcci&oacute;n para el env&iacute;o de la factura de este pedido si quiere que sea enviada a un sitio diferente.');
  define('TITLE_PLEASE_SELECT', 'Seleccione');

  define('TABLE_HEADING_NEW_SHIPPING_ADDRESS', 'Nueva Direcci&oacute;n');
  define('TABLE_HEADING_NEW_PAYMENT_ADDRESS', 'Nueva Direcci&oacute;n de Facturaci&oacute;n');
  define('TEXT_CREATE_NEW_SHIPPING_ADDRESS', 'Use el formulario siguiente para crear una direcci&oacute;n nueva en su libreta y usarla como direcci&oacute;n de entrega para su pedido.');
  define('TEXT_CREATE_NEW_PAYMENT_ADDRESS', 'Use el formulario siguiente para crear una nueva direcci&oacute;n en su libreta y usarla como direcci&oacute;n de facturaci&oacute;n en este pedido.');

  define('TABLE_HEADING_PAYMENT_ADDRESS', 'Direcci&oacute;n de Facturaci&oacute;n');
  define('TEXT_SELECTED_PAYMENT_DESTINATION', 'Esta es la direcci&oacute;n de facturaci&oacute;n seleccionada, donde se enviar&aacute; la factura.');
  define('TITLE_PAYMENT_ADDRESS', 'Direcci&oacute;n de Facturaci&oacute;n:');

  define('TITLE_CONTINUE_CHECKOUT_PROCEDURE', 'Continuar con el Proceso de Compra');
  define('TEXT_CONTINUE_CHECKOUT_PROCEDURE_TO_SHIPPING', 'para seleccionar la forma de envio.');
  define('TEXT_CONTINUE_CHECKOUT_PROCEDURE_TO_PAYMENT', 'para seleccionar la forma de pago.');
  define('TEXT_CONTINUE_CHECKOUT_PROCEDURE_TO_CONFIRMATION', 'para confirmar este pedido.');

  define('TABLE_HEADING_REMOVE', 'Quitar');
  define('TABLE_HEADING_QUANTITY', 'Cantidad');
  define('TABLE_HEADING_MODEL', 'Modelo');
  define('TABLE_HEADING_PRODUCTS', 'Producto(s)');
  define('TABLE_HEADING_TOTAL', 'Total');
  define('TEXT_CART_EMPTY', 'Tu Cesta de la Compra esta vacia!');
  define('SUB_TITLE_SUB_TOTAL', 'Subtotal:');
  define('SUB_TITLE_TOTAL', 'Total:');

  define('OUT_OF_STOCK_CANT_CHECKOUT', 'Los productos marcados con ' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' no estan disponibles en la cantidad que requiere.<br>Modifique la cantidad de productos marcados con ' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . ', Gracias');
  define('OUT_OF_STOCK_CAN_CHECKOUT', 'Los productos marcados con ' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' no estan disponibles en cantidad que requiere.<br>De todas formas, puede comprar los que hay disponibles y el resto se lo enviamos mas tarde o esperar a que la cantidad requerida este disponible.');

  define('TABLE_HEADING_BILLING_ADDRESS', 'Direcci&oacute;n de Facturaci&oacute;n');
  define('TEXT_SELECTED_BILLING_DESTINATION', 'Elija la direcci&oacute;n de su libreta donde quiera recibir la factura.');
  define('TITLE_BILLING_ADDRESS', 'Direcci&oacute;n de Facturaci&oacute;n:');

  define('TABLE_HEADING_CONDITIONS', 'Terms and Conditions');
  define('TEXT_CONDITIONS_DESCRIPTION', 'Please acknowledge the terms and conditions bound to this order by ticking the following box. The terms and conditions can be read <a href="' . tep_href_link(FILENAME_CONDITIONS, '', 'SSL') . '"><u>here</u></a>.');
  define('TEXT_CONDITIONS_CONFIRM', 'I have read and agreed to the terms and conditions bound to this order.');

  define('TABLE_HEADING_PAYMENT_METHOD', 'Forma de Pago');
  define('TEXT_SELECT_PAYMENT_METHOD', 'Escoja la forma de pago preferida para este pedido.');
  define('TEXT_ENTER_PAYMENT_INFORMATION', 'Esta es la unica forma de pago disponible para este pedido.');

  define('HEADING_DELIVERY_ADDRESS', 'Direcci&oacute;n de Entrega');
  define('HEADING_SHIPPING_METHOD', 'Forma de Envio');
  define('HEADING_PRODUCTS', 'Producto');
  define('HEADING_TAX', 'Impuestos');
  define('HEADING_TOTAL', 'Total');
  define('HEADING_BILLING_INFORMATION', 'Datos de Facturaci&oacute;n');
  define('HEADING_BILLING_ADDRESS', 'Direcci&oacute;n de Facturaci&oacute;n');
  define('HEADING_PAYMENT_METHOD', 'Forma de Pago');
  define('HEADING_PAYMENT_INFORMATION', 'Datos del Pago');
  define('HEADING_ORDER_COMMENTS', 'Comentarios Sobre Su Orden');

  define('TEXT_EDIT', 'Cambio');

  define('EMAIL_TEXT_SUBJECT', 'Procesar Pedido');
  define('EMAIL_TEXT_ORDER_NUMBER', 'Número de Pedido:');
  define('EMAIL_TEXT_INVOICE_URL', 'Pedido Detallado:');
  define('EMAIL_TEXT_DATE_ORDERED', 'Fecha del Pedido:');
  define('EMAIL_TEXT_PRODUCTS', 'Productos');
  define('EMAIL_TEXT_SUBTOTAL', 'Subtotal:');
  define('EMAIL_TEXT_TAX', 'Impuestos:      ');
  define('EMAIL_TEXT_SHIPPING', 'Gastos de Envío: ');
  define('EMAIL_TEXT_TOTAL', 'Total:    ');
  define('EMAIL_TEXT_DELIVERY_ADDRESS', 'Direcciön de Entrega');
  define('EMAIL_TEXT_BILLING_ADDRESS', 'Dirección de Facturación');
  define('EMAIL_TEXT_PAYMENT_METHOD', 'Forma de Pago');

  define('EMAIL_SEPARATOR', '------------------------------------------------------');
  define('TEXT_EMAIL_VIA', 'por');

  define('TEXT_SUCCESS', 'Su pedido ha sido realizado con &eacute;xito! Sus productos llegar&aacute;n a su destino de 2 a 5 dias laborales.');
  define('TEXT_NOTIFY_PRODUCTS', 'Por favor notifiqueme de cambios realizados a los productos seleccionados:');
  define('TEXT_SEE_ORDERS', 'Puede ver sus pedidos viendo la pagina de <a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">\'Su Cuenta\'</a> y pulsando sobre <a href="' . tep_href_link(FILENAME_ACCOUNT, 'orders', 'SSL') . '">\'Historial\'</a>.');
  define('TEXT_CONTACT_STORE_OWNER', 'Dirija sus preguntas al <a href="' . tep_href_link(FILENAME_CONTACT_US) . '">administrador</a>.');
  define('TEXT_THANKS_FOR_SHOPPING', '¡Gracias por comprar con nosotros!');

  define('TABLE_HEADING_DOWNLOAD_DATE', 'Fecha Caducidad: ');
  define('TABLE_HEADING_DOWNLOAD_COUNT', ' descargas restantes');
  define('HEADING_DOWNLOAD', 'Descargue sus productos aqui:');
  define('FOOTER_DOWNLOAD', 'Puede descargar sus productos mas tarde en \'%s\'');
?>

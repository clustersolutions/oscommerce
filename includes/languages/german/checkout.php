<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  define('NAVBAR_TITLE_CHECKOUT', 'Kasse');
  define('NAVBAR_TITLE_CHECKOUT_SHOPPING_CART', 'Warenkorb');
  define('NAVBAR_TITLE_CHECKOUT_SHIPPING', 'Versandinformationen');
  define('NAVBAR_TITLE_CHECKOUT_SHIPPING_ADDRESS', 'Versandadresse');
  define('NAVBAR_TITLE_CHECKOUT_PAYMENT', 'Zahlungsweise');
  define('NAVBAR_TITLE_CHECKOUT_PAYMENT_ADDRESS', 'Rechnungsadresse');
  define('NAVBAR_TITLE_CHECKOUT_CONFIRMATION', 'Best&auml;tigung');
  define('NAVBAR_TITLE_CHECKOUT_SUCCESS', 'Erfolg!');

  define('HEADING_TITLE_CHECKOUT_SHOPPING_CART', 'Warenkorb');
  define('HEADING_TITLE_CHECKOUT_SHIPPING', 'Versandinformationen');
  define('HEADING_TITLE_CHECKOUT_SHIPPING_ADDRESS', 'Versandadresse');
  define('HEADING_TITLE_CHECKOUT_PAYMENT', 'Zahlungsweise');
  define('HEADING_TITLE_CHECKOUT_PAYMENT_ADDRESS', 'Payment Address');
  define('HEADING_TITLE_CHECKOUT_CONFIRMATION', 'Best&auml;tigung');
  define('HEADING_TITLE_CHECKOUT_SUCCESS', 'Ihr Bestellung ist ausgef&uuml;hrt worden!');

  define('TABLE_HEADING_SHIPPING_ADDRESS', 'Versandadresse');
  define('TEXT_CHOOSE_SHIPPING_DESTINATION', 'Bitte w&auml;hlen Sie aus Ihrem Adressbuch die gew&uuml;nschte Versandadresse f&uuml;r Ihre Bestellung aus.');
  define('TEXT_SELECTED_SHIPPING_DESTINATION', 'Dies ist die aktuell ausgew&auml;hlte Versandadresse, an die Ihre Bestellung geliefert wird.');
  define('TITLE_SHIPPING_ADDRESS', 'Versandadresse:');

  define('TITLE_PLEASE_SELECT', 'Bitte w&auml;hlen Sie');

  define('TABLE_HEADING_SHIPPING_METHOD', 'Versandart');
  define('TEXT_CHOOSE_SHIPPING_METHOD', 'Bitte w&auml;hlen Sie die gew&uuml;nschte Versandart f&uuml;r Ihre Bestellung aus.');
  define('TEXT_ENTER_SHIPPING_INFORMATION', 'Zur Zeit bieten wir Ihnen nur eine Versandart an.');

  define('TABLE_HEADING_COMMENTS', 'F&uuml;gen Sie hier Ihre Anmerkungen zu dieser Bestellung ein');

  define('TABLE_HEADING_ADDRESS_BOOK_ENTRIES', 'Adressbucheintr&auml;ge');
  define('TEXT_SELECT_OTHER_SHIPPING_DESTINATION', 'Bitte w&auml;hlen Sie die gew&uuml;nschte Versandadresse, an die wir die Auslieferung vornehmen sollen.');
  define('TEXT_SELECT_OTHER_PAYMENT_DESTINATION', 'Bitte w&auml;hlen Sie die gew&uuml;nschte Rechnungsadresse, auf die wir die Rechnung ausstellen sollen.');
  define('TITLE_PLEASE_SELECT', 'Bitte w&auml;hlen Sie');

  define('TABLE_HEADING_NEW_SHIPPING_ADDRESS', 'Neue Versandadresse');
  define('TABLE_HEADING_NEW_PAYMENT_ADDRESS', 'Neue Rechnungsadresse');
  define('TEXT_CREATE_NEW_SHIPPING_ADDRESS', 'Bitte nutzen Sie dieses Formular, um eine neue Versandadresse f&uuml;r Ihre Bestellung zu erfassen.');
  define('TEXT_CREATE_NEW_PAYMENT_ADDRESS', 'Bitte nutzen Sie dieses Formular, um eine neue Rechnungsadresse f&uuml;r Ihre Bestellung zu erfassen.');

  define('TABLE_HEADING_PAYMENT_ADDRESS', 'Rechnungsadresse');
  define('TEXT_SELECTED_PAYMENT_DESTINATION', 'Dies ist die aktuell ausgew&auml;hlte Rechnungsadresse, auf die die Rechnung ausgestellt wird.');
  define('TITLE_PAYMENT_ADDRESS', 'Rechnungsadresse:');

  define('TITLE_CONTINUE_CHECKOUT_PROCEDURE', 'Fortsetzung des Bestellvorganges');
  define('TEXT_CONTINUE_CHECKOUT_PROCEDURE_TO_SHIPPING', 'zur Auswahl der gew&uuml;nschten Versandart.');
  define('TEXT_CONTINUE_CHECKOUT_PROCEDURE_TO_PAYMENT', 'zur Auswahl der gew&uuml;nschten Zahlungsweise.');
  define('TEXT_CONTINUE_CHECKOUT_PROCEDURE_TO_CONFIRMATION', 'zur Best&auml;tigung Ihrer Bestellung.');

  define('TABLE_HEADING_REMOVE', 'Entfernen');
  define('TABLE_HEADING_QUANTITY', 'Anzahl');
  define('TABLE_HEADING_MODEL', 'Artikelnr.');
  define('TABLE_HEADING_PRODUCTS', 'Artikel');
  define('TABLE_HEADING_TOTAL', 'Summe');
  define('TEXT_CART_EMPTY', 'Sie haben noch nichts in Ihrem Warenkorb.');
  define('SUB_TITLE_SUB_TOTAL', 'Zwischensumme:');
  define('SUB_TITLE_TOTAL', 'Summe:');

  define('OUT_OF_STOCK_CANT_CHECKOUT', 'Die mit ' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' markierten Produkte, sind leider nicht in der von Ihnen gew&uuml;nschten Menge auf Lager.<br />Bitte reduzieren Sie Ihre Bestellmenge f&uuml;r die gekennzeichneten Produkte, vielen Dank');
  define('OUT_OF_STOCK_CAN_CHECKOUT', 'Die mit ' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . ' markierten Produkte, sind leider nicht in der von Ihnen gew&uuml;nschten Menge auf Lager.<br />Die bestellte Menge wird kurzfristig von uns geliefert, wenn Sie es w&uuml;nschen nehmen wir auch eine Teillieferung vor.');

  define('TABLE_HEADING_BILLING_ADDRESS', 'Rechnungsadresse');
  define('TEXT_SELECTED_BILLING_DESTINATION', 'Bitte w&auml;hlen Sie aus Ihrem Adressbuch die gew&uuml;nschte Rechnungsadresse f&uuml;r Ihre Bestellung aus.');
  define('TITLE_BILLING_ADDRESS', 'Rechnungsadresse:');

  define('TABLE_HEADING_CONDITIONS', 'Terms and Conditions');
  define('TEXT_CONDITIONS_DESCRIPTION', 'Please acknowledge the terms and conditions bound to this order by ticking the following box. The terms and conditions can be read <a href="' . tep_href_link(FILENAME_INFO, 'conditions', 'SSL') . '"><u>here</u></a>.');
  define('TEXT_CONDITIONS_CONFIRM', 'I have read and agreed to the terms and conditions bound to this order.');

  define('TABLE_HEADING_PAYMENT_METHOD', 'Zahlungsweise');
  define('TEXT_SELECT_PAYMENT_METHOD', 'Bitte w&auml;hlen Sie die gew&uuml;nschte Zahlungsweise f&uuml;r Ihre Bestellung aus.');
  define('TEXT_ENTER_PAYMENT_INFORMATION', 'Zur Zeit bieten wir Ihnen nur eine Zahlungsweise an.');

  define('HEADING_DELIVERY_ADDRESS', 'Versandadresse');
  define('HEADING_SHIPPING_METHOD', 'Versandart');
  define('HEADING_PRODUCTS', 'Produkte');
  define('HEADING_TAX', 'MwSt.');
  define('HEADING_TOTAL', 'Summe');
  define('HEADING_BILLING_INFORMATION', 'Rechnungsinformationen');
  define('HEADING_BILLING_ADDRESS', 'Rechnungsadresse');
  define('HEADING_PAYMENT_METHOD', 'Zahlungsweise');
  define('HEADING_PAYMENT_INFORMATION', 'Zahlungsinformationen');
  define('HEADING_ORDER_COMMENTS', 'Anmerkung zu Ihrer Bestellung');

  define('TEXT_EDIT', 'Bearbeiten');

  define('EMAIL_TEXT_SUBJECT', 'Bestellung');
  define('EMAIL_TEXT_ORDER_NUMBER', 'Bestellnummer:');
  define('EMAIL_TEXT_INVOICE_URL', 'Detailierte Bestell&uuml;bersicht:');
  define('EMAIL_TEXT_DATE_ORDERED', 'Bestelldatum:');
  define('EMAIL_TEXT_PRODUCTS', 'Artikel');
  define('EMAIL_TEXT_SUBTOTAL', 'Zwischensumme:');
  define('EMAIL_TEXT_TAX', 'MwSt.');
  define('EMAIL_TEXT_SHIPPING', 'Versandkosten:');
  define('EMAIL_TEXT_TOTAL', 'Summe:        ');
  define('EMAIL_TEXT_DELIVERY_ADDRESS', 'Lieferanschrift');
  define('EMAIL_TEXT_BILLING_ADDRESS', 'Rechnungsanschrift');
  define('EMAIL_TEXT_PAYMENT_METHOD', 'Zahlungsweise');

  define('EMAIL_SEPARATOR', '------------------------------------------------------');
  define('TEXT_EMAIL_VIA', 'durch');

  define('TEXT_SUCCESS', 'Ihre Bestellung ist eingegangen und wird bearbeitet! Die Lieferung erfolgt innerhalb von ca. 2-5 Werktagen.');
  define('TEXT_NOTIFY_PRODUCTS', 'Bitte benachrichtigen Sie mich &uuml;ber Aktuelles zu folgenden Produkten:');
  define('TEXT_SEE_ORDERS', 'Sie k&ouml;nnen Ihre Bestellung(en) auf der Seite <a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '"><u>\'Ihr Konto\'</a></u> jederzeit einsehen und sich dort auch Ihre <a href="' . tep_href_link(FILENAME_ACCOUNT, 'orders', 'SSL') . '"><u>\'Bestell&uuml;bersicht\'</u></a> anzeigen lassen.');
  define('TEXT_CONTACT_STORE_OWNER', 'Falls Sie Fragen bez&uuml;glich Ihrer Bestellung haben, wenden Sie sich an unseren <a href="' . tep_href_link(FILENAME_INFO, 'contact') . '"><u>Vertrieb</u></a>.');
  define('TEXT_THANKS_FOR_SHOPPING', 'Wir danken Ihnen f&uuml;r Ihren Online-Einkauf!');

  define('TABLE_HEADING_DOWNLOAD_DATE', 'herunterladen m&ouml;glich bis:');
  define('TABLE_HEADING_DOWNLOAD_COUNT', 'max. Anz. Downloads');
  define('HEADING_DOWNLOAD', 'Artikel herunterladen:');
  define('FOOTER_DOWNLOAD', 'Sie k&ouml;nnen Ihre Artikel auch sp&auml;ter unter \'%s\' herunterladen');
?>

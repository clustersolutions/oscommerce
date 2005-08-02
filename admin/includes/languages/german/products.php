<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  define('HEADING_TITLE', 'Produkte');
  define('HEADING_TITLE_SEARCH', 'Suche: ');
  define('HEADING_TITLE_GOTO', 'Gehe zu:');

  define('TAB_GENERAL', 'Allgemein');
  define('TAB_DATA', 'Data');
  define('TAB_IMAGES', 'Bilder');
  define('TAB_ATTRIBUTES', 'Produktmerkmalen');
  define('TAB_CATEGORIES', 'Kategorien');

  define('FIELDSET_ASSIGNED_ATTRIBUTES', 'Assigned Attributes');

  define('TABLE_HEADING_PRODUCTS', 'Produkte');
  define('TABLE_HEADING_PRICE', 'Preis');
  define('TABLE_HEADING_QUANTITY', 'Anzahl');
  define('TABLE_HEADING_STATUS', 'Status');
  define('TABLE_HEADING_ACTION', 'Aktion');

  define('TEXT_NEW_PRODUCT', 'Neuer Produkt in &quot;%s&quot;');
  define('TEXT_CATEGORIES', 'Kategorien:');

  define('TEXT_EDIT_INTRO', 'Bitte f&uuml;hren Sie alle notwendigen &Auml;nderungen durch.');

  define('TEXT_INFO_COPY_TO_INTRO', 'Bitte w&auml;hlen Sie eine neue Kategorie aus, in die Sie den Artikel kopieren m&ouml;chten:');
  define('TEXT_INFO_CURRENT_CATEGORIES', 'aktuelle Kategorien:');

  define('TEXT_DELETE_PRODUCT_INTRO', 'Sind Sie sicher, dass Sie diesen Artikel l&ouml;schen m&ouml;chten?');

  define('TEXT_MOVE_PRODUCTS_INTRO', 'Bitte w&auml;hlen Sie die &uuml;bergordnete Kategorie, in die Sie <b>%s</b> verschieben m&ouml;chten');
  define('TEXT_MOVE', 'Verschiebe <b>%s</b> nach:');

  define('TEXT_PRODUCTS_STATUS', 'Produktstatus:');
  define('TEXT_PRODUCTS_DATE_AVAILABLE', 'Erscheinungsdatum:');
  define('TEXT_PRODUCT_AVAILABLE', 'auf Lager');
  define('TEXT_PRODUCT_NOT_AVAILABLE', 'nicht vorr&auml;tig');
  define('TEXT_PRODUCTS_MANUFACTURER', 'Artikel-Hersteller:');
  define('TEXT_PRODUCTS_NAME', 'Artikelname:');
  define('TEXT_PRODUCTS_DESCRIPTION', 'Artikelbeschreibung:');
  define('TEXT_PRODUCTS_QUANTITY', 'Artikelanzahl:');
  define('TEXT_PRODUCTS_MODEL', 'Artikel-Nr.:');
  define('TEXT_PRODUCTS_IMAGE', 'Artikelbild:');
  define('TEXT_PRODUCTS_URL', 'Herstellerlink:');
  define('TEXT_PRODUCTS_URL_WITHOUT_HTTP', '<small>(ohne f&uuml;hrendes http://)</small>');
  define('TEXT_PRODUCTS_TAX_CLASS', 'Steuerklasse:');
  define('TEXT_PRODUCTS_PRICE_NET', 'Artikelpreis (Netto):');
  define('TEXT_PRODUCTS_PRICE_GROSS', 'Artikelpreis (Brutto):');
  define('TEXT_PRODUCTS_WEIGHT', 'Artikelgewicht:');

  define('TEXT_PRODUCT_DATE_ADDED', 'Diesen Artikel haben wir am %s in unseren Katalog aufgenommen.');
  define('TEXT_PRODUCT_DATE_AVAILABLE', 'Dieser Artikel ist erh&auml;ltlich ab %s.');
  define('TEXT_PRODUCT_MORE_INFORMATION', 'F&uuml;r weitere Informationen, besuchen Sie bitte die <a href="http://%s" target="blank"><u>Homepage</u></a> des Herstellers.');

  define('TEXT_HOW_TO_COPY', 'Kopiermethode:');
  define('TEXT_COPY_AS_LINK', 'Produkt verlinken');
  define('TEXT_COPY_AS_DUPLICATE', 'Produkt duplizieren');

  define('ERROR_CANNOT_LINK_TO_SAME_CATEGORY', 'Fehler: Produkte k&ouml;nnen nicht in der gleichen Kategorie verlinkt werden.');
  define('ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE', 'Fehler: Das Verzeichnis \'images\' im Katalogverzeichnis ist schreibgesch&uuml;tzt: ' . realpath('../images'));
  define('ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST', 'Fehler: Das Verzeichnis \'images\' im Katalogverzeichnis ist nicht vorhanden: ' . realpath('../images'));
?>

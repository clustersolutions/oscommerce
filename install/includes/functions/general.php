<?php
/*
  $Id: general.php,v 1.7 2004/04/15 17:49:29 sparky Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  function tep_get_languages() {
    global $osC_Database;

    $Qlanguages = $osC_Database->query('select languages_id, name, code, image, directory from languages order by sort_order');
    while ($Qlanguages->next()) {
      $languages_array[] = array('id' => $Qlanguages->valueInt('languages_id'),
                                 'name' => $Qlanguages->value('name'),
                                 'code' => $Qlanguages->value('code'),
                                 'image' => $Qlanguages->value('image'),
                                 'directory' => $Qlanguages->value('directory'));
    }

    $Qlanguages->freeResult();

    return $languages_array;
  }

  function tep_currency_format($number, $calculate_currency_value = true, $currency_code = 'USD', $value = '') {
    $Qcurrencies = $osC_Database->query('select symbol_left, symbol_right, decimal_places, value from currencies where code = :code');
    $Qcurrencies->bindValue(':code', $currency_code);
    $Qcurrencies->execute();

    if ($calculate_currency_value == true) {
      if (strlen($currency_code) == 3) {
        if ($value) {
          $rate = $value;
        } else {
          $rate = $Qcurrencies->value('value');
        }
      } else {
        $rate = 1;
      }
      $number2currency = $Qcurrencies->value('symbol_left') . number_format(($number * $rate), $Qcurrencies->value('decimal_places'), NUMERIC_DECIMAL_SEPARATOR, NUMERIC_THOUSANDS_SEPARATOR) . $Qcurrencies->value('symbol_right');
    } else {
      $number2currency = $Qcurrencies->value('symbol_left') . number_format($number, $Qcurrencies->value('decimal_places'), NUMERIC_DECIMAL_SEPARATOR, NUMERIC_THOUSANDS_SEPARATOR) . $Qcurrencies->value('symbol_right');
    }

    $Qcurrencies->freeResult();

    return $number2currency;
  }
?>

<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Shop;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  class Payment {
    protected $_modules = array();

    public function __construct() {
      $OSCOM_PDO = Registry::get('PDO');
      $OSCOM_Language = Registry::get('Language');

      $Qmodules = $OSCOM_PDO->prepare('select code from :table_modules where modules_group = :modules_group');
      $Qmodules->bindValue(':modules_group', 'Payment');
      $Qmodules->setCache('modules-payment');
      $Qmodules->execute();

      while ( $Qmodules->fetch() ) {
        $this->_modules[] = $Qmodules->value('code');
      }

      $OSCOM_Language->load('modules-payment');
    }

    public function loadAll() {
      foreach ( $this->_modules as $module ) {
        $module_class = 'osCommerce\\OM\\Core\\Site\\Shop\\Module\\Payment\\' . $module;

        Registry::set('Payment_' . $module, new $module_class(), true);
      }

      usort($this->_modules, function ($a, $b) {
        if ( Registry::get('Payment_' . $a)->getSortOrder() == Registry::get('Payment_' . $b)->getSortOrder() ) {
          return strnatcasecmp(Registry::get('Payment_' . $a)->getTitle(), Registry::get('Payment_' . $b)->getTitle());
        }

        return (Registry::get('Payment_' . $a)->getSortOrder() < Registry::get('Payment_' . $b)->getSortOrder()) ? -1 : 1;
      });
    }

    public function load($module) {
      if ( in_array($module, $this->_modules) ) {
        $module_class = 'osCommerce\\OM\\Core\\Site\\Shop\\Module\\Payment\\' . $module;

        Registry::set('PaymentModule', new $module_class(), true);
      }
    }

    function sendTransactionToGateway($url, $parameters, $header = '', $method = 'post', $certificate = '') {
      if (empty($header) || !is_array($header)) {
        $header = array();
      }

      $server = parse_url($url);

      if (isset($server['port']) === false) {
        $server['port'] = ($server['scheme'] == 'https') ? 443 : 80;
      }

      if (isset($server['path']) === false) {
        $server['path'] = '/';
      }

      if (isset($server['user']) && isset($server['pass'])) {
        $header[] = 'Authorization: Basic ' . base64_encode($server['user'] . ':' . $server['pass']);
      }

      $connection_method = 0;

      if (function_exists('curl_init')) {
        $connection_method = 1;
      } elseif ( ($server['scheme'] == 'http') || (($server['scheme'] == 'https') && extension_loaded('openssl')) ) {
        if (function_exists('stream_context_create')) {
          $connection_method = 3;
        } else {
          $connection_method = 2;
        }
      }

      $result = '';

      switch ($connection_method) {
        case 1:
          $curl = curl_init($server['scheme'] . '://' . $server['host'] . $server['path'] . (isset($server['query']) ? '?' . $server['query'] : ''));
          curl_setopt($curl, CURLOPT_PORT, $server['port']);

          if (!empty($header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
          }

          if (!empty($certificate)) {
            curl_setopt($curl, CURLOPT_SSLCERT, $certificate);
          }

          curl_setopt($curl, CURLOPT_HEADER, 0);
          curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
          curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
          curl_setopt($curl, CURLOPT_POST, 1);
          curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);

          $result = curl_exec($curl);

          curl_close($curl);

          break;

        case 2:
          if ($fp = @fsockopen(($server['scheme'] == 'https' ? 'ssl' : $server['scheme']) . '://' . $server['host'], $server['port'])) {
            @fputs($fp, 'POST ' . $server['path'] . (isset($server['query']) ? '?' . $server['query'] : '') . ' HTTP/1.1' . "\r\n" .
                        'Host: ' . $server['host'] . "\r\n" .
                        'Content-type: application/x-www-form-urlencoded' . "\r\n" .
                        'Content-length: ' . strlen($parameters) . "\r\n" .
                        (!empty($header) ? implode("\r\n", $header) . "\r\n" : '') .
                        'Connection: close' . "\r\n\r\n" .
                        $parameters . "\r\n\r\n");

            $result = @stream_get_contents($fp);

            @fclose($fp);

            $result = trim(substr($result, strpos($result, "\r\n\r\n", strpos(strtolower($result), 'content-length:'))));
          }

          break;

        case 3:
          $options = array('http' => array('method' => 'POST',
                                           'header' => 'Host: ' . $server['host'] . "\r\n" .
                                                       'Content-type: application/x-www-form-urlencoded' . "\r\n" .
                                                       'Content-length: ' . strlen($parameters) . "\r\n" .
                                                       (!empty($header) ? implode("\r\n", $header) . "\r\n" : '') .
                                                       'Connection: close',
                                           'content' => $parameters));

          if (!empty($certificate)) {
            $options['ssl'] = array('local_cert' => $certificate);
          }

          $context = stream_context_create($options);

          if ($fp = fopen($url, 'r', false, $context)) {
            $result = '';

            while (!feof($fp)) {
              $result .= fgets($fp, 4096);
            }

            fclose($fp);
          }

          break;

        default:
          exec(escapeshellarg(CFG_APP_CURL) . ' -d ' . escapeshellarg($parameters) . ' "' . $server['scheme'] . '://' . $server['host'] . $server['path'] . (isset($server['query']) ? '?' . $server['query'] : '') . '" -P ' . $server['port'] . ' -k' . (!empty($header) ? ' -H ' . escapeshellarg(implode("\r\n", $header)) : '') . (!empty($certificate) ? ' -E ' . escapeshellarg($certificate) : ''), $result);
          $result = implode("\n", $result);
      }

      return $result;
    }

    function getJavascriptBlocks() {
      $js = '';

      if ( is_array($this->_modules) ) {
        $js = '<script type="text/javascript"><!-- ' . "\n" .
              'function check_form() {' . "\n" .
              '  var error = 0;' . "\n" .
              '  var error_message = "' . OSCOM::getDef('js_error') . '";' . "\n" .
              '  var payment_value = null;' . "\n" .
              '  if (document.checkout_payment.payment_method.length) {' . "\n" .
              '    for (var i=0; i<document.checkout_payment.payment_method.length; i++) {' . "\n" .
              '      if (document.checkout_payment.payment_method[i].checked) {' . "\n" .
              '        payment_value = document.checkout_payment.payment_method[i].value;' . "\n" .
              '      }' . "\n" .
              '    }' . "\n" .
              '  } else if (document.checkout_payment.payment_method.checked) {' . "\n" .
              '    payment_value = document.checkout_payment.payment_method.value;' . "\n" .
              '  } else if (document.checkout_payment.payment_method.value) {' . "\n" .
              '    payment_value = document.checkout_payment.payment_method.value;' . "\n" .
              '  }' . "\n\n";

        foreach ( $this->_modules as $module ) {
          if ( Registry::get('Payment_' . $module)->isEnabled() ) {
            $js .= Registry::get('Payment_' . $module)->getJavascriptBlock();
          }
        }

        $js .= "\n" . '  if (payment_value == null) {' . "\n" .
               '    error_message = error_message + "' . OSCOM::getDef('js_no_payment_module_selected') . '\n";' . "\n" .
               '    error = 1;' . "\n" .
               '  }' . "\n\n" .
               '  if (error == 1) {' . "\n" .
               '    alert(error_message);' . "\n" .
               '    return false;' . "\n" .
               '  } else {' . "\n" .
               '    return true;' . "\n" .
               '  }' . "\n" .
               '}' . "\n" .
               '//--></script>' . "\n";
      }

      return $js;
    }

    function selection() {
      $selection_array = array();

      foreach ($this->_modules as $module) {
        if (Registry::get('Payment_' . $module)->isEnabled()) {
          $selection = Registry::get('Payment_' . $module)->selection();
          if (is_array($selection)) $selection_array[] = $selection;
        }
      }

      return $selection_array;
    }

    public function hasActive() {
      $has_active = false;

      foreach ( $this->_modules as $module ) {
        if ( Registry::get('Payment_' . $module)->isEnabled() ) {
          $has_active = true;
          break;
        }
      }

      return $has_active;
    }

    public function numberOfActive() {
      $active = 0;

      foreach ( $this->_modules as $module ) {
        if ( Registry::get('Payment_' . $module)->isEnabled() ) {
          $active++;
        }
      }

      return $active;
    }

    public function getActive() {
      $active = array();

      foreach ( $this->_modules as $module ) {
        if ( Registry::get('Payment_' . $module)->isEnabled() ) {
          $active[] = $module;
        }
      }

      return $active;
    }
  }
?>

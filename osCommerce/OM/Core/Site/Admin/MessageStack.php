<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2015 osCommerce; http://www.oscommerce.com
 * @license BSD; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Site\Admin;

use osCommerce\OM\Core\HTML;
use osCommerce\OM\Core\OSCOM;

class MessageStack extends \osCommerce\OM\Core\MessageStack
{
    public function get(string $group = null) : string
    {
        if (empty($group)) {
            $group = OSCOM::getSiteApplication();
        }

        $result = '';

        if ($this->exists($group)) {
            $data = [];

            foreach ($this->_data[$group] as $message) {
                $data['messageStack' . ucfirst($message['type'])][] = $message['text'];
            }

            foreach ($data as $type => $messages) {
                $result .= '<div class="messageStack ' . HTML::outputProtected($type) . '" role="alert">';

                foreach ($messages as $message) {
                    $result .= '<p>' . HTML::outputProtected($message) . '</p>';
                }

                $result .= '</div>';
            }

            unset($this->_data[$group]);
        }

        return $result;
    }
}

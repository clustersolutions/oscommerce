<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright (c) 2016 osCommerce; https://www.oscommerce.com
 * @license BSD; https://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Module;

abstract class EventAbstract
{
    protected $watch = [];

    public function getWatches() {
        return $this->watch;
    }
}

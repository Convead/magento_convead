<?php

/**
 * Main block for module
 *
 * @category   Convead
 * @package    Convead_Tracker
 */
class Convead_Tracker_Block_Tracking extends Mage_Core_Block_Template
{
    public function getCustomerPhone()
    {
        if ($billingAddress = Mage::getSingleton('customer/session')->getCustomer()->getDefaultBillingAddress()) {
            return $billingAddress->getTelephone();
        }

        return false;
    }
}

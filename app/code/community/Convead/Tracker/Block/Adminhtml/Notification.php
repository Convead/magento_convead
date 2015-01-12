<?php

/**
 *
 * @category   Convead
 * @package    Convead_Tracker
 */
class Convead_Tracker_Block_Adminhtml_Notification extends Mage_Core_Block_Template
{
    public function isApiKeyIntroduced()
    {
        if ($this->helper('convead_tracker')->getConveadApiKey()) {
            return true;
        }
        return false;
    }
}
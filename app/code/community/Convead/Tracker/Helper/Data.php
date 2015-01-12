<?php

/**
 * Generic helper for module
 *
 * @category   Convead
 * @package    Convead_Tracker
 */
class Convead_Tracker_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_CONVEAD_ENABLED          = 'sales/convead_tracker/enabled';
    const XML_PATH_CONVEAD_API_KEY          = 'sales/convead_tracker/api_key';

    public function getConveadApiKey()
    {
        return Mage::getStoreConfig(self::XML_PATH_CONVEAD_API_KEY);
    }

    public function isEnabledConveadTracker()
    {
        if (Mage::getStoreConfig(self::XML_PATH_CONVEAD_ENABLED) == 1) {
            return true;
        } else {
            return false;
        }
    }
}

<?php
class Convead_Tracker_Model_Observer
{
    public function onSalesOrderSaveAfter($observer)
    {
        if (!Mage::helper('convead_tracker')->isEnabledConveadTracker() || !Mage::helper('convead_tracker')->getConveadApiKey()) {
            return $this;
        }

        $order = $observer->getOrder();

        try {
            Mage::getModel('convead_tracker/api')->apiOrderSetState($order);
        } catch (Exception $e) {
            Mage::log($e->getMessage());
        }

        return $this;
    }

    public function onAdminOrderCancelAfter($observer)
    {
        if (!Mage::helper('convead_tracker')->isEnabledConveadTracker() || !Mage::helper('convead_tracker')->getConveadApiKey()) {
            return $this;
        }

        $order = $observer->getOrder();

        try {
            Mage::getModel('convead_tracker/api')->apiOrderDelete($order);
        } catch (Exception $e) {
            Mage::log($e->getMessage());
        }

        return $this;
    }

    public function onCheckoutCartProductAddAfter($observer)
    {
        if (!Mage::helper('convead_tracker')->isEnabledConveadTracker() || !Mage::helper('convead_tracker')->getConveadApiKey()) {
            return $this;
        }

        Mage::register('last_added_item', $observer->getQuoteItem());

        return $this;
    }

    public function onCheckoutCartSaveAfter()
    {
        if (!$item = Mage::registry('last_added_item')) {
            return $this;
        }

        if ($parentItemId = $item->getParentItemId()) {
            foreach ($item->getQuote()->getAllItems() as $item) {
                if ($parentItemId == $item->getId()) {
                    break;
                }
            }
        }

        try {
            Mage::getModel('convead_tracker/api')->apiAddToCart($item);
            Mage::unregister('last_added_item');
        } catch (Exception $e) {
            Mage::log($e->getMessage());
        }

        return $this;
    }

    public function onSalesQuoteRemoveItem($observer)
    {
        if (!Mage::helper('convead_tracker')->isEnabledConveadTracker() || !Mage::helper('convead_tracker')->getConveadApiKey()) {
            return $this;
        }

        $item = $observer->getQuoteItem();

        try {
            Mage::getModel('convead_tracker/api')->apiRemoveFromCart($item);
        } catch (Exception $e) {
            Mage::log($e->getMessage());
        }

        return $this;
    }

    public function onCheckoutCartUpdateItemsAfter($observer)
    {
        if (!Mage::helper('convead_tracker')->isEnabledConveadTracker() || !Mage::helper('convead_tracker')->getConveadApiKey()) {
            return $this;
        }

        $items = $observer->getEvent()->getCart()->getQuote()->getAllVisibleItems();

        try {
            Mage::getModel('convead_tracker/api')->apiUpdateCart($items);
        } catch (Exception $e) {
            Mage::log($e->getMessage());
        }

        return $this;
    }

    public function onCheckoutSubmitAllAfter($observer)
    {
        if (!Mage::helper('convead_tracker')->isEnabledConveadTracker() || !Mage::helper('convead_tracker')->getConveadApiKey()) {
            return $this;
        }

        $order = $observer->getEvent()->getOrder();

        try {
            Mage::getModel('convead_tracker/api')->apiPurchase($order);
        } catch (Exception $e) {
            Mage::log($e->getMessage());
        }

        return $this;
    }

    public function onControllerActionPredispatchCheckoutCartAjaxUpdate()
    {
        if (!Mage::helper('convead_tracker')->isEnabledConveadTracker() || !Mage::helper('convead_tracker')->getConveadApiKey()) {
            return $this;
        }

        $items = $cart = Mage::getModel('checkout/cart')->getQuote()->getAllVisibleItems();

        $oldItems = array();
        foreach ($items as $item) {
            $oldItems[$item->getId()] = $item->getQty();
        }
        Mage::register('old_quote_items', $oldItems);

        return $this;
    }

    public function onControllerActionPostdispatchCheckoutCartAjaxUpdate()
    {
        if (!Mage::helper('convead_tracker')->isEnabledConveadTracker() || !Mage::helper('convead_tracker')->getConveadApiKey()) {
            return $this;
        }

        $oldItems = Mage::registry('old_quote_items');

        $items = Mage::getModel('checkout/cart')->getQuote()->getAllVisibleItems();

        foreach ($items as $item) {
            if ($oldItems[$item->getId()] != $item->getQty()) {
                try {
                    Mage::getModel('convead_tracker/api')->apiUpdateCart($items);
                    break;
                } catch (Exception $e) {
                    Mage::log($e->getMessage());
                }
            }
        }

        return $this;
    }
}
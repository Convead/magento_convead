<?php
class Convead_Tracker_Model_Api
{
    protected $_tracker;

    public function __construct()
    {
        require_once 'Convead'.DS.'Tracker'.DS.'lib'.DS.'ConveadTracker.php';

        $this->_initConvead();
    }

    protected function _initConveadAnonym()
    {
        $key = Mage::helper('convead_tracker')->getConveadApiKey();
        $this->_tracker = new ConveadTracker($key);
    }

    protected function _initConvead($order = false)
    {
        $key = Mage::helper('convead_tracker')->getConveadApiKey();
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $guest_uid = (isset($_COOKIE['convead_guest_uid']) ? $_COOKIE['convead_guest_uid'] : false);

        if ($order) {

            if ($order->getBillingAddress()) {
                $visitorInfo = array();
                if ($firstName = $order->getBillingAddress()->getFirstname()) {
                    $visitorInfo['first_name'] = $firstName;
                }
                if ($lastName = $order->getBillingAddress()->getLastname()) {
                    $visitorInfo['last_name'] = $lastName;
                }
                if ($phone = $order->getBillingAddress()->getTelephone()) {
                    $visitorInfo['phone'] = $phone;
                }
                if ($email = $order->getBillingAddress()->getEmail()) {
                    $visitorInfo['email'] = $email;
                }
            }

            $this->_tracker = new ConveadTracker($key, $_SERVER['HTTP_HOST'], $guest_uid,
                (Mage::getSingleton("customer/session")->isLoggedIn() ? $customer->getId() : false), $visitorInfo);
        } else {
            $this->_tracker = new ConveadTracker($key, $_SERVER['HTTP_HOST'], $guest_uid,
                (Mage::getSingleton("customer/session")->isLoggedIn() ? $customer->getId() : false));
        }
    }


    public function apiAddToCart($item)
    {
        $product_id = $item->getProductId();
        $qnt = $item->getQtyToAdd();
        $price = $item->getPrice();
        $product_name = $item->getName();
        $product_url = $item->getProduct()->getProductUrl();

        $this->_tracker->eventAddToCart($product_id, $qnt, $price, $product_name, $product_url);

        return $this;
    }

    public function apiRemoveFromCart($item)
    {
        $product_id = $item->getProductId();
        $qnt = $item->getQty();
        $product_name = $item->getName();
        $product_url = $item->getProduct()->getProductUrl();

        $this->_tracker->eventRemoveFromCart($product_id, $qnt, $product_name, $product_url);

        return $this;
    }

    public function apiUpdateCart($items)
    {

        $order_array = array();
        foreach ($items as $item) {
            $order_array[] = array (
                'product_id'    => $item->getProductId(),
                'qnt'           => $item->getQty(),
                'price'         => $item->getPrice()
            );
        }

        $this->_tracker->eventUpdateCart($order_array);

        return $this;
    }

    public function apiPurchase($order)
    {
        $this->_initConvead($order);
        
        $orderData = $this->getOrderData($order);

        $this->_tracker->eventOrder($orderData->order_id, $orderData->revenue, $orderData->items, $orderData->state);

        return $this;
    }

    public function apiOrderSetState($order)
    {
        $this->_initConveadAnonym();
 
        $orderData = $this->getOrderData($order);

        if (!$orderData->state) return false;

        $this->_tracker->webHookOrderUpdate($orderData->order_id, $orderData->state, $orderData->revenue, $orderData->items);

        return $this;
    }
    
    public function apiOrderDelete($order)
    {
        $this->_initConveadAnonym();
 
        $order_id = $order->getIncrementId();
        
        $this->_tracker->webHookOrderUpdate($order_id, 'cancelled');

        return $this;
    }

    private function getOrderData($order)
    {
        $orderData = new stdClass();

        $items = $order->getAllVisibleItems();
        $order_array = array();
        foreach ($items as $item) {
            $order_array[] = array (
                'product_id'    => $item->getProductId(),
                'qnt'           => $item->getQtyOrdered(),
                'price'         => $item->getPrice()
            );
        }

        $orderData->order_id = $order->getIncrementId();
        $orderData->revenue = $order->getGrandTotal();
        $orderData->items = $order_array;
        $orderData->state = $this->switchState( $order->getState() );

        return $orderData;
    }

    private function switchState($state) {
        switch ($state) {
          case 'processing':
            $state = 'new';
            break;
          case 'payment_review':
            $state = 'paid';
            break;
          case 'complete':
            $state = 'shipped';
            break;
          case 'cancelled':
            $state = 'cancelled';
            break;
          case 'closed':
            $state = 'cancelled';
            break;
        }
        return $state;
    }

}
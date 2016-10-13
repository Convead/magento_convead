<?php
class Convead_Tracker_Model_Api
{
    protected $_convead;
    protected $_api;

    public function __construct()
    {
        require_once 'Convead'.DS.'Tracker'.DS.'lib'.DS.'ConveadTracker.php';

        $this->_initConvead();
    }

    protected function _initApi()
    {
        $key = Mage::helper('convead_tracker')->getConveadApiKey();
        $this->_api = new ConveadApi($key);
    }

    protected function _initConvead($order = false)
    {
        $key = Mage::helper('convead_tracker')->getConveadApiKey();
        $customer = Mage::getSingleton('customer/session')->getCustomer();

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

            $this->_convead = new ConveadTracker($key, $_SERVER['HTTP_HOST'], $_COOKIE['convead_guest_uid'],
                (Mage::getSingleton("customer/session")->isLoggedIn() ? $customer->getId() : false), $visitorInfo);
        } else {
            $this->_convead = new ConveadTracker($key, $_SERVER['HTTP_HOST'], $_COOKIE['convead_guest_uid'],
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

        $this->_convead->eventAddToCart($product_id, $qnt, $price, $product_name, $product_url);

        return $this;
    }

    public function apiRemoveFromCart($item)
    {
        $product_id = $item->getProductId();
        $qnt = $item->getQty();
        $product_name = $item->getName();
        $product_url = $item->getProduct()->getProductUrl();

        $this->_convead->eventRemoveFromCart($product_id, $qnt, $product_name, $product_url);

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

        $this->_convead->eventUpdateCart($order_array);

        return $this;
    }

    public function apiPurchase($order)
    {
        $this->_initConvead($order);
        $order_id = $order->getIncrementId();
        $revenue = $order->getGrandTotal();

        $items = $order->getAllVisibleItems();
        $order_array = array();
        foreach ($items as $item) {
            $order_array[] = array (
                'product_id'    => $item->getProductId(),
                'qnt'           => $item->getQtyOrdered(),
                'price'         => $item->getPrice()
            );
        }

        $this->_convead->eventOrder($order_id, $revenue, $order_array);

        return $this;
    }

    public function apiOrderSetState($order)
    {
        $this->_initApi();
 
        $order_id = $order->getIncrementId();
        $state = $order->getState();

        if ($state == 'canceled') $this->_api->orderDelete($order_id);
        else {
            if ($state != '') $this->_api->orderSetState($order_id, $state);
        }

        return $this;
    }
}
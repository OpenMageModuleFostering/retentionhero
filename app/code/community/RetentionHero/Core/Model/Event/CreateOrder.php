<?php

/*
 * Copyright (c) 2014 Retention Hero
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class RetentionHero_Core_Model_Event_CreateOrder extends RetentionHero_Core_Model_Event_Abstract
{
    public function setOrder($order)
    {
      $this->_order = $order;
    }
    public function getEventData()
    {
      return $this->setUpData($this->_order);
    }

    public function getApiKey()
    {
      return $this->_apiKey;
    }

    protected function setUpData($order)
    {
      $data = array();

      $data['store_id'] = Mage::app()->getStore()->getStoreId();
      $data['increment_id'] = $order->getIncrementId();
      $data['created_at'] = $order->getCreatedAt();

      if ( Mage::getSingleton('customer/session')->isLoggedIn() ) {
        $data['customer_id'] = Mage::getSingleton('customer/session')->getCustomer()->getId();
      }
      if ($order->getCustomerIsGuest()) {
        $data['customer_firstname'] = $order->getBillingAddress()->getFirstname();
        $data['customer_lastname'] = $order->getBillingAddress()->getLastname();
      } else {
        $data['customer_firstname'] = $order->getCustomerFirstname();
        $data['customer_lastname'] = $order->getCustomerLastname();
      }
      $data['customer_email']  = $order->getCustomerEmail();
      $data['base_grand_total'] = $order->getBaseGrandTotal();
      $data['base_subtotal'] = $order->getBaseSubtotal();
      $data['discount_amount'] = $order->getDiscountAmount();
      $data['order_currency_code'] = $order->getOrderCurrencyCode();

      $billing_address = $order->getBillingAddress();
      if ( !empty( $billing_address ) ) {
        $data['billing_address'] = $this->_parseAddress( $billing_address );
      }

      $shipping_address = $order->getShippingAddress();
      if( !empty( $shipping_address ) ) {
        $data['shipping_address'] = $this->_parseAddress( $shipping_address );
      }

      $data['items'] = array();

      $items = $order->getAllVisibleItems();
      foreach ($items as $item) {
        $data['items'][] = $this->_parseItem( $item );
      }

      return $data;
    }

    private function _parseItem($item)
    {
      $rhItem = array();
      $rhItem['item_id'] = $item->getId();
      $rhItem['product_id'] = $item->getProductId();
      $rhItem['base_price'] = $item->getBasePrice();
      $rhItem['name'] = $item->getName();
      $rhItem['sku'] = $item->getSku();
      $rhItem['qty_ordered'] = $item->getQtyOrdered();
      return $rhItem;
    }
}

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
 *
 *
 * Extending Model_Abstract is not necessary, but helpful for testing.
 */

class RetentionHero_Core_Model_Observer extends Mage_Core_Model_Abstract
{

  public function front_after_hook($observer)
  {
    $dispatcher = Mage::getSingleton('retentionhero_core/dispatcher');
    $dispatcher->fireAll();
    return $observer;
  }

   /**
   * Triggers the RetentionHero Event_CreateOrder model to fire.
   * @param  array $observer array( 'order'=>$order, 'quote'=>$quote )
   * @return array $observer
   */
  public function order_success_hook($observer)
  {
    $order = $observer->getEvent()->getOrder();

    // $order->setRetentionhero_customerid($user_id);

    // Fire event after updating user id
    Mage::log( '[RH] order_success_hook firing');
    $createOrderEvent = Mage::getSingleton('retentionhero_core/event_createOrder');
    $createOrderEvent->setOrder($order);
    $createOrderEvent->dispatch();

    // $order->save();

    return $observer;
  }
}

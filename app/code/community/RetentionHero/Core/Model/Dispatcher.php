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

class RetentionHero_Core_Model_Dispatcher extends Mage_Core_Model_Abstract
{

  protected $_apiKey;

  public function _construct()
  {
    parent::_construct();
    $this->_apiKey = Mage::getStoreConfig('retentionhero_options/general/rest_api_key');
  }
  
  public function getApiKey()
  {
    return $this->_apiKey;
  }
  public function dispatch($event)
  {
    Mage::log('[RH] Dispatching event');

    $events = $this->getPendingEvents();
    $events[] = $event;
    $this->setPendingEvents($events);
    return true;
  }

  public function fireEvent($event)
  {
    if ( Mage::getStoreConfig('retentionhero_options/general/enable') != true ) {
      Mage::log('[RH_WARNING] Retention Hero is disabled. Not firing events');
      return false;
    }

    $eventData = $event->getEventData();
    Mage::log( '[RH] Firing event data:' . var_export($eventData,TRUE) );
    $this->fireCurl( $eventData );
  }

  public function fireAll()
  {
    $events = $this->getPendingEvents();
    // Mage::log( '[RH] Pending events: ' . var_export($events, TRUE) );
    if( empty($events) ) { return false; }
    foreach ($events as $event) {
      $this->fireEvent( $event );
    }

    $this->setPendingEvents( array() );

    return true;
  }

  protected function fireCurl($data)
  {
    $data_string = json_encode($data);
    $api_key = $this->getApiKey();

    $ch = curl_init('https://app.retentionhero.com/api/v1/orders/');

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); // 2-second timeout
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Token ' . $api_key,
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
    );

    $result = curl_exec($ch);
    Mage::log( '[RH] Curl response: ' . var_export(json_decode($result), TRUE) );
  }
}

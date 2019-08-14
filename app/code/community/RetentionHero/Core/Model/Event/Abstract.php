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

abstract class RetentionHero_Core_Model_Event_Abstract extends Mage_Core_Model_Abstract
{
  abstract protected function getEventData();

  protected $_dispatched = false;

  protected $_apiKey;

  public function _construct()
  {
    parent::_construct();
    $this->_apiKey = Mage::getStoreConfig('retentionhero_options/general/rest_api_key');
  }
  public function dispatch()
  {
    $dispatcher = Mage::getSingleton('retentionhero_core/dispatcher');
    $this->_dispatched = $dispatcher->dispatch($this);
  }

  public function isDispatched()
  {
    return $this->_dispatched ? true : false;
  }

  protected function _parseAddress($address)
  {
    $rhAddress = array();

    $rhAddress['firstname'] = $address->getFirstname();
    $rhAddress['middlename'] = $address->getMiddlename();
    $rhAddress['lastname'] = $address->getLastname();
    $rhAddress['telephone'] = $address->getTelephone();
    $rhAddress['company'] = $address->getCompany();

    $streets = $address->getStreet();
    $rhAddress['address1'] = $streets[0];
    if ( sizeof( $streets ) > 1 ) {
      $rhAddress['address2'] = $streets[1];
    }

    $rhAddress['city'] = $address->getCity();
    $rhAddress['region'] = $address->getRegion();
    $rhAddress['country'] = $address->getCountry();
    $rhAddress['postcode'] = $address->getPostcode();
    $rhAddress['country_id'] = $address->getCountry();

    return $rhAddress;
  }
}

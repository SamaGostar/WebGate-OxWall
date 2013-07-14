<?php

/**
 * Copyright (c) 2013, zarinpal.com.
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ or http://www.oxwall.su/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

$billingService = BOL_BillingService::getInstance();

$gateway = new BOL_BillingGateway();
$gateway->gatewayKey = 'billingsprypay';
$gateway->adapterClassName = 'BILLINGSPRYPAY_CLASS_SprypayAdapter';
$gateway->active = 0;
$gateway->mobile = 0;
$gateway->recurring = 1;
$gateway->currencies = 'USD,RUB';

$billingService->addGateway($gateway);

$billingService->addConfig('billingsprypay', 'secCode', '');
$billingService->addConfig('billingsprypay', 'spShopId', '');
$billingService->addConfig('billingsprypay', 'spCurrency', 'rur');
$billingService->addConfig('billingsprypay', 'lang', 'ru');
$billingService->addConfig('billingsprypay', 'tabNum', '1');

OW::getPluginManager()->addPluginSettingsRouteName('billingsprypay', 'billing_sprypay_admin');

$path = OW::getPluginManager()->getPlugin('billingsprypay')->getRootDir() . 'langs.zip';
OW::getLanguage()->importPluginLangs($path, 'billingsprypay');

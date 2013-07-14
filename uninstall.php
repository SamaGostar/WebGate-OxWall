<?php

/**
 * Copyright (c) 2013, zarinpal.com.
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ or http://www.oxwall.su/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

$billingService = BOL_BillingService::getInstance();


$billingService->deleteConfig('billingsprypay', 'secCode');
$billingService->deleteConfig('billingsprypay', 'spShopId');
$billingService->deleteConfig('billingsprypay', 'spCurrency');
$billingService->deleteConfig('billingsprypay', 'lang');
$billingService->deleteConfig('billingsprypay', 'tabNum');

$billingService->deleteGateway('billingsprypay');

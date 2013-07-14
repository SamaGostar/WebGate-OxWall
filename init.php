<?php

/**
 * Copyright (c) 2013, zarinpal.com.
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ or http://www.oxwall.su/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */
OW::getRouter()->addRoute(new OW_Route('billing_sprypay_order_form', 'billing-sprypay/order', 'BILLINGSPRYPAY_CTRL_Order', 'form'));
OW::getRouter()->addRoute(new OW_Route('billing_sprypay_notify', 'billing-sprypay/order/notify', 'BILLINGSPRYPAY_CTRL_Order', 'notify'));
OW::getRouter()->addRoute(new OW_Route('billing_sprypay_completed', 'billing-sprypay/order/completed/', 'BILLINGSPRYPAY_CTRL_Order', 'completed'));
OW::getRouter()->addRoute(new OW_Route('billing_sprypay_canceled', 'billing-sprypay/order/canceled/', 'BILLINGSPRYPAY_CTRL_Order', 'canceled'));
OW::getRouter()->addRoute(new OW_Route('billing_sprypay_admin', 'admin/billing-sprypay', 'BILLINGSPRYPAY_CTRL_Admin', 'index'));

function sprypay_add_admin_notification( BASE_CLASS_EventCollector $coll )
{
    $billingService = BOL_BillingService::getInstance();

    if ( !mb_strlen($billingService->getGatewayConfigValue(BILLINGSPRYPAY_CLASS_SprypayAdapter::GATEWAY_KEY, 'seccode')) &&
    !mb_strlen($billingService->getGatewayConfigValue(BILLINGSPRYPAY_CLASS_SprypayAdapter::GATEWAY_KEY, 'spShopId'))   ){
        $coll->add(
            OW::getLanguage()->text(
                'billingsprypay', 
                'plugin_configuration_notice', 
                array('url' => OW::getRouter()->urlForRoute('billing_sprypay_admin'))
            )
        );
    }
}

OW::getEventManager()->bind('admin.add_admin_notification', 'sprypay_add_admin_notification');

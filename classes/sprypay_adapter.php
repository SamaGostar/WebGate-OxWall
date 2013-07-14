<?php

/* No direct access */
defined('_OW_') or die('Restricted access');

/**
 * Copyright (c) 2013, zarinpal.com.
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ or http://www.oxwall.su/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Zarinpal admin controller
 *
 * @author Zarinpal developement team <info@zarinpal.com>
 * @package ow.ow_plugins.billing_Zarinpal.controllers
 * @since 1.0
 */
class BILLINGSPRYPAY_CLASS_SprypayAdapter implements OW_BillingAdapter
{
    const GATEWAY_KEY = 'billingsprypay';

    /**
     * @var BOL_BillingService
     */
    private $billingService;

    public function __construct()
    {
        $this->billingService = BOL_BillingService::getInstance();
    }

    public function prepareSale( BOL_BillingSale $sale )
    {
        // ... gateway custom manipulations

        return $this->billingService->saveSale($sale);
    }

    public function verifySale( BOL_BillingSale $sale )
    {
        // ... gateway custom manipulations

        return $this->billingService->saveSale($sale);
    }

    /**
     * (non-PHPdoc)
     * @see ow_core/OW_BillingAdapter#getFields($params)
     */
    public function getFields( $params = null )
    {
        $router = OW::getRouter();

        return array(
            'seccode' => $this->billingService->getGatewayConfigValue(self::GATEWAY_KEY, 'seccode'),
            'spShopId' => $this->billingService->getGatewayConfigValue(self::GATEWAY_KEY, 'spShopId'),
			'spCurrency' => $this->billingService->getGatewayConfigValue(self::GATEWAY_KEY, 'spCurrency'),
			'lang' => $this->billingService->getGatewayConfigValue(self::GATEWAY_KEY, 'lang'),
			'tabNum' => $this->billingService->getGatewayConfigValue(self::GATEWAY_KEY, 'tabNum '),
            'formActionUrl' => $this->getOrderFormActionUrl()
        );
    }

    /**
     * (non-PHPdoc)
     * @see ow_core/OW_BillingAdapter#getOrderFormUrl()
     */
    public function getOrderFormUrl()
    {
        return OW::getRouter()->urlForRoute('billing_sprypay_order_form');
    }

    /**
     * (non-PHPdoc)
     * @see ow_core/OW_BillingAdapter#getLogoUrl()
     */
    public function getLogoUrl()
    {
        $plugin = OW::getPluginManager()->getPlugin('billingsprypay');

        return $plugin->getStaticUrl() . 'img/sprypay_logo.gif';
    }

    /**
     *
     * @return string
     */
    private function getOrderFormActionUrl()
    {
        return null;
    }

    /**
     *  
     * @param array $post
     * @return boolean
     */
    public function isVerified()
    {
        return null;
    }
}

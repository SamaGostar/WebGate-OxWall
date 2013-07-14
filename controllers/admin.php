<?php

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
class BILLINGSPRYPAY_CTRL_Admin extends ADMIN_CTRL_Abstract
{

    public function index()
    {
        $billingService = BOL_BillingService::getInstance();
        $language = OW::getLanguage();

        $sprypayConfigForm = new SpryPayConfigForm();
        $this->addForm($sprypayConfigForm);

        if ( OW::getRequest()->isPost() && $sprypayConfigForm->isValid($_POST) )
        {
            $res = $sprypayConfigForm->process();
            OW::getFeedback()->info($language->text('billingsprypay', 'settings_updated'));
            $this->redirect();
        }

        $adapter = new BILLINGSPRYPAY_CLASS_SprypayAdapter();
        $this->assign('logoUrl', $adapter->getLogoUrl());

        $gateway = $billingService->findGatewayByKey(BILLINGSPRYPAY_CLASS_SprypayAdapter::GATEWAY_KEY);
        $this->assign('gateway', $gateway);

        $this->assign('activeCurrency', $billingService->getActiveCurrency());

        $supported = $billingService->currencyIsSupported($gateway->currencies);
        $this->assign('currSupported', $supported);

        $this->setPageHeading(OW::getLanguage()->text('billingsprypay', 'config_page_heading'));
        $this->setPageHeadingIconClass('ow_ic_app');
    }
}

class SpryPayConfigForm extends Form
{

    public function __construct()
    {
        parent::__construct('sprypay-config-form');

        $language = OW::getLanguage();
        $billingService = BOL_BillingService::getInstance();
        $gwKey = BILLINGSPRYPAY_CLASS_SprypayAdapter::GATEWAY_KEY;

        $element = new TextField('seccode');
        $element->setValue($billingService->getGatewayConfigValue($gwKey, 'seccode'));
        $this->addElement($element);

        $element = new Selectbox('spShopId');
        $element
            ->setValue($billingService->getGatewayConfigValue($gwKey, 'spShopId'))
            ->setHasInvitation(false)
            ->addOption('0', '0');
        $this->addElement($element);

        $element = new Selectbox('spCurrency');
        $element
            ->setValue($billingService->getGatewayConfigValue($gwKey, 'spCurrency'))
            ->setHasInvitation(false)
            ->addOption('usd', 'IRR');
        $this->addElement($element);

        $element = new Selectbox('lang');
        $element
            ->setValue($billingService->getGatewayConfigValue($gwKey, 'lang'))
            ->setHasInvitation(false)
            ->addOption('en', 'English');
        $this->addElement($element);

        $element = new Selectbox('tabNum');
        $element
            ->setValue($billingService->getGatewayConfigValue($gwKey, 'tabNum'))
            ->setHasInvitation(false)
            ->addOption('1', 'Default');
        $this->addElement($element);

        // submit
        $submit = new Submit('save');
        $submit->setValue($language->text('billingsprypay', 'btn_save'));
        $this->addElement($submit);
    }

    public function process()
    {
        $values = $this->getValues();

        $billingService = BOL_BillingService::getInstance();
        $gwKey = BILLINGSPRYPAY_CLASS_SprypayAdapter::GATEWAY_KEY;

        $billingService->setGatewayConfigValue($gwKey, 'seccode', $values['seccode']);
        $billingService->setGatewayConfigValue($gwKey, 'spShopId', $values['spShopId']);
        $billingService->setGatewayConfigValue($gwKey, 'spCurrency', $values['spCurrency']);
        $billingService->setGatewayConfigValue($gwKey, 'lang', $values['lang']);
        $billingService->setGatewayConfigValue($gwKey, 'tabNum', $values['tabNum']);
    }
}

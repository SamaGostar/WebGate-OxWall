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
class BILLINGSPRYPAY_CTRL_Order extends OW_ActionController
{
    public function send($desc,$merchent,$amount,$redirect){
	$client = new SoapClient('https://de.zarinpal.com/pg/services/WebGate/wsdl', array('encoding'=>'UTF-8'));
	$res = $client->PaymentRequest(
	array(
					'MerchantID' 		=> $merchent ,
					'Amount' 		=> $amount ,
					'Description' 		=> $desc ,
					'Email' 		=> '' ,
					'Mobile' 		=> '' ,
					'CallbackURL' 		=> $redirect

					)
	 );
    return $res;
	}
	
    public function get($merchent,$au,$amount){
	$client = new SoapClient('https://de.zarinpal.com/pg/services/WebGate/wsdl', array('encoding'=>'UTF-8'));
	$res = $client->PaymentVerification(
			array(
					'MerchantID'	 => $merchent ,
					'Authority' 	 => $au ,
					'Amount'	=> $amount
				)
		);
        return $res;
    }     

    public function form()
    {
        $billingService = BOL_BillingService::getInstance();
        $adapter = new BILLINGSPRYPAY_CLASS_SprypayAdapter();
        $lang = OW::getLanguage();

        $sale = $billingService->getSessionSale();
        if ( !$sale )
        {
            $url = $billingService->getSessionBackUrl();
            if ( $url != null )
            {
                OW::getFeedback()->warning($lang->text('base', 'billing_order_canceled'));
                $billingService->unsetSessionBackUrl();
                $this->redirect($url);
            }
            else
            {
                $this->redirect($billingService->getOrderFailedPageUrl());
            }
        }

        $formId = uniqid('order_form-');
        $this->assign('formId', $formId);

        $js = '$("#' . $formId . '").submit()';
        OW::getDocument()->addOnloadScript($js);

        $fields = $adapter->getFields();
        $this->assign('fields', $fields);
        $this->assign('email', OW::getUser()->getEmail());

        $desc = $this->assign('email', OW::getUser()->getEmail());
        $merchent = $fields['seccode'];
        $amount = (int)$sale->totalAmount;
        $redirect = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'/notify');
        $result = $this->send($desc,$merchent,$amount,$redirect);
        if($result->Status == 100 ){
            $url = "https://www.zarinpal.com/pg/StartPay/" . $result->Authority . "/";
            $this->redirect($fields['formActionUrl']);
            die();
        }else{
        	echo'ERR: '.$result->Status;
        }

        if ( $billingService->prepareSale($adapter, $sale) )
        {
            $sale->totalAmount = floatval($sale->totalAmount);
            $this->assign('sale', $sale);

            $masterPageFileDir = OW::getThemeManager()->getMasterPageTemplate('blank');
            OW::getDocument()->getMasterPage()->setTemplate($masterPageFileDir);
            $billingService->unsetSessionSale();
        }
        else
        {
            $productAdapter = $billingService->getProductAdapter($sale->entityKey);

            if ( $productAdapter )
            {
                $productUrl = $productAdapter->getProductOrderUrl();
            }

            OW::getFeedback()->warning($lang->text('base', 'billing_order_init_failed'));
            $url = isset($productUrl) ? $productUrl : $billingService->getOrderFailedPageUrl();
            $this->redirect($url);
        }
    }

    public function notify()
    {
        if (!isset($_GET['Authority']) )
        {
            exit;
        }
        //$refid = $_GET['refid'];
        $au = $_GET['Authority'];

        $status = 'COMPLETED';

        $billingService = BOL_BillingService::getInstance();
        $sale = $billingService->getSessionSale();
        $adapter = new BILLINGSPRYPAY_CLASS_SprypayAdapter();
        $fields = $adapter->getFields();
        $merchent = $fields['seccode'];
        $amount = $sale->totalAmount;
        $result = $this->get($merchent,$au,$amount);
        if ( $result->Status == 100 )
        {

            if ( $status == 'COMPLETED' )
            {
                if ( !$billingService->saleDelivered($refid, $sale->gatewayId) )
                {
                    $sale->transactionUid = $refid;

                    if ( $billingService->verifySale($adapter, $sale) )
                    {
                        $sale = $billingService->getSaleById($sale->id);
                                
                        $productAdapter = $billingService->getProductAdapter($sale->entityKey);

                        if ( $productAdapter )
                        {
                            $billingService->deliverSale($productAdapter, $sale);
                            $this->completed();
							die('OK');
                        }
                    }
					die;
                } else {
                    $this->completed();
					die('OK');
			    }
            }
            $this->canceled();
			die;
        }
        else
        {
        	echo'ERR:'.$result->Status;
       	    $this->canceled();
            exit;
        }
    }

    public function completed()
    {
        $hash = $_REQUEST['spUserDataHash'];

        $this->redirect(BOL_BillingService::getInstance()->getOrderCompletedPageUrl($hash));
    }
    
    public function canceled()
    {
        $this->redirect(BOL_BillingService::getInstance()->getOrderCancelledPageUrl());
    }
}

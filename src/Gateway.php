<?php
namespace Alvarofelipems\PayU;

use Omnipay\Common\AbstractGateway;
use Omnipay\PayU\Message\PurchaseResponse;

/**

 * @method \Omnipay\Common\Message\ResponseInterface completeAuthorize(array $options = array())
 * @method \Omnipay\Common\Message\ResponseInterface completePurchase(array $options = array())
 * @method \Omnipay\Common\Message\ResponseInterface void(array $options = array())
 * @method \Omnipay\Common\Message\ResponseInterface createCard(array $options = array())
 * @method \Omnipay\Common\Message\ResponseInterface updateCard(array $options = array())
 * @method \Omnipay\Common\Message\ResponseInterface deleteCard(array $options = array())
 */
class Gateway extends AbstractGateway
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'PayU';
    }

    /**
     * @return array
     */
    public function getDefaultParameters()
    {
        return array(
            'merchantId' => '',
            'testMode' => true,
            'language' => 'es',
            'currence' => 'BRL',
        );
    }
    
    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->getParameter('language');
    }
    
    /**
     * @param $value
     * @return mixed
     */
    public function setLanguage($value)
    {
        return $this->setParameter('language', $value);
    }
    
    /**
     * @return mixed
     */
    public function setMerchantId($id)
    {
        return $this->setParameter('merchantId', $id);
    }
    
    /**
     * @return mixed
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }
    
    /**
     * @return mixed
     */
    public function setAccountId($id)
    {
        return $this->setParameter('accountId', $id);
    }
    
    /**
     * @return mixed
     */
    public function getAccountId()
    {
        return $this->getParameter('accountId');
    }
    
    /**
     * @param $value
     * @return mixed
     */
    public function setApiLogin($apiLogin)
    {
        return $this->setParameter('apiLogin', $apiLogin);
    }
    
    /**
     * @return mixed
     */
    public function getApiLogin()
    {
        return $this->getParameter('apiLogin');
    }
    
    /**
     * @param $value
     * @return mixed
     */
    public function setApiKey($apiKey)
    {
        return $this->setParameter('apiKey', $apiKey);
    }
    
    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }
    
    /**
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function authorize(array $parameters = array()){
        return $this->createRequest('\Omnipay\PayU\Message\AuthorizeRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function capture(array $parameters = array()){
        return $this->createRequest('\Omnipay\PayU\Message\CaptureRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return PurchaseResponse
     */
    public function purchase(array $parameters = array())
    {
        $parameters = array_merge($this->getParameters(), $parameters);
        return $this->createRequest('\Omnipay\PayU\Message\PurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function refund(array $parameters = array()){
        return $this->createRequest('\Omnipay\PayU\Message\RefundRequest', $parameters);
    }

    function __call($name, $arguments)
    {
        // TODO: Implement @method \Omnipay\Common\Message\ResponseInterface completeAuthorize(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\ResponseInterface completePurchase(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\ResponseInterface void(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\ResponseInterface createCard(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\ResponseInterface updateCard(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\ResponseInterface deleteCard(array $options = array())
    }
}
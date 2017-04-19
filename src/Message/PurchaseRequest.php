<?php
namespace Omnipay\PayU\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Common\Item;
use Omnipay\Common\Message\AbstractRequest;
use Symfony\Component\HttpFoundation\Response;
use Guzzle\Http\Exception\ServerErrorResponseException;
/**
 * PayU Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    /**
     * @var array
     */
    protected $endpoints = array(
        'authorize' => 'https://api.payulatam.com/payments-api/4.0/service.cgi',
        'purchase' => 'https://api.payulatam.com/payments-api/4.0/service.cgi',
        'test' => 'https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi',
    );
    
    /**
     * @var array
     */
    protected $identityTypes = array(
        'NCZ',
        'PSPRT',
        'EHLIYET',
    );
    
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
     * @param $value
     * @return mixed
     */
    public function setMerchant($id, $login, $key)
    {
        $this->setParameter('merchantId', $id);
        $this->setParameter('apiLogin', $login);
        $this->setParameter('apiKey', $key);
    }
    
    /**
     * @return mixed
     */
    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }
    
    
    /**
     * @param $value
     * @return mixed
     */
    public function setDniNumber($dniNumber)
    {
        return $this->setParameter('dniNumber', $dniNumber);
    }
    
    /**
     * @return mixed
     */
    public function getDniNumber()
    {
        return $this->getParameter('dniNumber');
    }
    
    /**
     * @param $endpoint
     * @return mixed
     */
    public function getEndpoint($endpoint)
    {
        return $this->getTestMode() ? $this->endpoints['test'] : $this->endpoints[$endpoint];
    }

    /**
     * @return mixed
     */
    public function getIdentityNumber() {
        return $this->getParameter('identityNumber');
    }

    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setIdentityNumber($value) {
        return $this->setParameter('identityNumber',$value);
    }

    /**
     * @return mixed
     */
    public function getTransactionDescription() {
        return $this->getParameter('transactionDescription');
    }

    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setTransactionDescription($transactionDescription) {
        return $this->setParameter('transactionDescription', $transactionDescription);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getIdentityType(){
        $type = $this->getParameter('identityType');
        if(!in_array($type,$this->identityTypes)){
            throw new \Exception("Invalid identity type ($type). Available types: ". implode(',',$this->identityTypes));
        }
        return $type;
    }

    /**
     * @param $value
     */
    public function setIdentityType($value){
        $this->setParameter('identityType',$value);
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getInstallment(){
        $installment = $this->getParameter('installment');
        if(false == $installment){
            return 1;
        }
        if( $installment <1 || $installment >12){
            throw new \Exception('Invalid installment number');
        }
        return $installment;
    }

    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setInstallment($value){
        return $this->setParameter('installment',$value);
    }
    /**
     * The date when the order is initiated in the system, in YYYY-MM-DD HH:MM:SS format (e.g.: "2012-05-01 21:15:45")
     * Important: Date should be UTC standard +/-10 minutes
     * @return mixed
     */
    public function getOrderDate(){
        return $this->getParameter('orderDate');
    }
    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setOrderDate($value){
        return $this->setParameter('orderDate',$value);
    }

    /**
     * @return mixed
     */
    public function getOrderVat(){
        return $this->getParameter('orderVat'); // todo : cana sor
    }

    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setOrderVat($value){
        return $this->setParameter('orderVat',$value);
    }

    /**
     * @return mixed
     */
    public function getOrderPriceType(){
        return $this->getParameter('orderPriceType'); // todo : cana sor
    }
    /**
     * @param $value
     * @return AbstractRequest
     */
    public function setOrderPriceType($value){
        return $this->setParameter('orderPriceType',$value);
    }
    /**
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidCreditCardException
     */
    public function getData()
    {
        $card = $this->getCard();
        
        //Identificar bandeira
        foreach ($card->getSupportedBrands() as $brand => $patern) {
            if (preg_match ($patern, $card->getNumber())) {
                $this->setPaymentMethod(strtoupper($brand));
                break;
            }
        }
        
        $data = [
            'language' => $this->getParameter('language'),
            'command' => "SUBMIT_TRANSACTION",
            'merchant' => [
                'apiKey' => $this->getParameter('apiKey'),
                'apiLogin' => $this->getParameter('apiLogin')
            ],
            'transaction' => [
                'order' => [
                    'accountId' => $this->getAccountId(),
                    'referenceCode' => $this->getTransactionReference(),
                    'description' => $this->getTransactionDescription(),
                    'language' => $this->getParameter('language'),
                    'signature' => 'AssinaturaMD5',
                    'notifyUrl' => $this->getNotifyUrl(),
                    'additionalValues' => [
                        'TX_VALUE' => [
                            'value' => $this->getAmount(),
                            'currency' => $this->getCurrency(),
                        ]
                    ],
                ],
                //'payer' => [],
                "extraParameters" => [
                     "INSTALLMENTS_NUMBER" => $this->getInstallment()
                ],
                'type' => 'AUTHORIZATION_AND_CAPTURE',
                'paymentMethod' => $this->getPaymentMethod(),
                'paymentCountry' => 'BR',
                'ipAddress' => $this->getClientIp(),
            ],
            'test' => $this->getTestMode(),
        ];
        
        /** @var CreditCard $card */
        // Billing Details
        $data['transaction']['order']['buyer'] = [
            'merchantBuyerId' => $this->getIdentityNumber(),
            'fullName' => $card->getName(),
            'emailAddress' => $card->getEmail(),
            'contactPhone' => $card->getBillingPhone(),
            'dniNumber' => $this->getParameter('dniNumber'),
            "shippingAddress" => [
               "street1" => $card->getBillingAddress1(),
               "street2" => $card->getBillingAddress2(),
               "city" => $card->getBillingCity(),
               "state" => $card->getBillingState(),
               "country" => $card->getBillingCountry(),
               "postalCode" => $card->getBillingPostcode(),
               "phone" => $card->getBillingPhone()
            ]
        ];

        // Card Details
        // If card is not valid then throw InvalidCreditCardException.
        $card->validate();

        $data['transaction']['creditCard'] = [
             "number" => $card->getNumber(),
             "securityCode" => $card->getCvv(),
             "expirationDate" => $card->getExpiryYear().'/'.str_pad($card->getExpiryMonth(), 2, "0", STR_PAD_LEFT),
             "name" => $card->getName()
        ];
        
        /*
        $data['BILL_COUNTRYCODE'] = $card->getBillingCountry();
        $data['BILL_CITYPE'] = $this->getIdentityType();
        $data['BILL_CINUMBER'] = $this->getIdentityNumber();
        
        $data['ORDER_DATE'] = $this->getOrderDate();
        $data['BACK_REF'] = $this->getReturnUrl();
        $data['ORDER_TIMEOUT'] = 1000;
        */
        // Product Details
        /*$items = $this->getItems();
        if( !empty($items)){
            foreach ($this->getItems() as $key => $item) {
                $data['ORDER_PNAME[' . $key . ']'] = $item->getName();
                $data['ORDER_PCODE[' . $key . ']'] = $item->getName();
                $data['ORDER_PINFO[' . $key . ']'] = $item->getName();
                $data['ORDER_PRICE[' . $key . ']'] = $item->getPrice();
                $data['ORDER_VAT[' . $key . ']'] = $this->getOrderVat();
                $data['ORDER_PRICE_TYPE[' . $key . ']'] = $this->getParameter('orderPriceType'); // todo : cana sor
                $data['ORDER_QTY[' . $key . ']'] = $item->getQuantity();
            }
        }*/

        // Other Details
        // Order Hash
        //$data["ORDER_HASH"] = $this->generateHash($data);
        //$this->printCurlOutput($data);
        return $data;
    }

    /**
     * @param $data
     * @return Response
     */
    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }
    /**
     * @param $data
     * @return PurchaseResponse
     */
    public function sendData($data)
    {
        $data['transaction']['order']['signature'] = $this->generateHash($data);
        
        $httpRequest = $this->httpClient->post(
            $this->getEndpoint('test'),
            [
                'Content-Type' => 'application/json; charset=utf-8',
                'Accept' => 'application/json',
                'Content-Length' => 'length'
            ]
        );
        
        $httpRequest->setBody(json_encode($data));
        
        //$httpRequest->getCurlOptions()->set(CURLOPT_SSLVERSION, 6); // CURL_SSLVERSION_TLSv1_2 for libcurl < 7.35
        $response = $httpRequest->send();
        $jsonData = $response->json();
        
        $data = array();
        foreach($jsonData as $key => $value){
            $data[$key] = empty($value) ? null: $value;
        }
        return new PurchaseResponse($this,$data);
    }

    /**
     * HMAC_MD5 signature applied on all parameters from the request.
     * Source string for HMAC_MD5 will be calculated by adding the length
     * of each field value at the beginning of field value. A common key
     * shared between PayU and the merchant is used for the signature.
     * Find more details on how is HASH generated https://secure.payu.com.tr/docs/alu/v3/#hash
     * @param array $data
     * @return string
     */
    public function generateHash(array $data)
    {
        return md5(
            $this->getParameter('apiKey').'~'.
            $this->getParameter('merchantId').'~'.
            $data['transaction']['order']['referenceCode'].'~'.
            $data['transaction']['order']['additionalValues']['TX_VALUE']['value'].'~'.
            $data['transaction']['order']['additionalValues']['TX_VALUE']['currency']
        );
        
        if ($this->getSecretKey()) {
            //begin HASH calculation
            ksort($data);
            $hashString = "";
            foreach ($data as $key => $val) {
                $hashString .= strlen($val) . $val;
            }
            return hash_hmac("md5", $hashString, $this->getSecretKey());
        }
    }
}
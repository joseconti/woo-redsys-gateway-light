<?php
/*
 * 2021 Inespay S.L.
 *
 * Este código fuente forma parte de la Información Confidencial contenida
 * en la Cláusula I del Acuerdo de Confidencialidad firmado entre las partes.
 * Por favor, no edite o agregue nada a este archivo.
 * Si desea modificar los servicios que presta, diríjase a support@team.inespay.com.
 * @version 1.0.6
 * @author Inespay <info@inespay.com>
 * @copyright  2021 Inespay S.L.
 * @license    https://www.inespay.com
 * Instituto Europeo de Sistemas de Pago S.L.
 */

require_once('InespayApiBase.php');
require_once('InespayResponse.php');

require_once(plugin_dir_path(__FILE__) . 'requests/PeriodicInitRequest.php');
require_once(plugin_dir_path(__FILE__) . 'requests/PeriodicCancelRequest.php');
require_once(plugin_dir_path(__FILE__) . 'requests/SingleInitRequest.php');
require_once(plugin_dir_path(__FILE__) . 'requests/XmlRefundRequest.php');

require_once(plugin_dir_path(__FILE__) . 'responses/SingleInitResponse.php');
require_once(plugin_dir_path(__FILE__) . 'responses/PeriodicCancelResponse.php');
require_once(plugin_dir_path(__FILE__) . 'responses/PeriodicInitResponse.php');
require_once(plugin_dir_path(__FILE__) . 'responses/SinglePayinsResponse.php');
require_once(plugin_dir_path(__FILE__) . 'responses/XmlRefundResponse.php');


class InespayApiPublic extends InespayApiBase
{
    const SINGLE_PAINS_INIT_ENDPOINT = '/payins/single/init';

    const SINGLE_PAYINS_INFO_ENDPOINT = '/payins/single';

    const PERIODIC_PAYIN_INIT_ENDPOINT = '/payins/periodic/init';

    const PERIODIC_PAYIN_CANCEL_ENDPOINT = '/payins/periodic/cancel';

    const SINGLE_PAYIN_REFUND_ENDPOINT = '/refunds/init';

    const SINGLE_PAYINS_REFUND_SEPA_XML = '/refunds/sepa-xml';

    const SCHEME_STANDARD_TRANSFER = 'SCT';

    const SCHEME_INSTANT_TRANSFER = 'SCT-INST';

    private $ALG_HASH = 'sha256';

    private $subject = null;

    private $amount = null;

    private $reference = null;

    private $urlOk = null;

    private $urlError = null;

    private $urlNotif = null;

    private $accountDestiny = null;

    private $holderDestiny = null;

    private $expireMinutes = 30; //Default

    private $customData = null;

    private $partnerId = null;

    private $bankIdSelected = null;

    private $frequency = null;

    private $debtorAccount = null;

    private $startDate = null;

    private $endDate = null;

    private $debtId = null;

    private $executionRule = null;

    private $dayOfExecution = null;

    private $debtorIdFromBank = null;

    private $debtorBic = null;

    private $debtorIban = null;

    private $debtorName = null;

    private $debtorPaymentOrders = null;

    private $batchBooking = false;

    private $dataReturn = null;

    private $dataReturnDecode = null;

    private $dataReturnJson = null;

    private $signatureDataReturn = null;

    private $customRecipient = null;

    private $scheme = null;

    public function wakeup()
    {
        $okWakeup = false;
        foreach (range(0, 4) as $value) {
            $response = parent::apiRequest([], '/wakeup');

            if (
                isset($response->status)
                && $response->status == parent::STATUS_CODE_SUCCESS
            ) {
                $okWakeup = true;
                break;
            }
        }

        return $okWakeup;
    }

    public function generateSimplePaymentUrl($singleInitRequest)
    {
        $singleInitRequest->setAmount($this->convertAmount($singleInitRequest->getAmount()));
        $singleInitRequestArray = json_decode(json_encode($singleInitRequest), true);
        $singleInitRequestWithoutNulls = array_filter((array) $singleInitRequestArray, [$this, "filterToRemoveNullValues"]); //Eliminamos los valores nulos, vacios..

        $response = parent::apiRequest($singleInitRequestWithoutNulls, self::SINGLE_PAINS_INIT_ENDPOINT);

        return new SingleInitResponse($response);
    }

    private function convertAmount($amount)
    {
        $amountConverted = null;
        if (is_numeric($amount)) {
            $amountConverted = number_format($amount, 2, '.', '');
            $amountConverted = str_replace('.', '', $amountConverted);
        }

        return $amountConverted;
    }

    public function getAmount()
    {
        return ($this->amount / 100);
    }

    public function setAmount($amount)
    {
        if ($amount != null) {
            if (is_numeric($amount)) {
                $amountConverted = number_format($amount, 2, '.', '');
                $amountConverted = str_replace('.', '', $amountConverted);
                $this->amount = $amountConverted;
            } else {
                die('Error: Amount is not a number.');
            }
        } else {
            die('Error: Amount cannot be null.');
        }
    }

    public function generatePeriodicPaymentUrl($periodicInitRequest)
    {
        $periodicInitRequest->setAmount($this->convertAmount($periodicInitRequest->getAmount()));
        $periodicInitRequestArray = json_decode(json_encode($periodicInitRequest), true);
        $periodicInitRequestWithoutNulls = array_filter((array) $periodicInitRequestArray, [$this, "filterToRemoveNullValues"]); //Eliminamos los valores nulos, vacios..

        $response = parent::apiRequest($periodicInitRequestWithoutNulls, self::PERIODIC_PAYIN_INIT_ENDPOINT);

        return new PeriodicInitResponse($response);
    }

    public function generateCancelPeriodicPaymentUrl($periodicCancelRequest)
    {
        $periodicCancelRequestArray = json_decode(json_encode($periodicCancelRequest), true);
        $periodicCancelRequestWithoutNulls = array_filter((array) $periodicCancelRequestArray, [$this, "filterToRemoveNullValues"]); //Eliminamos los valores nulos, vacios..

        $response = parent::apiRequest($periodicCancelRequestWithoutNulls, self::PERIODIC_PAYIN_CANCEL_ENDPOINT);

        return new PeriodicCancelResponse($response);
    }

    public function generateXmlRefund($xmlRefundRequest)
    {
        $xmlRefundRequestArray = json_decode(json_encode($xmlRefundRequest), true);
        $xmlRefundRequestWithoutNulls = array_filter((array) $xmlRefundRequestArray, [$this, "filterToRemoveNullValues"]); //Eliminamos los valores nulos, vacios..

        $response = parent::apiRequest($xmlRefundRequestWithoutNulls, self::SINGLE_PAYINS_REFUND_SEPA_XML);

        return new XmlRefundResponse($response);
    }

    public function generateRefund($singlePayinId, $amount, $description, $reference)
    {
        $dataParams = [];
        $error = null;

        if ($singlePayinId == null) {
            $error = 'singlePayinId not defined';
        } elseif ($amount == null) {
            $error = 'amount not defined';
        } elseif ($description == null) {
            $error = 'description not defined';
        } elseif ($reference == null) {
            $error = 'reference not defined';
        }

        if ($error == null) {
            $dataParams['singlePayinId'] = $singlePayinId;
            $dataParams['amount'] = $this->convertAmount($amount);
            $dataParams['description'] = $description;
            $dataParams['reference'] = $reference;
            return parent::apiRequest($dataParams, self::SINGLE_PAYIN_REFUND_ENDPOINT);
        } else {
            die('Error:' . $error);
        }
    }

    public function getSinglePayins($from, $to, $pageSize = 15, $page = 1)
    {
        $dataParams = [];
        $dataParams['dateFrom'] = $from;
        $dataParams['dateTo'] = $to;
        $dataParams['pageSize'] = $pageSize;
        $dataParams['pageRequest'] = $page;

        $response = parent::apiRequest($dataParams, self::SINGLE_PAYINS_INFO_ENDPOINT, self::GET_HTTP);

        return new SinglePayinsResponse($response);
    }

    public function setSubject($subject)
    {
        if ($subject != null) {
            if (! empty($subject)) {
                $this->subject = trim($subject);
            } else {
                die('Error: subject cannot be empty.');
            }
        } else {
            die('Error: subject cannot be null.');
        }
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function setReference($reference)
    {
        if ($reference != null) {
            if (! empty($reference)) {
                $this->reference = trim($reference);
            } else {
                die('Error: reference cannot be empty.');
            }
        } else {
            die('Error: reference cannot be null.');
        }
    }

    public function setUrlOk($urlOk)
    {
        if ($urlOk != null) {
            if (! empty($urlOk)) {
                $this->urlOk = trim($urlOk);
            } else {
                die('Error: urlOk cannot be empty.');
            }
        } else {
            die('Error: urlOk cannot be null.');
        }
    }

    public function setUrlError($urlError)
    {
        if ($urlError != null) {
            if (! empty($urlError)) {
                $this->urlError = trim($urlError);
            } else {
                die('Error: urlError cannot be empty.');
            }
        } else {
            die('Error: urlError cannot be null.');
        }
    }

    public function setUrlNotif($urlNotif)
    {
        if ($urlNotif != null) {
            if (! empty($urlNotif)) {
                $this->urlNotif = trim($urlNotif);
            } else {
                die('Error: urlNotif cannot be empty.');
            }
        } else {
            die('Error: urlNotif cannot be null.');
        }
    }

    public function setAccountDestiny($accountDestiny)
    {
        if ($accountDestiny != null) {
            if (! empty($accountDestiny)) {
                $this->accountDestiny = trim($accountDestiny);
            } else {
                die('Error: accountDestiny cannot be empty.');
            }
        } else {
            die('Error: accountDestiny cannot be null.');
        }
    }

    public function setHolderDestiny($holderDestiny)
    {
        if ($holderDestiny != null) {
            if (! empty($holderDestiny)) {
                $this->holderDestiny = trim($holderDestiny);
            } else {
                die('Error: holderDestiny cannot be empty.');
            }
        } else {
            die('Error: holderDestiny cannot be null.');
        }
    }

    public function setExpireMinutes($expireMinutes)
    {
        if ($expireMinutes != null) {
            if (is_numeric($expireMinutes)) {
                $this->expireMinutes = $expireMinutes;
            } else {
                die('Error: expireMinutes is not a number.');
            }
        } else {
            die('Error: expireMinutes cannot be null.');
        }
    }

    public function setCustomData($customData)
    {
        if ($customData != null) {
            if (! empty($customData)) {
                $this->customData = $customData;
            } else {
                die('Error: customData cannot be empty.');
            }
        } else {
            die('Error: customData cannot be null.');
        }
    }

    public function setPartnerId($partnerId)
    {
        if ($partnerId != null) {
            if (! empty($partnerId)) {
                $this->partnerId = $partnerId;
            } else {
                die('Error: partnerId cannot be empty.');
            }
        } else {
            die('Error: partnerId cannot be null.');
        }
    }

    public function setFrequency($frequency)
    {
        if ($frequency != null) {
            if (! empty($frequency)) {
                $this->frequency = $frequency;
            } else {
                die('Error: frequency cannot be empty.');
            }
        } else {
            die('Error: frequency cannot be null.');
        }
    }

    public function setDebtorAccount($debtorAccount)
    {
        if ($debtorAccount != null) {
            if (! empty($debtorAccount)) {
                $this->debtorAccount = $debtorAccount;
            } else {
                die('Error: debtorAccount cannot be empty.');
            }
        } else {
            die('Error: debtorAccount cannot be null.');
        }
    }

    public function setStartDate($startDate)
    {
        if ($startDate != null) {
            if (! empty($startDate)) {
                $this->startDate = $startDate;
            } else {
                die('Error: startDate cannot be empty.');
            }
        } else {
            die('Error: startDate cannot be null.');
        }
    }

    public function setEndDate($endDate)
    {
        if ($endDate != null) {
            if (! empty($endDate)) {
                $this->endDate = $endDate;
            } else {
                die('Error: endDate cannot be empty.');
            }
        } else {
            die('Error: endDate cannot be null.');
        }
    }

    public function setBankIdSelected($bankIdSelected)
    {
        if ($bankIdSelected != null) {
            if (! empty($bankIdSelected)) {
                $this->bankIdSelected = trim($bankIdSelected);
            } else {
                die('Error: bankIdSelected cannot be empty.');
            }
        } else {
            die('Error: bankIdSelected cannot be null.');
        }
    }

    public function setDebtId($debtId)
    {
        if ($debtId != null) {
            if (! empty($debtId)) {
                $this->debtId = trim($debtId);
            } else {
                die('Error: debtId cannot be empty.');
            }
        } else {
            die('Error: debtId cannot be null.');
        }
    }

    public function setDebtorIdFromBank($debtorIdFromBank)
    {
        if ($debtorIdFromBank != null) {
            if (! empty($debtorIdFromBank)) {
                $this->debtorIdFromBank = trim($debtorIdFromBank);
            } else {
                die('Error: debtorIdFromBank cannot be empty.');
            }
        } else {
            die('Error: debtorIdFromBank cannot be null.');
        }
    }

    public function setDebtorBic($debtorBic)
    {
        if ($debtorBic != null) {
            if (! empty($debtorBic)) {
                $this->debtorBic = trim($debtorBic);
            } else {
                die('Error: debtorBic cannot be empty.');
            }
        } else {
            die('Error: debtorBic cannot be null.');
        }
    }

    public function setDebtorIban($debtorIban)
    {
        if ($debtorIban != null) {
            if (! empty($debtorIban)) {
                $this->debtorIban = trim($debtorIban);
            } else {
                die('Error: debtorIban cannot be empty.');
            }
        } else {
            die('Error: debtorIban cannot be null.');
        }
    }

    public function setDebtorName($debtorName)
    {
        if ($debtorName != null) {
            if (! empty($debtorName)) {
                $this->debtorName = trim($debtorName);
            } else {
                die('Error: debtorName cannot be empty.');
            }
        } else {
            die('Error: debtorName cannot be null.');
        }
    }

    public function setBatchBooking($batchBooking)
    {
        if (is_bool($batchBooking)) {
            $this->batchBooking = $batchBooking;
        } else {
            die('Error: batchBooking must be a boolean.');
        }
    }

    public function setDebtorPaymentOrders($debtorPaymentOrders)
    {
        if ($debtorPaymentOrders != null) {
            if (! empty($debtorPaymentOrders)) {
                $this->debtorPaymentOrders = $debtorPaymentOrders;
            } else {
                die('Error: debtorPaymentOrders cannot be empty.');
            }
        } else {
            die('Error: debtorPaymentOrders cannot be null.');
        }
    }

    public function setDataReturn($dataReturnBase64)
    {
        if (($dataReturnBase64 != null) && (! empty($dataReturnBase64))) {
            $this->dataReturn = $dataReturnBase64;
            $this->dataReturnDecode = $this->decodeBase64($this->dataReturn);

            $this->dataReturnJson = json_decode($this->dataReturnDecode, true);
        } else {
            die('Error: dataReturn cannot be null or empty.');
        }
    }

    private function decodeBase64($data)
    {
        return base64_decode($data);
    }

    public function getStatusFromDataReturn()
    {
        return $this->dataReturnJson['codStatus'];
    }

    public function getDescriptionFromDataReturn()
    {
        return $this->dataReturnJson['description'];
    }

    public function getTransactionIdFromDataReturn()
    {
        return $this->dataReturnJson['transactionId'];
    }

    public function getAmountFromDataReturn()
    {
        return $this->dataReturnJson['amount'];
    }

    public function getReferenceFromDataReturn()
    {
        return $this->dataReturnJson['reference'];
    }

    public function getCustomDataFromDataReturn()
    {
        if (array_key_exists('customData', $this->dataReturnJson)) {
            return $this->dataReturnJson['customData'];
        } else {
            return 'error: customData not exist in response';
        }
    }

    public function getSignatureDataReturn()
    {
        return $this->signatureDataReturn;
    }

    public function setSignatureDataReturn($signatureDataReturn)
    {
        if (($signatureDataReturn != null) && ! empty($signatureDataReturn)) {
            $this->signatureDataReturn = $signatureDataReturn;
        } else {
            die('Error: signatureDataReturn cannot be null or empty.');
        }

    }

    public function isDataReturnValid()
    {
        $signatureDataOk = false;
        if ($this->signatureDataReturn === $this->calculateSignature($this->dataReturn, $this->apiKeyInespay)) {
            $signatureDataOk = true;
        }

        return $signatureDataOk;
    }

    public function calculateSignature($dataReturnDecode, $key)
    {
        $signatureBase64 = null;

        if (($dataReturnDecode != null) && (! empty($dataReturnDecode))) {
            if ($key != null) {
                $signatureCalculated = $this->createSignatureFromData($dataReturnDecode, $key);
                $signatureBase64 = $this->encodeBase64($signatureCalculated);
            } else {
                die('Error: key to calculated signature not init.');
            }
        } else {
            die('Error: $dataReturnDecode cannot be null or empty.');
        }

        return $signatureBase64;
    }

    private function createSignatureFromData($data, $key)
    {
        $signatureData = hash_hmac($this->ALG_HASH, $data, $key, false);

        return $signatureData;
    }

    private function encodeBase64($data)
    {
        return base64_encode($data);
    }

    public function getCustomRecipient()
    {
        return $this->customRecipient;
    }

    public function setCustomRecipient($customRecipient)
    {
        $this->customRecipient = $customRecipient;
    }

    public function getExecutionRule()
    {
        return $this->executionRule;
    }

    public function setExecutionRule($executionRule)
    {
        $this->executionRule = $executionRule;
    }

    public function getDayOfExecution()
    {
        return $this->dayOfExecution;
    }

    public function setDayOfExecution($dayOfExecution)
    {
        $this->dayOfExecution = $dayOfExecution;
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
    }

    private function validateMandatoryParamsPeriodicPayment()
    {
        $error = null;
        if ($this->subject == null) {
            $error = 'subject not defined';
        } elseif ($this->amount == null) {
            $error = 'amount not defined';
        } elseif ($this->reference == null) {
            $error = 'reference not defined';
        } elseif ($this->frequency == null) {
            $error = 'frequency not defined';
        } elseif ($this->startDate == null) {
            $error = 'startDate not defined';
        }

        return $error;
    }

    private function validateMandatoryParamsSimplePayment()
    {
        $error = null;
        if ($this->subject == null) {
            $error = 'subject not defined';
        } elseif ($this->amount == null) {
            $error = 'amount not defined';
        } elseif ($this->reference == null) {
            $error = 'reference not defined';
        }

        return $error;
    }

    private function filterToRemoveNullValues($var)
    {
        return ($var !== null && $var !== false && $var !== "");
    }
}

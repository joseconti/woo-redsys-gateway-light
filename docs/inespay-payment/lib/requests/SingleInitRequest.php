<?php

class SingleInitRequest implements \JsonSerializable
{
    private $description;

    private $amount;

    private $reference;

    private $successLinkRedirect;

    private $successLinkRedirectMethod;

    private $abortLinkRedirect;

    private $abortLinkRedirectMethod;

    private $notifUrl;

    private $notifUrlContentType;

    private $expiration;

    private $creditorAccount;

    private $customData;

    private $customBeneficiary;

    private $selectedBank;

    private $scheme;

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param mixed $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return mixed
     */
    public function getSuccessLinkRedirect()
    {
        return $this->successLinkRedirect;
    }

    /**
     * @param mixed $successLinkRedirect
     */
    public function setSuccessLinkRedirect($successLinkRedirect)
    {
        $this->successLinkRedirect = $successLinkRedirect;
    }

    /**
     * @return mixed
     */
    public function getSuccessLinkRedirectMethod()
    {
        return $this->successLinkRedirectMethod;
    }

    /**
     * @param mixed $successLinkRedirectMethod
     */
    public function setSuccessLinkRedirectMethod($successLinkRedirectMethod)
    {
        $this->successLinkRedirectMethod = $successLinkRedirectMethod;
    }

    /**
     * @return mixed
     */
    public function getAbortLinkRedirect()
    {
        return $this->abortLinkRedirect;
    }

    /**
     * @param mixed $abortLinkRedirect
     */
    public function setAbortLinkRedirect($abortLinkRedirect)
    {
        $this->abortLinkRedirect = $abortLinkRedirect;
    }

    /**
     * @return mixed
     */
    public function getAbortLinkRedirectMethod()
    {
        return $this->abortLinkRedirectMethod;
    }

    /**
     * @param mixed $abortLinkRedirectMethod
     */
    public function setAbortLinkRedirectMethod($abortLinkRedirectMethod)
    {
        $this->abortLinkRedirectMethod = $abortLinkRedirectMethod;
    }

    /**
     * @return mixed
     */
    public function getNotifUrl()
    {
        return $this->notifUrl;
    }

    /**
     * @param mixed $notifUrl
     */
    public function setNotifUrl($notifUrl)
    {
        $this->notifUrl = $notifUrl;
    }

    /**
     * @return mixed
     */
    public function getNotifUrlContentType()
    {
        return $this->notifUrlContentType;
    }

    /**
     * @param mixed $notifUrlContentType
     */
    public function setNotifUrlContentType($notifUrlContentType)
    {
        $this->notifUrlContentType = $notifUrlContentType;
    }

    /**
     * @return mixed
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * @param mixed $expiration
     */
    public function setExpiration($expiration)
    {
        $this->expiration = $expiration;
    }

    /**
     * @return mixed
     */
    public function getCreditorAccount()
    {
        return $this->creditorAccount;
    }

    /**
     * @param mixed $creditorAccount
     */
    public function setCreditorAccount($creditorAccount)
    {
        $this->creditorAccount = $creditorAccount;
    }

    /**
     * @return mixed
     */
    public function getCustomData()
    {
        return $this->customData;
    }

    /**
     * @param mixed $customData
     */
    public function setCustomData($customData)
    {
        $this->customData = $customData;
    }

    /**
     * @return mixed
     */
    public function getCustomBeneficiary()
    {
        return $this->customBeneficiary;
    }

    /**
     * @param mixed $customBeneficiary
     */
    public function setCustomBeneficiary($customBeneficiary)
    {
        $this->customBeneficiary = $customBeneficiary;
    }

    /**
     * @return mixed
     */
    public function getSelectedBank()
    {
        return $this->selectedBank;
    }

    /**
     * @param mixed $selectedBank
     */
    public function setSelectedBank($selectedBank)
    {
        $this->selectedBank = $selectedBank;
    }

    /**
     * @return mixed
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @param mixed $scheme
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
    }
}
<?php

class NotificationCallback
{
    /** @var string */
    public $singlePayinId;

    /** @var string */
    public $codStatus;

    /** @var string */
    public $description;

    /** @var int */
    public $amount;

    /** @var string */
    public $reference;

    /** @var int */
    public $date;

    /** @var string */
    public $creditorAccount;

    /** @var string */
    public $debtorName;

    /** @var string */
    public $debtorAccount;

    /** @var string|null */
    public $customData;

    /** @var string */
    public $periodicPayinId;

    /** @var int */
    public $startDate;

    /** @var int */
    public $endDate;

    /** @var string */
    public $frequency;

    /** @var string */
    public $periodicCancelId;

    public function __construct($data)
    {
        if (isset($data->singlePayinId)) {
            $this->singlePayinId = $data->singlePayinId;
        }

        if (isset($data->codStatus)) {
            $this->codStatus = $data->codStatus;
        }

        if (isset($data->description)) {
            $this->description = $data->description;
        }

        if (isset($data->amount)) {
            $this->amount = $data->amount;
        }

        if (isset($data->reference)) {
            $this->reference = $data->reference;
        }

        if (isset($data->date)) {
            $this->date = $data->date;
        }

        if (isset($data->creditorAccount)) {
            $this->creditorAccount = $data->creditorAccount;
        }

        if (isset($data->customData)) {
            $this->customData = $data->customData;
        }

        if (isset($data->periodicPayinId)) {
            $this->periodicPayinId = $data->periodicPayinId;
        }

        if (isset($data->startDate)) {
            $this->startDate = $data->startDate;
        }

        if (isset($data->endDate)) {
            $this->endDate = $data->endDate;
        }

        if (isset($data->frequency)) {
            $this->frequency = $data->frequency;
        }

        if (isset($data->periodicCancelId)) {
            $this->periodicCancelId = $data->periodicCancelId;
        }
    }

    public function getSinglePayinId()
    {
        return $this->singlePayinId;
    }

    public function setSinglePayinId($singlePayinId)
    {
        $this->singlePayinId = $singlePayinId;
    }

    public function getCodStatus()
    {
        return $this->codStatus;
    }

    public function setCodStatus($codStatus)
    {
        $this->codStatus = $codStatus;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getCreditorAccount()
    {
        return $this->creditorAccount;
    }

    public function setCreditorAccount($creditorAccount)
    {
        $this->creditorAccount = $creditorAccount;
    }

    public function getDebtorName()
    {
        return $this->debtorName;
    }

    public function setDebtorName($debtorName)
    {
        $this->debtorName = $debtorName;
    }

    public function getDebtorAccount()
    {
        return $this->debtorAccount;
    }

    public function setDebtorAccount($debtorAccount)
    {
        $this->debtorAccount = $debtorAccount;
    }

    public function getCustomData()
    {
        return $this->customData;
    }

    public function setCustomData($customData)
    {
        $this->customData = $customData;
    }

    public function getPeriodicPayinId()
    {
        return $this->periodicPayinId;
    }

    public function setPeriodicPayinId($periodicPayinId)
    {
        $this->periodicPayinId = $periodicPayinId;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    public function getFrequency()
    {
        return $this->frequency;
    }

    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;
    }

    public function getPeriodicCancelId()
    {
        return $this->periodicCancelId;
    }

    public function setPeriodicCancelId($periodicCancelId)
    {
        $this->periodicCancelId = $periodicCancelId;
    }
}
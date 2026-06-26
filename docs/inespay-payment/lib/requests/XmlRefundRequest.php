<?php

class XmlRefundRequest implements \JsonSerializable
{
    private $debtorIdFromBank;

    private $debtorBic;

    private $debtorIban;

    private $debtorName;

    private $batchBooking;

    private $paymentOrders;

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * @return mixed
     */
    public function getDebtorIdFromBank()
    {
        return $this->debtorIdFromBank;
    }

    /**
     * @param mixed $debtorIdFromBank
     */
    public function setDebtorIdFromBank($debtorIdFromBank)
    {
        $this->debtorIdFromBank = $debtorIdFromBank;
    }

    /**
     * @return mixed
     */
    public function getDebtorBic()
    {
        return $this->debtorBic;
    }

    /**
     * @param mixed $debtorBic
     */
    public function setDebtorBic($debtorBic)
    {
        $this->debtorBic = $debtorBic;
    }

    /**
     * @return mixed
     */
    public function getDebtorIban()
    {
        return $this->debtorIban;
    }

    /**
     * @param mixed $debtorIban
     */
    public function setDebtorIban($debtorIban)
    {
        $this->debtorIban = $debtorIban;
    }

    /**
     * @return mixed
     */
    public function getDebtorName()
    {
        return $this->debtorName;
    }

    /**
     * @param mixed $debtorName
     */
    public function setDebtorName($debtorName)
    {
        $this->debtorName = $debtorName;
    }

    /**
     * @return mixed
     */
    public function getBatchBooking()
    {
        return $this->batchBooking;
    }

    /**
     * @param mixed $batchBooking
     */
    public function setBatchBooking($batchBooking)
    {
        $this->batchBooking = $batchBooking;
    }

    /**
     * @return mixed
     */
    public function getPaymentOrders()
    {
        return $this->paymentOrders;
    }

    /**
     * @param mixed $paymentOrders
     */
    public function setPaymentOrders($paymentOrders)
    {
        $this->paymentOrders = $paymentOrders;
    }
}
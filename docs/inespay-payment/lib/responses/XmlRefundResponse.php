<?php

class XmlRefundResponse
{
    private $xml = null;

    private $status = null;

    private $statusDesc = null;

    public function __construct($data)
    {
        if (isset($data->xml)) {
            $this->xml = $data->xml;
        }

        if (isset($data->status)) {
            $this->status = $data->status;
        }

        if (isset($data->statusDesc)) {
            $this->statusDesc = $data->statusDesc;
        }
    }

    /**
     * @return null
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * @param null $xml
     */
    public function setXml($xml)
    {
        $this->xml = $xml;
    }

    /**
     * @return null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param null $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return null
     */
    public function getStatusDesc()
    {
        return $this->statusDesc;
    }

    /**
     * @param null $statusDesc
     */
    public function setStatusDesc($statusDesc)
    {
        $this->statusDesc = $statusDesc;
    }
}
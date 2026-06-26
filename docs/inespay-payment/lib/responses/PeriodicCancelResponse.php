<?php

class PeriodicCancelResponse
{
    private $periodicCancelId = null;

    private $periodicCancelLink = null;

    private $status = null;

    private $statusDesc = null;

    public function __construct($data)
    {
        if (isset($data->periodicCancelId)) {
            $this->periodicCancelId = $data->periodicCancelId;
        }

        if (isset($data->periodicCancelLink)) {
            $this->periodicCancelLink = $data->periodicCancelLink;
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
    public function getPeriodicCancelId()
    {
        return $this->periodicCancelId;
    }

    /**
     * @param null $periodicCancelId
     */
    public function setPeriodicCancelId($periodicCancelId)
    {
        $this->periodicCancelId = $periodicCancelId;
    }

    /**
     * @return null
     */
    public function getPeriodicCancelLink()
    {
        return $this->periodicCancelLink;
    }

    /**
     * @param null $periodicCancelLink
     */
    public function setPeriodicCancelLink($periodicCancelLink)
    {
        $this->periodicCancelLink = $periodicCancelLink;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getStatusDesc()
    {
        return $this->statusDesc;
    }
}
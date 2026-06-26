<?php

class PeriodicInitResponse
{
    private $periodicPayinId = null;

    private $periodicPayinLink = null;

    private $status = null;

    private $statusDesc = null;

    public function __construct($data)
    {
        if (isset($data->periodicPayinId)) {
            $this->periodicPayinId = $data->periodicPayinId;
        }

        if (isset($data->periodicPayinLink)) {
            $this->periodicPayinLink = $data->periodicPayinLink;
        }

        if (isset($data->status)) {
            $this->status = $data->status;
        }

        if (isset($data->statusDesc)) {
            $this->statusDesc = $data->statusDesc;
        }
    }

    public function getPeriodicPayinId()
    {
        return $this->periodicPayinId;
    }

    public function getPeriodicPayinLink()
    {
        return $this->periodicPayinLink;
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
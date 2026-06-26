<?php

class SingleInitResponse
{
    private $singlePayinId = null;
    private $singlePayinLink = null;

    private $status = null;

    private $statusDesc = null;

    public function __construct($data)
    {
        if (isset($data->singlePayinId)) {
            $this->singlePayinId = $data->singlePayinId;
        }

        if (isset($data->singlePayinLink)) {
            $this->singlePayinLink = $data->singlePayinLink;
        }

        if (isset($data->status)) {
            $this->status = $data->status;
        }

        if (isset($data->statusDesc)) {
            $this->statusDesc = $data->statusDesc;
        }
    }

    public function getSinglePayinId()
    {
        return $this->singlePayinId;
    }

    public function getSinglePayinLink()
    {
        return $this->singlePayinLink;
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
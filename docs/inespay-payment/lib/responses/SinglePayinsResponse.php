<?php

class SinglePayinsResponse
{
    private $singlePayins = null;

    private $itemsReturned = null;

    private $currentPage = null;

    private $totalItems = null;

    private $totalPages = null;

    private $pageSize = null;

    private $status = null;

    private $statusDesc = null;

    public function __construct($data)
    {
        if (isset($data->singlePayins)) {
            $this->singlePayins = $data->singlePayins;
        }

        if (isset($data->itemsReturned)) {
            $this->itemsReturned = $data->itemsReturned;
        }

        if (isset($data->currentPage)) {
            $this->currentPage = $data->currentPage;
        }

        if (isset($data->totalItems)) {
            $this->totalItems = $data->totalItems;
        }

        if (isset($data->totalPages)) {
            $this->totalPages = $data->totalPages;
        }

        if (isset($data->pageSize)) {
            $this->pageSize = $data->pageSize;
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
    public function getSinglePayins()
    {
        return $this->singlePayins;
    }

    /**
     * @param null $singlePayins
     */
    public function setSinglePayins($singlePayins)
    {
        $this->singlePayins = $singlePayins;
    }

    /**
     * @return null
     */
    public function getItemsReturned()
    {
        return $this->itemsReturned;
    }

    /**
     * @param null $itemsReturned
     */
    public function setItemsReturned($itemsReturned)
    {
        $this->itemsReturned = $itemsReturned;
    }

    /**
     * @return null
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param null $currentPage
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
    }

    /**
     * @return null
     */
    public function getTotalItems()
    {
        return $this->totalItems;
    }

    /**
     * @param null $totalItems
     */
    public function setTotalItems($totalItems)
    {
        $this->totalItems = $totalItems;
    }

    /**
     * @return null
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }

    /**
     * @param null $totalPages
     */
    public function setTotalPages($totalPages)
    {
        $this->totalPages = $totalPages;
    }

    /**
     * @return null
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @param null $pageSize
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
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
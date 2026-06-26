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


class InespayResponse
{
    private $urlSigned = null;

    private $statusCode = null;

    private $idDebt = null;

    private $statusDescription = null;

    public function __construct($data)
    {
        if (isset($data->status)) {
            $this->statusCode = $data->status;
        }

        if (isset($data->description)) {
            $this->statusDescription = $data->description;
        }

        if (isset($data->url)) {
            $this->urlSigned = $data->url;
        }

        if (isset($data->idDebt)) {
            $this->idDebt = $data->idDebt;
        }
    }

    public function getUrlSigned()
    {
        return $this->urlSigned;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getStatusDescription()
    {
        return $this->statusDescription;
    }

    public function getIdDebt()
    {
        return $this->idDebt;
    }
}

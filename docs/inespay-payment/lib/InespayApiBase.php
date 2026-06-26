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

class InespayApiBase
{
    const STATUS_CODE_SUCCESS = '200';
    const STATUS_CODE_OK = 'OK';
    const STATUS_CODE_SETTLED = 'SETTLED';

    const CONNECT_TIMEOUT = 28;

    const TIMEOUT = 28;

    const ENV_TEST = 'test';

    const ENV_SAN = 'san';

    const ENV_UAT = 'uat';

    const ENV_PRO = 'pro';

    protected $urlBaseApiInespay = null;

    protected $tokenInespay = null;

    protected $apiKeyInespay = null;

    const URL_BASE = 'https://apiflow.inespay.com';

    const URL_TEST = self::URL_BASE . '/test/v21';

    const URL_SANDBOX = self::URL_BASE . '/san/v21';

    const URL_UAT = self::URL_BASE . '/uat/v21';

    const URL_PRODUCTION = self::URL_BASE . '/pro/v21';

    const GET_HTTP = 'get';

    const POST_HTTP = 'post';

    public function setEnvironmentInespay($environmentApi)
    {
        $this->urlBaseApiInespay = $this->getUrlBaseWithEnv($environmentApi);
    }

    public function setTokenInespay($tokenApi)
    {
        $this->tokenInespay = $tokenApi;
    }

    public function setApiKeyInespay($keyApi)
    {
        $this->apiKeyInespay = $keyApi;
    }

    /**
     * @throws Exception
     */
    protected function apiRequest($data, $endpoint, $httpVerb = self::POST_HTTP)
    {
        $headers = [];
        $headers[] = 'Authorization:' . $this->tokenInespay;
        $headers[] = 'X-Api-Key:' . $this->apiKeyInespay;
        $headers[] = 'Content-Type: application/json';

        $curl = curl_init();

        $url = $this->urlBaseApiInespay . $endpoint;
        if (strcasecmp(self::POST_HTTP, $httpVerb) === 0) {
            $dataJson = json_encode($data);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $dataJson);
        } elseif (strcasecmp(self::GET_HTTP, $httpVerb) === 0) {
            $url = $url . '?' . http_build_query($data);
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIMEOUT);
        curl_setopt($curl, CURLOPT_TIMEOUT, self::TIMEOUT);

        $result = curl_exec($curl);

        if (! curl_errno($curl)) {
            curl_close($curl);
            $result = json_decode($result);
        } else {
            throw new Exception(curl_error($curl));
        }

        return $result;
    }

    public static function getUrlBaseWithEnv($environmentApi)
    {
        $urlBase = 'unknown';

        if ($environmentApi == InespayApiBase::ENV_TEST) {
            $urlBase = self::URL_TEST;
        } elseif ($environmentApi == InespayApiBase::ENV_SAN) {
            $urlBase = self::URL_SANDBOX;
        } elseif ($environmentApi == InespayApiBase::ENV_UAT) {
            $urlBase = self::URL_UAT;
        } elseif ($environmentApi == InespayApiBase::ENV_PRO) {
            $urlBase = self::URL_PRODUCTION;
        }

        return $urlBase;
    }
}

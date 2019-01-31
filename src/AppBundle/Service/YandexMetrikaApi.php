<?php
/**
 * Created by PhpStorm.
 * User: MVYaroslavcev
 * Date: 29/01/19
 * Time: 22:07
 */

namespace AppBundle\Service;


class YandexMetrikaApi
{
    private $token;
    private $urlApi;
    private $counterId;


    public function __construct($token, $urlApi, $counterId)
    {
        $this->token = $token;
        $this->urlApi = $urlApi;
        $this->counterId = $counterId;
    }

    public function getTable()
    {
        return $this->getData($this->urlApi.
            "stat/v1/data?ids=".
            $this->counterId.
            "&preset=traffic".
            "&pretty=1"
        );
    }

    private function getData($url)
    {
        $result = false;
        if ($url)
        {
            $headers = array(
                'Authorization: OAuth '.$this->token,
            );

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $data = curl_exec($curl);

            $data=json_decode($data);
            if ($data)
            {
                if (property_exists($data,'data'))
                {
                    if ($data->data!='')
                    {
                        $result=$data->data;
                    }
                }
            }
            curl_close($curl);
        }

        return $result;
    }

}
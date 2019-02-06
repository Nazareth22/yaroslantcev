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
        $result = array();
        $url = $this->urlApi.
            "stat/v1/data/bytime?ids=".
            $this->counterId.
            "&date1=4daysAgo".
            "&date2=today".
            "&dimensions=ym:s:<attribution>TrafficSource&attribution=last".
            "&group=day".
            "&preset=traffic"
            ;
        $data = $this->getData($url);

        $i = 0;
        foreach ($data['time_intervals'] as $date)
        {
            $metricsArr = [];
            $j = 0;
            foreach ($data['data']->metrics as $metric)
            {
                if ($j == 6)
                {
                    $metricsArr[] = gmdate('i:s', $metric[$i]);
                }
                else
                {
                    $metricsArr[] = $metric[$i];
                }
                $j++;
            }
            $result[] = ['date'=>$date[0], 'metrics'=>$metricsArr];

            $i++;
        }



        return  array_reverse($result);
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
                $result = ['data'=>$data->data[0], 'time_intervals'=>$data->time_intervals];
            }
            curl_close($curl);
        }

        return $result;
    }

}
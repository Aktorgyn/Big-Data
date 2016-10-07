<?php

namespace app\helpers;

/**
 * Created by PhpStorm.
 * User: Akoshka-Kakashka
 * Date: 10/4/16
 * Time: 11:58 PM
 */


class NurNewsParser
{


    /**
     * @param $newsNumber
     * @return array
     * Trying to spoof as normal user to get Data via Curl Get Request
     */
    public static function getNewsUrls($newsNumber){

        // Creating spoofed header
        $header[0] = "Accept: application/json";
        $header[] = "Accept-Encoding: gzip, deflate, sdch, br";
        $header[] = "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4";
        $header[] = "Cache-Control: no-cache";
        $header[] = "Connection: keep-alive";
        $header[] = "Host: data.nur.kz";
        $header[] = "Origin: https://www.nur.kz";
        $header[] = "Pragma: no-cache";
        $header[] = "Referer: https://www.nur.kz/world";
        $header[] = "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36";
        $header[] = "X-Requested-With: XMLHttpRequest";

        $ch = curl_init();

        // Setting Target Url
        curl_setopt($ch, CURLOPT_URL,self::getCurlUrl($newsNumber));

        // Setting Return Transfer
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);

        // Get Result with Header also
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Need to Follow from various redirects
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);

        $response=curl_exec($ch);

        curl_close($ch);

        return self::makeUrlFromRaw(json_decode(gzdecode($response),true));
    }

    /**
     * @param $newsNumber
     * @return string
     *
     * Build Request Url
     */
    private static function getCurlUrl($newsNumber){
        return "https://data.nur.kz/posts/latest-by-tag/32717?search[language]=ru&per-page=".$newsNumber."&search[status]=3&sort=-published_at&thumbnail=r305x185&_format=json&fields=id,slug,catchy_title,description,published_at,thumb,comment_count,section_id&page=1";
    }


    /**
     * @param $rawJson
     * @return array
     *
     * Form correct News Urls
     */
    private static function makeUrlFromRaw($rawJson){
        $ans = [];

        foreach($rawJson as $item){

            $ans[] = [
                'url' => "https://nur.kz/".$item['id'].'-'.$item['slug'].'.html',
                'news_title' => $item["catchy_title"]
            ];

        }

        return $ans;
    }

}
<?php

namespace app\helpers;

/**
 * Created by PhpStorm.
 * User: Akoshka-Kakashka
 * Date: 10/4/16
 * Time: 11:58 PM
 */
class NurCommentParser
{
    public $newsUrl;
    private $currentPage;
    private $comments;
    private $newsTitle;

    /**
     * NurCommentParser constructor.
     * @param array $news
     *
     * Construct Comment Parser Object with given News Url
     */
    function __construct($news) {
        $this->newsUrl = $news['url'];
        $this->newsTitle = $news["news_title"];
        $this->currentPage = 1;

    }

    /**
     * @return mixed
     *
     * Sole public function which will give array of comments
     */
    public function getComments(){

        $rawResponse = $this->getRawData();

        $this->comments = $this->addNewsTitle(json_decode($rawResponse[1], true));

        $maxPageNumber = self::getMaxPage($rawResponse[0]);

        while($maxPageNumber >= $this->currentPage ){

            $this->comments = array_merge($this->comments, $this->addNewsTitle(json_decode($this->getRawData()[1], true)));
        }
        return $this->comments;
    }

    /**
     * @return array
     *
     * Creating spoof to fool Nur.kz server
     */
    private function getRawData(){

        // Creating spoofed Header
        $header[0] = "Accept: application/json";
        $header[] = "Accept-Encoding: gzip, deflate, sdch, br";
        $header[] = "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4";
        $header[] = "Cache-Control: no-cache";
        $header[] = "Connection: keep-alive";
        $header[] = "Host: data.nur.kz";
        $header[] = "Origin: https://www.nur.kz";
        $header[] = "Pragma: no-cache";
        $header[] = "Referer: $this->newsUrl";
        $header[] = "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36";
        $header[] = "X-Requested-With: XMLHttpRequest";

        $ch = curl_init();

        // Setting Proper Url and Return Transfer
        curl_setopt($ch, CURLOPT_URL,$this->getCommentUrl());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);

        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        $response=curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        curl_close($ch);

        $this->currentPage++;

        // Decode Gzip
        return [$header, gzdecode($body)];
    }


    /**
     * @return string
     *
     * Form Url for comments
     */
    private function getCommentUrl(){
        $ans = 'https://data.nur.kz/post-currents/';
        preg_match('/\d{7}/', $this->newsUrl, $newsId);
        return $ans.$newsId[0].'/comments?page='.$this->currentPage;
    }


    /**
     * @param $header
     * @return int
     *
     * Get comment page number
     */
    private static function getMaxPage($header){
        preg_match("/X-Pagination-Page-Count:.+/", $header, $matches);
        return (int) explode(' ',trim($matches[0]))[1];
    }

    /**
     * @param $array
     * @return array
     */
    private function addNewsTitle($array){
        $ans = [];
        foreach ($array as $item){
            $item['news_title'] = $this->newsTitle;
            $ans[] = $item;
        }

        return $ans;
    }

}
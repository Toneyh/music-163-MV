<?php
header('Content-Type:application/json; charset=utf-8');
function MloocCurl($url, $UserAgent = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, $UserAgent);
        #关闭重定向
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        #关闭SSL
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        #返回数据不直接显示
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
$id = isset($_GET['id']) ? $_GET['id'] : "";
if($id == ""){
    $result['code'] = 400;
    $result['msg'] = "参数错误";
    print(json_encode($result));
    exit();
}
$html = MloocCurl("https://music.163.com/song?id=" . $id);
preg_match("~<a title=\"播放mv\" href=\"\/mv\?id=(.*?)\">~", $html, $id);
if(!isset($id[1])){
    $result['code'] = 400;
    $result['msg'] = "该歌曲没有MV";
    print(json_encode($result));
    exit();
}
$id = $id[1];
$html = MloocCurl("https://music.163.com/mv?id=" . $id);
preg_match("~<meta property=\"og:title\" content=\"(.*?)\" \/>~", $html, $title);
preg_match("~<meta property=\"og:image\" content=\"(.*?)\" \/>~", $html, $image);
preg_match("~<meta property=\"og:video\" content=\"(.*?)\" \/>~", $html, $video);
if(!isset($title[1]) || !isset($image[1]) || !isset($video[1])){
    $result['code'] = 400;
    $result['msg'] = "获取错误";
    print(json_encode($result));
    exit();
}
$title = $title[1];
$image = $image[1];
$video = urldecode($video[1]);

$result['code'] = 200;
$result['title'] = $title;
$result['img'] = $image;
$result['video'] = $video;
$result['msg'] = "获取成功";
print(json_encode($result));
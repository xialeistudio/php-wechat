<?php
/**
 * Created by PhpStorm.
 * User: xialei
 * Date: 2018/7/19
 * Time: 上午10:30
 */

namespace Wechat\reply;

use Wechat\WechatReplyException;

/**
 * 回复音乐
 * Class MusicResponseBuilder
 * @package Wechat\reply
 */
class MusicResponseBuilder extends ResponseBuilder
{
    private $title;
    private $description;
    private $musicUrl;
    private $hqMusicUrl;
    private $thumbMediaId;

    /**
     * @param mixed $title
     * @return MusicResponseBuilder
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param mixed $description
     * @return MusicResponseBuilder
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param mixed $musicUrl
     * @return MusicResponseBuilder
     */
    public function setMusicUrl($musicUrl)
    {
        $this->musicUrl = $musicUrl;
        return $this;
    }

    /**
     * @param mixed $hqMusicUrl
     * @return MusicResponseBuilder
     */
    public function setHqMusicUrl($hqMusicUrl)
    {
        $this->hqMusicUrl = $hqMusicUrl;
        return $this;
    }

    /**
     * @param mixed $thumbMediaId
     * @return MusicResponseBuilder
     */
    public function setThumbMediaId($thumbMediaId)
    {
        $this->thumbMediaId = $thumbMediaId;
        return $this;
    }

    public function build(): string
    {
        if (empty($this->thumbMediaId)) {
            throw new WechatReplyException('thumbMediaId不能为空');
        }
        $placeholder = <<<XML
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%d</CreateTime>
<MsgType><![CDATA[music]]></MsgType>
<Music>
<Title><![CDATA[%s]]></Title>
<Description><![CDATA[%s]]></Description>
<MusicUrl><![CDATA[%s]]></MusicUrl>
<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
</Music>
</xml>
XML;

        $now = time();
        return sprintf(
            $placeholder,
            $this->request['FromUserName'],
            $this->request['ToUserName'],
            $now,
            $this->title,
            $this->description,
            $this->musicUrl,
            $this->hqMusicUrl,
            $this->thumbMediaId
        );
    }
}
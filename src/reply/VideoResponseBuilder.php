<?php
/**
 * Created by PhpStorm.
 * User: xialei
 * Date: 2018/7/19
 * Time: 上午10:27
 */

namespace Wechat\reply;

use Wechat\WechatReplyException;

/**
 * 视频消息回复
 * Class VideoResponseBuilder
 * @package Wechat\reply
 */
class VideoResponseBuilder extends ResponseBuilder
{
    private $mediaId;
    private $title;
    private $description;

    /**
     * @param mixed $mediaId
     * @return VideoResponseBuilder
     */
    public function setMediaId($mediaId)
    {
        $this->mediaId = $mediaId;
        return $this;
    }

    /**
     * @param mixed $title
     * @return VideoResponseBuilder
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param mixed $description
     * @return VideoResponseBuilder
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function build(): string
    {
        if (empty($this->mediaId)) {
            throw new WechatReplyException('media_id不能为空');
        }
        $placeholder = <<<XML
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%d</CreateTime>
<MsgType><![CDATA[video]]></MsgType>
<Video>
<MediaId><![CDATA[%s]]></MediaId>
<Title><![CDATA[%s]]></Title>
<Description><![CDATA[%s]]></Description>
</Video>
</xml>
XML;

        $now = time();
        return sprintf(
            $placeholder,
            $this->request['FromUserName'],
            $this->request['ToUserName'],
            $now,
            $this->mediaId,
            $this->title,
            $this->description
        );
    }
}
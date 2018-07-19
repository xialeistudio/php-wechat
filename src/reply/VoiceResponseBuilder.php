<?php
/**
 * Created by PhpStorm.
 * User: xialei
 * Date: 2018/7/19
 * Time: 上午10:23
 */

namespace Wechat\reply;

use Wechat\WechatReplyException;

/**
 * Class VoiceResponseBuilder
 * @package Wechat\reply
 */
class VoiceResponseBuilder extends ResponseBuilder
{
    private $mediaId;

    /**
     * @param mixed $mediaId
     * @return VoiceResponseBuilder
     */
    public function setMediaId($mediaId)
    {
        $this->mediaId = $mediaId;
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
<MsgType><![CDATA[voice]]></MsgType>
<Voice>
<MediaId><![CDATA[%s]]></MediaId>
</Voice>
</xml>
XML;

        $now = time();
        return sprintf(
            $placeholder,
            $this->request['FromUserName'],
            $this->request['ToUserName'],
            $now,
            $this->mediaId
        );
    }
}
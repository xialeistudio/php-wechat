<?php
/**
 * Created by PhpStorm.
 * User: xialei
 * Date: 2018/7/19
 * Time: 上午10:14
 */

namespace Wechat\reply;

use Wechat\WechatReplyException;

/**
 * 文本响应
 * Class TextResponseBuilder
 * @package Wechat\reply
 */
class TextResponseBuilder extends ResponseBuilder
{
    private $content;

    /**
     * @param mixed $content
     * @return TextResponseBuilder
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * 回复响应
     * @return string
     * @throws WechatReplyException
     */
    public function build(): string
    {
        if (empty($this->content)) {
            throw new WechatReplyException('回复内容不能为空');
        }
        $placeholder = <<<XML
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%d</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>
XML;

        $now = time();
        return sprintf(
            $placeholder,
            $this->request['FromUserName'],
            $this->request['ToUserName'],
            $now,
            $this->content
        );
    }
}
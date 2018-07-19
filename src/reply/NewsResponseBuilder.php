<?php
/**
 * Created by PhpStorm.
 * User: xialei
 * Date: 2018/7/19
 * Time: 上午10:32
 */

namespace Wechat\reply;

use Wechat\WechatReplyException;

/**
 * Class NewsResponseBuilder
 * @package Wechat\reply
 */
class NewsResponseBuilder extends ResponseBuilder
{
    private $news = [];

    public function addNews($title, $description, $picUrl, $url)
    {
        $this->news[] = [
            'title' => $title,
            'description' => $description,
            'pic_url' => $picUrl,
            'url' => $url
        ];
        return $this;
    }

    public function build(): string
    {
        if (empty($this->news)) {
            throw new WechatReplyException('回复内容不能为空');
        }
        if (count($this->news) > 8) {
            throw new WechatReplyException('最多8条图文消息');
        }
        $placeholder = <<<XML
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%d</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>%d</ArticleCount>
<Articles>%s</Articles>
</xml>
XML;
        $newsContent = [];
        foreach ($this->news as $news) {
            $placeholder = <<<XML
<item>
<Title><![CDATA[%s]]></Title>
<Description><![CDATA[%s]]></Description>
<PicUrl><![CDATA[%s]]></PicUrl>
<Url><![CDATA[%s]]></Url>
</item>
XML;
            $newsContent[] = sprintf($placeholder, $news['title'], $news['description'], $news['pic_url'], $news['url']);
        }
        return sprintf(
            $placeholder,
            $this->request['FromUserName'],
            $this->request['ToUserName'],
            time(),
            count($this->news),
            join("\n", $newsContent)
        );
    }
}
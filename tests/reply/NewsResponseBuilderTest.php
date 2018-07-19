<?php
/**
 * Created by PhpStorm.
 * User: xialei
 * Date: 2018/7/19
 * Time: 上午10:41
 */

namespace tests\reply;

use PHPUnit\Framework\TestCase;
use Wechat\crypto\XmlCodec;
use Wechat\reply\ImageResponseBuilder;
use Wechat\reply\MusicResponseBuilder;
use Wechat\reply\NewsResponseBuilder;

class NewsResponseBuilderTest extends TestCase
{
    public function testBuild()
    {
        $request = <<<REQUEST
<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[fromUser]]></FromUserName>
<CreateTime>1348831860</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[this is a test]]></Content>
<MsgId>1234567890123456</MsgId>
</xml>
REQUEST;
        $builder = new NewsResponseBuilder(XmlCodec::decode($request));
        $xml = $builder
            ->addNews('title1', 'description1', 'picurl1', 'url1')
            ->addNews('title2', 'description2', 'picurl2', 'url2')
            ->build();
        $xmlData = XmlCodec::decode($xml);
        $this->assertEquals('2', $xmlData['ArticleCount']);
        $this->assertEquals('title1', $xmlData['Articles']['item'][0]['Title']);
        $this->assertEquals('description1', $xmlData['Articles']['item'][0]['Description']);
        $this->assertEquals('picurl1', $xmlData['Articles']['item'][0]['PicUrl']);
        $this->assertEquals('url1', $xmlData['Articles']['item'][0]['Url']);
    }
}

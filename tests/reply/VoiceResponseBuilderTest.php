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
use Wechat\reply\TextResponseBuilder;
use Wechat\reply\VideoResponseBuilder;
use Wechat\reply\VoiceResponseBuilder;

class VoiceResponseBuilderTest extends TestCase
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
        $builder = new VoiceResponseBuilder(XmlCodec::decode($request));
        $xml = $builder
            ->setMediaId('123')
            ->build();
        $xmlData = XmlCodec::decode($xml);
        $this->assertEquals('123', $xmlData['Voice']['MediaId']);
    }
}

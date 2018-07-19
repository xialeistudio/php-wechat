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

class MusicResponseBuilderTest extends TestCase
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
        $builder = new MusicResponseBuilder(XmlCodec::decode($request));
        $xml = $builder
            ->setTitle('title')
            ->setDescription('description')
            ->setHqMusicUrl('hqMusicUrl')
            ->setMusicUrl('musicUrl')
            ->setThumbMediaId('media_id')
            ->build();
        $xmlData = XmlCodec::decode($xml);
        $this->assertEquals('title', $xmlData['Music']['Title']);
        $this->assertEquals('description', $xmlData['Music']['Description']);
        $this->assertEquals('hqMusicUrl', $xmlData['Music']['HQMusicUrl']);
        $this->assertEquals('musicUrl', $xmlData['Music']['MusicUrl']);
        $this->assertEquals('media_id', $xmlData['Music']['ThumbMediaId']);
    }
}

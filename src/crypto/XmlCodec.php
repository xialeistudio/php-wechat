<?php
/**
 * Created by PhpStorm.
 * User: xialei
 * Date: 2018/7/18
 * Time: 下午9:24
 */

namespace Wechat\crypto;

/**
 * xml编码解码器
 * Class XmlCoder
 * @package Wechat\crypto
 */
class XmlCodec
{
    /**
     * XML编码
     * @param $msg
     * @param $signature
     * @param $timestamp
     * @param $nonce
     * @return string
     */
    public static function encode($msg, $signature, $timestamp, $nonce)
    {
        $placeholder = <<<XML
<xml>
<Encrypt><![CDATA[%s]]></Encrypt>
<MsgSignature><![CDATA[%s]]></MsgSignature>
<TimeStamp>%s</TimeStamp>
<Nonce><![CDATA[%s]]></Nonce>
</xml>
XML;
        return sprintf($placeholder, $msg, $signature, $timestamp, $nonce);
    }

    /**
     * XML转换为数组
     * @param $xml
     * @return mixed
     */
    public static function decode($xml)
    {
        $simpleXMLElement = simplexml_load_string($xml, \SimpleXMLElement::class, LIBXML_NOCDATA);
        return json_decode(json_encode($simpleXMLElement, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), true);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: xialei
 * Date: 2018/7/18
 * Time: 下午8:55
 */

namespace Wechat\crypto;

/**
 * 微信XML加解密
 * Class WXBizMsgCrypt
 * @package Wechat\crypto
 */
class WXBizMsgCrypt
{
    const ERROR_NONE = 0;
    const ERROR_VALIDATE_SIGNATURE = -40001;
    const ERROR_PARSE_XML = -40002;
    const ERROR_COMPUTE_SIGNATURE = -40003;
    const ERROR_ILLEGAL_AES_KEY = -40004;
    const ERROR_APPID_INVALID = -40005;
    const ERROR_ENCRYPT_AES = -40006;
    const ERROR_DECRYPT_AES = -40007;
    const ERROR_ILLEGAL_BUFFER = -40008;
    const ERROR_BASE64_ENCODE = -40009;
    const ERROR_BASE64_DECODE = -40010;
    const ERROR_GET_RETURN_XML = -40011;

    private $token;
    private $aesKey;
    private $appid;

    /**
     * WXBizMsgCrypt constructor.
     * @param $token
     * @param $aesKey
     * @param $appid
     */
    public function __construct($token, $aesKey, $appid)
    {
        $this->token = $token;
        $this->aesKey = $aesKey;
        $this->appid = $appid;
    }

    public function getSignature($timestamp, $nonce, $msg)
    {
        $data = [$msg, $this->token, $timestamp, $nonce];
        ksort($data, SORT_ASC | SORT_STRING);
        $str = implode($data);
        return sha1($str);
    }

    /**
     * 验证链接
     * @param $msgSignature
     * @param $timestamp
     * @param $nonce
     * @param $echoStr
     * @return array
     */
    public function verifyUrl($msgSignature, $timestamp, $nonce, $echoStr)
    {
        if (strlen($this->aesKey) != 43) {
            return [self::ERROR_ILLEGAL_AES_KEY, null];
        }
        //verify msg_signature
        if ($msgSignature != $this->getSignature($timestamp, $nonce, $echoStr)) {
            return [self::ERROR_VALIDATE_SIGNATURE, null];
        }
        $pc = new Prpcrypt($this->aesKey);
        return $pc->decrypt($echoStr, $this->appid);
    }

    /**
     * 加密消息
     * @param $msg
     * @return array
     */
    public function encrypt($msg)
    {
        $timestamp = time();
        $nonce = uniqid();
        $pc = new Prpcrypt($this->aesKey);
        list($errcode, $encrypted) = $pc->decrypt($msg, $this->appid);
        if ($errcode !== self::ERROR_NONE) {
            return [$errcode, null];
        }
        $signature = $this->getSignature($timestamp, $nonce, $encrypted);
        return [self::ERROR_NONE, XmlCodec::encode($encrypted, $signature, $timestamp, $nonce)];
    }

    const TAG_APPID = 'AppId';
    const TAG_TO_USERNAME = 'ToUserName';

    /**
     * 消息解码
     * @param $msgSignature
     * @param $timestamp
     * @param $nonce
     * @param $xml
     * @param $tagName
     * @return array
     */
    public function decrypt($msgSignature, $timestamp, $nonce, $xml, $tagName)
    {
        if (strlen($this->aesKey) != 43) {
            return [self::ERROR_ILLEGAL_AES_KEY, null];
        }
        $pc = new Prpcrypt($this->aesKey);
        // 解析XML
        $xmlData = XmlCodec::decode($xml);
        $encrypted = $xmlData['Encrypt'];
        $appid = $xml[$tagName];

        if ($msgSignature != $this->getSignature($timestamp, $nonce, $encrypted)) {
            return [self::ERROR_VALIDATE_SIGNATURE, null];
        }
        return $pc->decrypt($encrypted, $appid);
    }
}
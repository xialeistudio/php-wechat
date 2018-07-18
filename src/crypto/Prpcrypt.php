<?php
/**
 * Created by PhpStorm.
 * User: xialei
 * Date: 2018/7/18
 * Time: 下午8:54
 */

namespace Wechat\crypto;


use Exception;

class Prpcrypt
{
    private $key;
    private $iv;

    public function __construct($aesKey)
    {
        $this->key = base64_decode($aesKey . '=');
        $this->iv = substr($this->key, 0, 16);
    }

    /**
     * 加密
     * @param $text
     * @param $appid
     * @return array
     */
    public function encrypt($text, $appid)
    {
        try {
            //拼接
            $text = $this->getRandomStr() . pack('N', strlen($text)) . $text . $appid;
            //添加PKCS#7填充
            $text = PKCS7Codec::encode($text);
            //加密
            $encrypted = openssl_encrypt($text, 'AES-256-CBC', $this->key, OPENSSL_ZERO_PADDING, $this->iv);
            return [WXBizMsgCrypt::ERROR_NONE, $encrypted];
        } catch (Exception $e) {
            return [WXBizMsgCrypt::ERROR_ENCRYPT_AES, null];
        }
    }

    /**
     * 解密
     * @param $encrypted
     * @param $appid
     * @return array
     */
    public function decrypt($encrypted, $appid)
    {
        try {
            //解密
            $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $this->key, OPENSSL_ZERO_PADDING, $this->iv);
        } catch (Exception $e) {
            return [WXBizMsgCrypt::ERROR_DECRYPT_AES, null];
        }
        try {
            //删除PKCS#7填充
            $result = PKCS7Codec::decode($decrypted);
            if (strlen($result) < 16) {
                return [WXBizMsgCrypt::ERROR_ILLEGAL_BUFFER, null];
            }
            //拆分
            $content = substr($result, 16, strlen($result));
            $len_list = unpack('N', substr($content, 0, 4));
            $xml_len = $len_list[1];
            $xml_content = substr($content, 4, $xml_len);
            $fromAppid = substr($content, $xml_len + 4);
        } catch (Exception $e) {
            return [WXBizMsgCrypt::ERROR_ILLEGAL_BUFFER, null];
        }
        if ($fromAppid != $appid) {
            return [WXBizMsgCrypt::ERROR_APPID_INVALID, null];
        }
        return [0, $xml_content];
    }

    private function getRandomStr()
    {
        $str = '';
        $str_pol = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyl';
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }
        return $str;
    }
}
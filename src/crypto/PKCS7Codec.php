<?php
/**
 * Created by PhpStorm.
 * User: xialei
 * Date: 2018/7/18
 * Time: 下午8:52
 */

namespace Wechat\crypto;


class PKCS7Codec
{
    public static $blockSize = 32;

    /**
     * 编码
     * @param $text
     * @return string
     */
    public static function encode($text)
    {
        $text_length = strlen($text);
        //计算需要填充的位数
        $amount_to_pad = static::$blockSize - ($text_length % static::$blockSize);
        if ($amount_to_pad == 0) {
            $amount_to_pad = static::$blockSize;
        }
        //获得补位所用的字符
        $pad_chr = chr($amount_to_pad);
        $tmp = "";
        for ($index = 0; $index < $amount_to_pad; $index++) {
            $tmp .= $pad_chr;
        }
        return $text . $tmp;
    }

    /**
     * 解码
     * @param $text
     * @return bool|string
     */
    public static function decode($text)
    {
        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > static::$blockSize) {
            $pad = 0;
        }
        return substr($text, 0, (strlen($text) - $pad));
    }
}
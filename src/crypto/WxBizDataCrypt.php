<?php
/**
 * Created by PhpStorm.
 * User: xialei
 * Date: 2018/7/18
 * Time: 下午10:01
 */

namespace Wechat\crypto;

/**
 * 小程序解密
 * Class WxBizDataCrypt
 * @package Wechat\crypto
 */
class WxBizDataCrypt
{
    const ERROR_NONE = 0;
    const ERROR_ILLEGAL_AES_KEY = -41001;
    const ERROR_ILLEGAL_IV = -41002;
    const ERROR_ILLEGAL_BUFFER = -41003;
    const ERROR_BASE64_DECODE = -41004;

    private $appid;
    private $sessionKey;

    /**
     * WxBizDataCrypt constructor.
     * @param $appid
     * @param $sessionKey
     */
    public function __construct($appid, $sessionKey)
    {
        $this->appid = $appid;
        $this->sessionKey = $sessionKey;
    }

    /**
     * 小程序解码
     * @param $data
     * @param $iv
     * @return array
     */
    public function decrypt($data, $iv)
    {
        if (strlen($this->sessionKey) != 24) {
            return [self::ERROR_ILLEGAL_AES_KEY, null];
        }
        $aesKey = base64_decode($this->sessionKey);


        if (strlen($iv) != 24) {
            return [self::ERROR_ILLEGAL_IV, null];
        }
        $aesIV = base64_decode($iv);

        $aesCipher = base64_decode($data);

        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $dataObj = json_decode($result, true);
        if ($dataObj == NULL) {
            return [self::ERROR_ILLEGAL_BUFFER, null];
        }
        if ($dataObj['watermark']['appid'] != $this->appid) {
            return [self::ERROR_ILLEGAL_BUFFER, null];
        }
        return [self::ERROR_NONE, $dataObj];
    }
}
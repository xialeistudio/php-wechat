<?php
/**
 * User: xialei
 * Date: 2018/7/15
 */

namespace Wechat\official_account;

use Wechat\crypto\XmlCodec;
use Wechat\traits\OfficialAccountTrait;

/**
 * 微信公众平台
 * Class OfficialAccount
 *
 * @package mp
 * @see     https://mp.weixin.qq.com/wiki
 */
class OfficialAccount
{
    use OfficialAccountTrait {
        OfficialAccountTrait::__construct as private __traitConstruct;
    }
    protected $appid;
    protected $secret;

    /**
     * OfficialAccount constructor.
     *
     * @param       $appid
     * @param       $secret
     * @param $token
     * @param array $options
     */
    public function __construct($appid, $secret, $token, array $options = [])
    {
        $this->appid = $appid;
        $this->secret = $secret;
        $this->__traitConstruct($options);
    }

    /**
     * 获取AccessToken
     *
     * @return mixed|string
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140183
     * @throws \Wechat\WechatException
     */
    public function accessToken()
    {
        $response = $this->client->get('/cgi-bin/token', [
            'query' => [
                'grant_type' => 'client_credential',
                'appid' => $this->appid,
                'secret' => $this->secret
            ]
        ]);
        $data = $this->handleResponse($response);;
        return $data['access_token'];
    }

    /**
     * 通过code换取网页授权access_token
     * @param $code
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140842
     */
    public function getOauthAccessToken($code)
    {
        $response = $this->client->get('/sns/oauth2/access_token', [
            'query' => [
                'appid' => $this->appid,
                'secret' => $this->secret,
                'code' => $code,
                'grant_type' => 'authorization_code'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 刷新access_token（如果需要）
     * @param $refreshToken
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140842
     */
    public function refreshOauthAccessToken($refreshToken)
    {
        $response = $this->client->get('/sns/oauth2/refresh_token', [
            'query' => [
                'appid' => $this->appid,
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 检测被动回复的签名
     * @param $signature
     * @param $timestamp
     * @param $nonce
     * @return bool
     */
    public function checkReplySignature($signature, $timestamp, $nonce)
    {
        $data = [$timestamp, $nonce];
        sort($data, SORT_STRING | SORT_ASC);
        return $signature == sha1(implode($data));
    }

    /**
     * 微信被动回复
     * @param string $xml 微信请求XML(明文)
     * @param \Closure $callback
     * @return mixed|string
     */
    public function reply($xml, \Closure $callback)
    {
        if (!empty($_GET['echostr'])) {
            return $this->checkReplySignature($_GET['signature'], $_GET['timestamp'], $_GET['nonce']) ? $_GET['echostr'] : '';
        }
        $data = XmlCodec::decode($xml);
        $response = $callback($data);
        return $response;
    }
}
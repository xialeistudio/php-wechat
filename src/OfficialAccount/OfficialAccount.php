<?php
/**
 * User: xialei
 * Date: 2018/7/15
 */

namespace Wechat\OfficialAccount;

use GuzzleHttp\Client;
use Wechat\ResponseProcessor;

/**
 * 微信公众平台API
 * Class OfficialAccount
 * @package mp
 */
class OfficialAccount
{
    use ResponseProcessor;
    protected $appid;
    protected $secret;
    /**
     * @var Client
     */
    protected $client;

    /**
     * OfficialAccount constructor.
     * @param $appid
     * @param $secret
     * @param array $options
     */
    public function __construct($appid, $secret, array $options = [])
    {
        $this->appid = $appid;
        $this->secret = $secret;

        if (!isset($options['base_uri'])) {
            $options['base_uri'] = 'https://api.weixin.qq.com/cgi-bin/';
        }

        $this->client = new Client($options);
    }

    /**
     * 获取AccessToken
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function token()
    {
        $response = $this->client->get('token', [
            'query' => [
                'grant_type' => 'client_credential',
                'appid' => $this->appid,
                'secret' => $this->secret
            ]
        ]);
        return $this->handleResponse($response);
    }
}
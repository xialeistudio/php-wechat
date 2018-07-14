<?php
/**
 * User: xialei
 * Date: 2018/7/15
 */

namespace Wechat\OfficialAccount;

use GuzzleHttp\Client;
use Wechat\WechatProcessor;

/**
 * 微信公众平台
 * Class OfficialAccount
 * @package mp
 * @see https://mp.weixin.qq.com/wiki
 */
class OfficialAccount
{
    use WechatProcessor;
    protected $appid;
    protected $secret;
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string access_token only for tests!
     */
    private $token = null;

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
        if (!empty($options['token'])) {
            $this->token = $options['token'];
            unset($options['token']);
        }

        $this->client = new Client($options);
    }

    /**
     * 获取AccessToken
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140183
     */
    public function token()
    {
        if (isset($this->token)) {
            return $this->token;
        }
        $response = $this->client->get('token', [
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
     * 创建自定义菜单
     * @param array $menu
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141013
     */
    public function menuCreate(array $menu)
    {
        $response = $this->client->post('menu/create', [
            'query' => ['access_token' => static::token()],
            'body' => $this->jsonEncode($menu),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 查询自定义菜单
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141014
     */
    public function menuGet()
    {
        $response = $this->client->get('menu/get', [
            'query' => ['access_token' => static::token()],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 删除自定义菜单
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141015
     */
    public function menuDelete()
    {
        $response = $this->client->get('menu/delete', [
            'query' => ['access_token' => static::token()],
        ]);
        return $this->handleResponse($response);
    }

    public function menuAddConditional(array $menu)
    {
        $response = $this->client->post('menu/addconditional', [
            'query' => ['access_token' => static::token()],
            'body' => $this->jsonEncode($menu),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }
}
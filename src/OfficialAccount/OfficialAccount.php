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
            $options['base_uri'] = 'https://api.weixin.qq.com';
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
     * 创建自定义菜单
     * @param array $menu
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141013
     */
    public function menuCreate(array $menu)
    {
        $response = $this->client->post('/cgi-bin/menu/create', [
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
        $response = $this->client->get('/cgi-bin/menu/get', [
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
        $response = $this->client->get('/cgi-bin/menu/delete', [
            'query' => ['access_token' => static::token()],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 创建个性化菜单
     * @param array $menu
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1455782296#0
     */
    public function menuAddConditional(array $menu)
    {
        $response = $this->client->post('/cgi-bin/menu/addconditional', [
            'query' => ['access_token' => static::token()],
            'body' => $this->jsonEncode($menu),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 删除个性化菜单
     * @param $menuId
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1455782296#1
     */
    public function menuDelConditional($menuId)
    {
        $response = $this->client->post('/cgi-bin/menu/delconditional', [
            'query' => ['access_token' => static::token()],
            'body' => $this->jsonEncode(['menuid' => $menuId]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 测试个性化菜单匹配结果
     * @param $openid
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1455782296#2
     */
    public function menuTryMatch($openid)
    {
        $response = $this->client->post('/cgi-bin/menu/trymatch', [
            'query' => ['access_token' => static::token()],
            'body' => $this->jsonEncode(['user_id' => $openid]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取自定义菜单配置接口
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1434698695
     */
    public function getCurrentSelfMenuInfo()
    {
        $response = $this->client->get('/cgi-bin/get_current_selfmenu_info', [
            'query' => ['access_token' => static::token()],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取微信服务器IP地址
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140187
     */
    public function getCallbackIp()
    {
        $response = $this->client->get('/cgi-bin/getcallbackip', [
            'query' => ['access_token' => static::token()],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 添加客服账号
     * @param array $account
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140547
     */
    public function kfAccountAdd(array $account)
    {
        $response = $this->client->post('/customservice/kfaccount/add', [
            'query' => ['access_token' => static::token()],
            'body' => $this->jsonEncode($account),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 修改客服账号
     * @param array $account
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140547
     */
    public function kfAccountUpdate(array $account)
    {
        $response = $this->client->post('/customservice/kfaccount/update', [
            'query' => ['access_token' => static::token()],
            'body' => $this->jsonEncode($account),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 删除客服账号
     * @param array $account
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140547
     */
    public function kfAccountDel(array $account)
    {
        $response = $this->client->post('/customservice/kfaccount/del', [
            'query' => ['access_token' => static::token()],
            'body' => $this->jsonEncode($account),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 设置客服帐号的头像
     * @param $account
     * @param $filename
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140547
     */
    public function kfAccountUploadHeadImg($account, $filename)
    {
        $response = $this->client->post('/customservice/kfaccount/uploadheadimg', [
            'query' => ['access_token' => static::token(), 'kf_account' => $account],
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($filename, 'r')
                ]
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取客服列表
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140547
     */
    public function getKfList()
    {
        $response = $this->client->get('/cgi-bin/customservice/getkflist', [
            'query' => ['access_token' => static::token()],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 发送客服消息
     * @param array $data
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140547
     */
    public function messageCustomerSend(array $data)
    {
        $response = $this->client->post('/cgi-bin/message/custom/send', [
            'query' => ['access_token' => static::token()],
            'body' => $this->jsonEncode($data),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    const CUSTOMER_TYPING = 'Typing';
    const CUSTOMER_CANCEL_TYPING = 'CancelTyping';

    /**
     * 客服输入状态
     * @param $openid
     * @param $command
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140547
     */
    public function messageCustomerTyping($openid, $command)
    {
        $response = $this->client->post('/cgi-bin/message/custom/typing', [
            'query' => ['access_token' => static::token()],
            'body' => $this->jsonEncode(['touser' => $openid, 'command' => $command]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }
}
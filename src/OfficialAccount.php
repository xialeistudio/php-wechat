<?php
/**
 * User: xialei
 * Date: 2018/7/15
 */

namespace Wechat;

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
     * @var string access_token only for tests!
     */
    private $accessToken = null;

    /**
     * OfficialAccount constructor.
     *
     * @param       $appid
     * @param       $secret
     * @param array $options
     */
    public function __construct($appid, $secret, array $options = [])
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
     * @throws WechatException
     */
    public function accessToken()
    {
        if (!empty($this->accessToken)) {
            return $this->accessToken;
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
}
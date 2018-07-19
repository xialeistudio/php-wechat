<?php
/**
 * Created by PhpStorm.
 * User: xialei
 * Date: 2018/7/18
 * Time: 下午4:26
 */

namespace Wechat\open;


use GuzzleHttp\Client;
use Wechat\WechatProcessor;

/**
 * 开放平台
 * Class OpenWechat
 * @package Wechat\Open
 */
abstract class OpenWechat
{
    use WechatProcessor;
    protected $appid;
    protected $secret;
    protected $aesKey;
    protected $token;
    /**
     * @var Client
     */
    protected $client;

    /**
     * OpenWechat constructor.
     * @param $appid
     * @param $secret
     * @param $aesKey
     * @param $token
     * @param array $options
     */
    public function __construct($appid, $secret, $aesKey, $token, array $options = [])
    {
        $this->appid = $appid;
        $this->secret = $secret;
        $this->aesKey = $aesKey;
        $this->token = $token;
        if (!isset($options['base_uri'])) {
            $options['base_uri'] = 'https://api.weixin.qq.com';
        }
    }

    const AUTH_OFFICIAL_ACCOUNT = 1;
    const AUTH_APPLET = 2;
    const AUTH_BOTH = 3;

    /**
     * 建议从缓存读取
     * @return string
     */
    abstract public function getTicket();

    /**
     * @param $appid
     * @return string
     */
    abstract public function getAuthorizerAccessToken($appid);

    /**
     * 构造PC绑定链接
     * @param $callback
     * @param $authType
     * @param null $bizAppid
     * @return string
     * @see https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1453779503&token=&lang=zh_CN
     * @throws \Wechat\WechatException
     */
    public function makeWebBindUrl($callback, $authType, $bizAppid = null)
    {
        $params = [
            'component_appid' => $this->appid,
            'pre_auth_code' => static::getComponentPreAuthCode(),
            'redirect_uri' => $callback,
            'auth_type' => $authType,
        ];
        if (!empty($params)) {
            $params['biz_appid'] = $bizAppid;
        }

        return 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?' . http_build_query($params);
    }

    /**
     * 构造移动版授权接入链接
     * @param $callback
     * @param $authType
     * @param null $bizAppid
     * @return string
     * @throws \Wechat\WechatException
     */
    public function makeMobileBindUrl($callback, $authType, $bizAppid = null)
    {
        $params = [
            'action' => 'bindcomponent',
            'auth_type' => $authType,
            'no_scan' => 1,
            'component_appid' => $this->appid,
            'pre_auth_code' => static::getComponentPreAuthCode(),
            'redirect_uri' => $callback,
        ];
        if (!empty($bizAppid)) {
            $params['biz_appid'] = $bizAppid;
        }
        return 'https://mp.weixin.qq.com/safe/bindcomponent?' . http_build_query($params) . '#wechat_redirect';
    }

    /**
     * 获取第三方平台component_access_token
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1453779503&token=&lang=zh_CN
     */
    public function getComponentAccessToken()
    {
        $response = $this->client->post('/cgi-bin/component/api_component_token', [
            'body' => $this->jsonEncode([
                'component_appid' => $this->appid,
                'component_appsecret' => $this->secret,
                'component_verify_ticket' => static::getTicket(),
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $data = $this->handleResponse($response);
        return $data['component_access_token'];
    }

    /**
     * 获取预授权码
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function getComponentPreAuthCode()
    {
        $response = $this->client->post('/cgi-bin/component/api_create_preauthcode', [
            'query' => ['component_access_token' => static::getComponentAccessToken()],
            'body' => $this->jsonEncode(['component_appid' => $this->appid]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $data = $this->handleResponse($response);
        return $data['pre_auth_code'];
    }

    /**
     * 使用授权码换取公众号或小程序的接口调用凭据和授权信息
     * @param $code
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function getAuthorizationInfo($code)
    {
        $response = $this->client->post('/cgi-bin/component/api_query_auth', [
            'query' => ['component_access_token' => static::getComponentAccessToken()],
            'body' => $this->jsonEncode(['component_appid' => $this->appid, 'authorization_code' => $code]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取（刷新）授权公众号或小程序的接口调用凭据（令牌）
     * @param $appid
     * @param $refreshToken
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function refreshAuthorizerToken($appid, $refreshToken)
    {
        $response = $this->client->post('/cgi-bin/component/api_authorizer_token', [
            'query' => ['component_access_token' => static::getComponentAccessToken()],
            'body' => $this->jsonEncode([
                'component_appid' => $this->appid,
                'authorizer_appid' => $appid,
                'authorizer_refresh_token' => $refreshToken
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取授权方的帐号基本信息
     * @param $appid
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function getAuthorizerInfo($appid)
    {
        $response = $this->client->post('/cgi-bin/component/api_get_authorizer_info', [
            'query' => ['component_access_token' => static::getComponentAccessToken()],
            'body' => $this->jsonEncode([
                'component_appid' => $this->appid,
                'authorizer_appid' => $appid,
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取授权方的选项设置信息
     * @param $appid
     * @param $option
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function getAuthorizerOption($appid, $option)
    {
        $response = $this->client->post('/cgi-bin/component/api_get_authorizer_option', [
            'query' => ['component_access_token' => static::getComponentAccessToken()],
            'body' => $this->jsonEncode([
                'component_appid' => $this->appid,
                'authorizer_appid' => $appid,
                'option_name' => $option
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 设置授权方的选项信息
     * @param $appid
     * @param $option
     * @param $value
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function setAuthorizerOption($appid, $option, $value)
    {
        $response = $this->client->post('/cgi-bin/component/api_set_authorizer_option', [
            'query' => ['component_access_token' => static::getComponentAccessToken()],
            'body' => $this->jsonEncode([
                'component_appid' => $this->appid,
                'authorizer_appid' => $appid,
                'option_name' => $option,
                'option_value' => $value
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 清零API次数
     * @param $appid
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function clearQuota($appid)
    {
        $response = $this->client->post('/cgi-bin/clear_quota', [
            'query' => ['access_token' => static::getAuthorizerAccessToken($appid)],
            'body' => $this->jsonEncode(['appid' => $appid]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    const SCOPE_BASE = 'snsapi_base';
    const SCOPE_USERINFO = 'snsapi_userinfo';

    /**
     * 构造网页授权链接
     * @param $appid
     * @param $callback
     * @param $scope
     * @param $state
     * @return string
     * @see https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419318590&token=&lang=zh_CN
     */
    public function buildOauthUrl($appid, $callback, $scope, $state)
    {
        $params = [
            'appid' => $appid,
            'redirect_uri' => $callback,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => $state,
            'component_appid' => $this->appid
        ];
        return 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query($params) . '#wechat_redirect';
    }

    /**
     * 获取网页授权accessToken
     * @param $appid
     * @param $code
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function getOauthAccessToken($appid, $code)
    {
        $response = $this->client->get('/sns/oauth2/component/access_token', [
            'query' => [
                'appid' => $appid,
                'code' => $code,
                'grant_type' => 'authorization_code',
                'component_appid' => $this->appid,
                'component_access_token' => static::getComponentAccessToken()
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 刷新授权方AccessToken
     * @param $appid
     * @param $refreshToken
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function refreshOauthAccessToken($appid, $refreshToken)
    {
        $response = $this->client->get('/sns/oauth2/component/refresh_token', [
            'query' => [
                'appid' => $appid,
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'component_appid' => $this->appid,
                'component_access_token' => static::getComponentAccessToken(),
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取授权用户信息
     * @param $accessToken
     * @param $openid
     * @param string $lang
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function getOauthUserInfo($accessToken, $openid, $lang = 'zh_CN')
    {
        $response = $this->client->get('/sns/userinfo', [
            'query' => [
                'access_token' => $accessToken,
                'openid' => $openid,
                'lang' => $lang
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 设置小程序服务器域名
     * @param $appid
     * @param array $request
     * @param array $wsRequest
     * @param array $upload
     * @param array $download
     * @param string $action
     * @return mixed
     * @throws \Wechat\WechatException
     * @see https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1489138143_WPbOO&token=&lang=zh_CN
     */
    public function wxaModifyDomain($appid, array $request, array $wsRequest, array $upload, array $download, $action = 'add')
    {
        $response = $this->client->post('/wxa/modify_domain', [
            'query' => ['access_token' => static::getAuthorizerAccessToken($appid)],
            'body' => $this->jsonEncode([
                'action' => $action,
                'requestdomain' => $request,
                'wsrequestdomain' => $wsRequest,
                'uploaddomain' => $upload,
                'downloaddomain' => $download
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 设置小程序业务域名（仅供第三方代小程序调用）
     * @param $appid
     * @param array $domains
     * @param string $action
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1489138143_WPbOO&token=&lang=zh_CN
     */
    public function wxaSetWebviewDomain($appid, array $domains, $action = 'add')
    {
        $response = $this->client->post('/wxa/setwebviewdomain', [
            'query' => ['access_token' => static::getAuthorizerAccessToken($appid)],
            'body' => $this->jsonEncode([
                'action' => $action,
                'webviewdomain' => $domains
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 上传小程序代码
     * @param $appid
     * @param $templateId
     * @param array $ext
     * @param $version
     * @param $desc
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1489140610_Uavc4&token=&lang=zh_CN
     */
    public function wxaCommit($appid, $templateId, array $ext, $version, $desc)
    {
        $response = $this->client->post('/wxa/commit', [
            'query' => ['access_token' => static::getAuthorizerAccessToken($appid)],
            'body' => $this->jsonEncode([
                'template_id' => $templateId,
                'ext_json' => $this->jsonEncode($ext),
                'user_version' => $version,
                'desc' => $desc
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 体验二维码
     * @param $appid
     * @param $path
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function wxaGetQrcode($appid, $path)
    {
        $response = $this->client->get('/wxa/get_qrcode', [
            'query' => [
                'access_token' => static::getAuthorizerAccessToken($appid),
                'path' => $path
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取授权小程序帐号的可选类目
     * @param $appid
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function wxaGetCategory($appid)
    {
        $response = $this->client->get('/wxa/get_category', [
            'query' => [
                'access_token' => static::getAuthorizerAccessToken($appid),
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取小程序的第三方提交代码的页面配置（仅供第三方开发者代小程序调用）
     * @param $appid
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function wxaGetPage($appid)
    {
        $response = $this->client->get('/wxa/get_page', [
            'query' => [
                'access_token' => static::getAuthorizerAccessToken($appid),
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 小程序提审
     * @param $appid
     * @param array $pages
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function wxaSubmitAudit($appid, array $pages)
    {
        $response = $this->client->post('/wxa/submit_audit', [
            'query' => ['access_token' => static::getAuthorizerAccessToken($appid)],
            'body' => $this->jsonEncode(['item_list' => $pages]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 查询某个指定版本的审核状态（仅供第三方代小程序调用）
     * @param $appid
     * @param $auditId
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function wxaGetAuditStatus($appid, $auditId)
    {
        $response = $this->client->post('/wxa/get_auditstatus', [
            'query' => ['access_token' => static::getAuthorizerAccessToken($appid)],
            'body' => $this->jsonEncode(['auditid' => $auditId]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 查询最新一次提交的审核状态（仅供第三方代小程序调用）
     * @param $appid
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function wxaGetLatestAuditStatus($appid)
    {
        $response = $this->client->get('/wxa/get_latest_auditstatus', [
            'query' => ['access_token' => static::getAuthorizerAccessToken($appid)],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 发布已通过审核的小程序（仅供第三方代小程序调用）
     * @param $appid
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function wxaRelease($appid)
    {
        $response = $this->client->get('/wxa/release', [
            'query' => ['access_token' => static::getAuthorizerAccessToken($appid)],
        ]);
        return $this->handleResponse($response);
    }

    const VISIBLE_OPEN = 'open';
    const VISIBLE_CLOSE = 'close';

    /**
     * 修改小程序线上代码的可见状态（仅供第三方代小程序调用）
     * @param $appid
     * @param $status
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function wxaChangeVisible($appid, $status)
    {
        $response = $this->client->post('/wxa/change_visitstatus', [
            'query' => ['access_token' => static::getAuthorizerAccessToken($appid)],
            'body' => $this->jsonEncode(['action' => $status]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 小程序版本回退（仅供第三方代小程序调用）
     * @param $appid
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function wxaRevert($appid)
    {
        $response = $this->client->get('/wxa/revertcoderelease', [
            'query' => ['access_token' => static::getAuthorizerAccessToken($appid)],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 查询当前设置的最低基础库版本及各版本用户占比 （仅供第三方代小程序调用）
     * @param $appid
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function getWeAppSupportVersion($appid)
    {
        $response = $this->client->get('/cgi-bin/wxopen/getweappsupportversion', [
            'query' => ['access_token' => static::getAuthorizerAccessToken($appid)],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 设置最低基础库版本（仅供第三方代小程序调用）
     * @param $appid
     * @param $version
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function setWeAppSupportVersion($appid, $version)
    {
        $response = $this->client->post('/wxa/setweappsupportversion', [
            'query' => ['access_token' => static::getAuthorizerAccessToken($appid)],
            'body' => $this->jsonEncode(['version' => $version]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 小程序撤回审核
     * @param $appid
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function wxaUndoAudit($appid)
    {
        $response = $this->client->get('/cgi-bin/wxopen/getweappsupportversion', [
            'query' => ['access_token' => static::getAuthorizerAccessToken($appid)],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 分阶段发布接口
     * @param $appid
     * @param $percentage
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function wxaGrayRelease($appid, $percentage)
    {
        $response = $this->client->post('/wxa/grayrelease', [
            'query' => ['access_token' => static::getAuthorizerAccessToken($appid)],
            'body' => $this->jsonEncode(['gray_percentage' => $percentage]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 取消分阶段发布
     * @param $appid
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function wxaRevertGrayRelease($appid)
    {
        $response = $this->client->get('/wxa/revertgrayrelease', [
            'query' => ['access_token' => static::getAuthorizerAccessToken($appid)],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 查询当前分阶段发布详情
     * @param $appid
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function wxaGetGrayReleasePlan($appid)
    {
        $response = $this->client->get('/wxa/getgrayreleaseplan', [
            'query' => ['access_token' => static::getAuthorizerAccessToken($appid)],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取草稿箱内的所有临时代码草稿
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function wxaGetTemplateDrafts()
    {
        $response = $this->client->get('/wxa/gettemplatedraftlist', [
            'query' => ['access_token' => static::getComponentAccessToken()],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取代码模版库中的所有小程序代码模版
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function wxaGetTemplates()
    {
        $response = $this->client->get('/wxa/gettemplatelist', [
            'query' => ['access_token' => static::getComponentAccessToken()],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 将草稿箱的草稿选为小程序代码模版
     * @param $draftId
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function wxaAddToTemplate($draftId)
    {
        $response = $this->client->post('/wxa/addtotemplate', [
            'query' => ['access_token' => static::getComponentAccessToken()],
            'body' => $this->jsonEncode(['draft_id' => $draftId]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $data = $this->handleResponse($response);
        return $data;
    }

    /**
     * 删除指定小程序代码模版
     * @param $templateId
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function wxaDeleteTemplate($templateId)
    {
        $response = $this->client->post('/wxa/deletetemplate', [
            'query' => ['access_token' => static::getComponentAccessToken()],
            'body' => $this->jsonEncode(['template_id' => $templateId]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $data = $this->handleResponse($response);
        return $data;
    }

    /**
     * code 换取 session_key
     * @param $appid
     * @param $code
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function getSessionByJsCode($appid, $code)
    {
        $response = $this->client->get('/sns/component/jscode2session', [
            'query' => [
                'appid' => $appid,
                'js_code' => $code,
                'grant_type' => 'authorization_code',
                'component_appid' => $this->appid,
                'component_access_token' => static::getComponentAccessToken(),
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取小程序模板库标题列表
     * @param $appid
     * @param int $offset
     * @param int $count
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function getTemplateLibrary($appid, $offset = 0, $count = 10)
    {
        $response = $this->client->post('/cgi-bin/wxopen/template/library/list', [
            'query' => ['access_token' => static::getAuthorizerAccessToken($appid)],
            'body' => $this->jsonEncode(['offset' => $offset, 'count' => $count]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取模板库某个模板标题下关键词库
     * @param $appid
     * @param $shortId
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function getTemplateKeyword($appid, $shortId)
    {
        $response = $this->client->post('/cgi-bin/wxopen/template/library/get', [
            'query' => ['access_token' => static::getAuthorizerAccessToken($appid)],
            'body' => $this->jsonEncode(['id' => $shortId]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 组合模板并添加至帐号下的个人模板库
     * @param $appid
     * @param $shortId
     * @param array $keywords
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function addTemplate($appid, $shortId, array $keywords)
    {
        $response = $this->client->post('/cgi-bin/wxopen/template/library/get', [
            'query' => ['access_token' => static::getAuthorizerAccessToken($appid)],
            'body' => $this->jsonEncode(['id' => $shortId, 'keyword_id_list' => $keywords]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取帐号下已存在的模板列表
     * @param $appid
     * @param int $offset
     * @param int $count
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function getTemplates($appid, $offset = 0, $count = 10)
    {
        $response = $this->client->post('/cgi-bin/wxopen/template/list', [
            'query' => ['access_token' => static::getAuthorizerAccessToken($appid)],
            'body' => $this->jsonEncode(['offset' => $offset, 'count' => $count]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 删除帐号下的某个模板
     * @param $appid
     * @param $templateId
     * @return mixed|string
     * @throws \Wechat\WechatException
     */
    public function delTemplate($appid, $templateId)
    {
        $response = $this->client->post('/cgi-bin/wxopen/template/del', [
            'query' => ['access_token' => static::getAuthorizerAccessToken($appid)],
            'body' => $this->jsonEncode(['template_id' => $templateId]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }
}
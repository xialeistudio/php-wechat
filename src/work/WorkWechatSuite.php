<?php
/**
 * Created by PhpStorm.
 * User: xialei
 * Date: 2018/7/18
 * Time: 下午6:57
 */

namespace Wechat\work;


use GuzzleHttp\Client;
use Wechat\WechatException;
use Wechat\WechatProcessor;

abstract class WorkWechatSuite
{
    use WechatProcessor;
    protected $name;
    protected $suiteId;
    protected $secret;
    protected $token;
    protected $aesKey;
    /**
     * @var Client
     */
    protected $client;

    /**
     * WorkWechatSuite constructor.
     * @param $name
     * @param $suiteId
     * @param $secret
     * @param $token
     * @param $aesKey
     * @param array $options
     */
    public function __construct($name, $suiteId, $secret, $token, $aesKey, array $options = [])
    {
        $this->name = $name;
        $this->suiteId = $suiteId;
        $this->secret = $secret;
        $this->token = $token;
        $this->aesKey = $aesKey;
        if (!isset($options['base_uri'])) {
            $options['base_uri'] = 'https://qyapi.weixin.qq.com';
        }
        $this->client = new Client($options);
    }


    /**
     * @return string
     */
    abstract public function getTicket();

    /**
     * @param $corpId
     * @return string
     */
    abstract public function getPermanentCode($corpId);

    /**
     * 获取应用ID
     * @param $corpId
     * @return mixed
     */
    abstract public function getAgentId($corpId);

    /**
     * 获取第三方应用凭证
     * @return mixed|string
     * @throws WechatException
     * @see http://work.weixin.qq.com/api/doc#10975/%E8%8E%B7%E5%8F%96%E7%AC%AC%E4%B8%89%E6%96%B9%E5%BA%94%E7%94%A8%E5%87%AD%E8%AF%81
     */
    public function getSuiteAccessToken()
    {
        $response = $this->client->post('/cgi-bin/service/get_suite_token', [
            'body' => $this->jsonEncode([
                'suite_id' => $this->suiteId,
                'suite_secret' => $this->secret,
                'suite_ticket' => static::getTicket()
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $data = $this->handleResponse($response);
        return $data['suite_access_token'];
    }

    /**
     * 获取预授权码
     * @return mixed|string
     * @throws WechatException
     */
    public function getPreAuthCode()
    {
        $response = $this->client->get('/cgi-bin/service/get_pre_auth_code', [
            'query' => ['suite_access_token' => static::getSuiteAccessToken()]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取永久授权码
     * @param $authCode
     * @return mixed|string
     * @throws WechatException
     */
    public function getPermanentCodeByCode($authCode)
    {
        $response = $this->client->post('/cgi-bin/service/get_permanent_code', [
            'query' => ['suite_access_token' => static::getSuiteAccessToken()],
            'body' => $this->jsonEncode([
                'auth_code' => $authCode
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取企业授权信息
     * @param $corpId
     * @return mixed|string
     * @throws WechatException
     */
    public function getAuthInfo($corpId)
    {
        $response = $this->client->post('/cgi-bin/service/get_permanent_code', [
            'query' => ['suite_access_token' => static::getSuiteAccessToken()],
            'body' => $this->jsonEncode([
                'auth_corpid' => $corpId,
                'permanent_code' => static::getPermanentCode($corpId)
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取公司Token
     * @param $corpId
     * @return mixed|string
     * @throws WechatException
     */
    public function getCorpAccessToken($corpId)
    {
        $response = $this->client->post('/cgi-bin/service/get_corp_token', [
            'query' => ['suite_access_token' => static::getSuiteAccessToken()],
            'body' => $this->jsonEncode([
                'auth_corpid' => $corpId,
                'permanent_code' => static::getPermanentCode($corpId)
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取管理员列表
     * @param $corpId
     * @return mixed|string
     * @throws WechatException
     */
    public function getAdminList($corpId)
    {
        $response = $this->client->post('/cgi-bin/service/get_admin_list', [
            'query' => ['suite_access_token' => static::getSuiteAccessToken()],
            'body' => $this->jsonEncode([
                'auth_corpid' => $corpId,
                'permanent_code' => static::getPermanentCode($corpId)
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    const SCOPE_BASE = 'snsapi_base';
    const SCOPE_USERINFO = 'snsapi_userinfo';
    const SCOPE_PRIVATEINFO = 'snsapi_privateinfo';

    /**
     * 构造网页授权链接
     * @param $callback
     * @param $scope
     * @param $state
     * @return string
     */
    public function buildOauthUrl($callback, $scope, $state)
    {
        $params = [
            'appid' => $this->suiteId,
            'redirect_uri' => $callback,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => $state
        ];
        return 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query($params) . '#wechat_redirect';
    }

    /**
     * 授权TOKEN
     * @param $code
     * @return mixed|string
     * @throws WechatException
     */
    public function getOauthToken($code)
    {
        $response = $this->client->get('/cgi-bin/service/getuserinfo3rd', [
            'query' => [
                'access_token' => static::getSuiteAccessToken(),
                'code' => $code
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 第三方使用user_ticket获取成员详情
     * @param $ticket
     * @return mixed|string
     * @throws WechatException
     */
    public function getOauthUserInfo($ticket)
    {
        $response = $this->client->post('/cgi-bin/service/getuserdetail3rd', [
            'query' => ['suite_access_token' => static::getSuiteAccessToken()],
            'body' => $this->jsonEncode(['user_ticket' => $ticket]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 添加成员
     * @param $corpId
     * @param array $data
     * @return mixed|string
     * @throws WechatException
     */
    public function createUser($corpId, array $data)
    {
        $response = $this->client->post('/cgi-bin/user/create', [
            'query' => ['access_token' => static::getCorpAccessToken($corpId)],
            'body' => $this->jsonEncode($data),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 读取成员
     * @param $corpId
     * @param $userId
     * @return mixed|string
     * @throws WechatException
     */
    public function getUser($corpId, $userId)
    {
        $response = $this->client->get('/cgi-bin/user/get', [
            'query' => [
                'access_token' => static::getCorpAccessToken($corpId),
                'userid' => $userId
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 创建成员
     * @param $corpId
     * @param array $data
     * @return mixed|string
     * @throws WechatException
     */
    public function updateUser($corpId, array $data)
    {
        $response = $this->client->post('/cgi-bin/user/update', [
            'query' => ['access_token' => static::getCorpAccessToken($corpId)],
            'body' => $this->jsonEncode($data),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 删除用户
     * @param $corpId
     * @param $userId
     * @return mixed|string
     * @throws WechatException
     */
    public function deleteUser($corpId, $userId)
    {
        $response = $this->client->get('/cgi-bin/user/delete', [
            'query' => [
                'access_token' => static::getCorpAccessToken($corpId),
                'userid' => $userId
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 批量删除成员
     * @param $corpId
     * @param array $userIds
     * @return mixed|string
     * @throws WechatException
     */
    public function batchDeleteUser($corpId, array $userIds)
    {
        $response = $this->client->post('/cgi-bin/user/update', [
            'query' => ['access_token' => static::getCorpAccessToken($corpId)],
            'body' => $this->jsonEncode(['useridlist' => $userIds]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取部门成员
     * @param $corpId
     * @param $departmentId
     * @param int $fetchChild
     * @return mixed|string
     * @throws WechatException
     */
    public function getUserSimpleList($corpId, $departmentId, $fetchChild = 0)
    {
        $response = $this->client->get('/cgi-bin/user/simplelist', [
            'query' => [
                'access_token' => static::getCorpAccessToken($corpId),
                'department_id' => $departmentId,
                'fetch_child' => $fetchChild,
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取详细列表
     * @param $corpId
     * @param $departmentId
     * @param int $fetchChild
     * @return mixed|string
     * @throws WechatException
     */
    public function getUserList($corpId, $departmentId, $fetchChild = 0)
    {
        $response = $this->client->get('/cgi-bin/user/list', [
            'query' => [
                'access_token' => static::getCorpAccessToken($corpId),
                'department_id' => $departmentId,
                'fetch_child' => $fetchChild,
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 创建部门
     * @param $corpId
     * @param $name
     * @param $parentId
     * @param int $id
     * @param int $order
     * @return mixed|string
     * @throws WechatException
     */
    public function createDepartment($corpId, $name, $parentId, $id = 0, $order = 0)
    {
        $response = $this->client->post('/cgi-bin/department/create', [
            'query' => ['access_token' => static::getCorpAccessToken($corpId)],
            'body' => $this->jsonEncode([
                'name' => $name,
                'parentid' => $parentId,
                'id' => $id,
                'order' => $order
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 更新部门
     * @param $corpId
     * @param $id
     * @param $name
     * @param $parentId
     * @param int $order
     * @return mixed|string
     * @throws WechatException
     */
    public function updateDepartment($corpId, $id, $name, $parentId, $order = 0)
    {
        $response = $this->client->post('/cgi-bin/department/update', [
            'query' => ['access_token' => static::getCorpAccessToken($corpId)],
            'body' => $this->jsonEncode([
                'name' => $name,
                'parentid' => $parentId,
                'id' => $id,
                'order' => $order
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 删除部门
     * @param $corpId
     * @param $id
     * @return mixed|string
     * @throws WechatException
     */
    public function deleteDepartment($corpId, $id)
    {
        $response = $this->client->get('/cgi-bin/department/delete', [
            'query' => [
                'access_token' => static::getCorpAccessToken($corpId),
                'id' => $id
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取部门列表
     * @param $corpId
     * @param int $id
     * @return mixed|string
     * @throws WechatException
     */
    public function departmentList($corpId, $id = 0)
    {
        $response = $this->client->get('/cgi-bin/department/list', [
            'query' => [
                'access_token' => static::getCorpAccessToken($corpId),
                'id' => $id
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 批量邀请
     * @param $corpId
     * @param array $data
     * @return mixed|string
     * @throws WechatException
     */
    public function batchInvite($corpId, array $data)
    {
        $response = $this->client->post('/cgi-bin/batch/invite', [
            'query' => ['access_token' => static::getCorpAccessToken($corpId)],
            'body' => $this->jsonEncode($data),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 发送应用消息
     * @param $corpId
     * @param array $data
     * @return mixed|string
     * @throws WechatException
     */
    public function sendMessage($corpId, array $data)
    {
        $data['agentid'] = $this->getAgentId($corpId);
        $response = $this->client->post('/cgi-bin/message/send', [
            'query' => ['access_token' => static::getCorpAccessToken($corpId)],
            'body' => $this->jsonEncode($data),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 上传临时素材
     * @param $corpId
     * @param $filename
     * @param $type
     * @return mixed|string
     * @throws WechatException
     */
    public function uploadMedia($corpId, $filename, $type)
    {
        $response = $this->client->post('/cgi-bin/media/upload', [
            'query' => ['access_token' => static::getCorpAccessToken($corpId), 'type' => $type],
            'multipart' => [
                [
                    'name' => 'media',
                    'contents' => fopen($filename, 'r')
                ]
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取临时素材
     * @param $corpId
     * @param $mediaId
     * @return mixed|string
     * @throws WechatException
     */
    public function getMedia($corpId, $mediaId)
    {
        $response = $this->client->get('/cgi-bin/media/get', [
            'query' => ['access_token' => static::getCorpAccessToken($corpId), 'media_id' => $mediaId]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取高清语音素材
     * @param $corpId
     * @param $mediaId
     * @return mixed|string
     * @throws WechatException
     */
    public function getMediaByJsSDK($corpId, $mediaId)
    {
        $response = $this->client->get('/cgi-bin/media/get/jssdk', [
            'query' => ['access_token' => static::getCorpAccessToken($corpId), 'media_id' => $mediaId]
        ]);
        return $this->handleResponse($response);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: xialei
 * Date: 2018/7/19
 * Time: 上午9:31
 */

namespace Wechat;


use GuzzleHttp\Client;

/**
 * 公众号混入
 * Trait OfficialAccountTrait
 * @package Wechat
 */
trait OfficialAccountTrait
{
    use WechatProcessor;
    /**
     * @var Client
     */
    protected $client;


    public function __construct(array $options = [])
    {
        if (!$this->client) {
            $this->client = new Client([
                'base_uri' => 'https://api.weixin.qq.com'
            ]);
        }
        return $this->client;
    }

    /**
     * 创建自定义菜单
     *
     * @param array $menu
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141013
     */
    public function menuCreate(array $menu, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/menu/create', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode($menu),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 查询自定义菜单
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141014
     */
    public function menuGet($accessToken)
    {
        $response = $this->client->get('/cgi-bin/menu/get', [
            'query' => ['access_token' => $accessToken],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 删除自定义菜单
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141015
     */
    public function menuDelete($accessToken)
    {
        $response = $this->client->get('/cgi-bin/menu/delete', [
            'query' => ['access_token' => $accessToken],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 创建个性化菜单
     *
     * @param array $menu
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1455782296#0
     */
    public function menuAddConditional(array $menu, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/menu/addconditional', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode($menu),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 删除个性化菜单
     *
     * @param $menuId
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1455782296#1
     */
    public function menuDelConditional($menuId, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/menu/delconditional', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode(['menuid' => $menuId]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 测试个性化菜单匹配结果
     *
     * @param $openid
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1455782296#2
     */
    public function menuTryMatch($openid, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/menu/trymatch', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode(['user_id' => $openid]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取自定义菜单配置接口
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1434698695
     */
    public function getCurrentSelfMenuInfo($accessToken)
    {
        $response = $this->client->get('/cgi-bin/get_current_selfmenu_info', [
            'query' => ['access_token' => $accessToken],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取微信服务器IP地址
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140187
     */
    public function getCallbackIp($accessToken)
    {
        $response = $this->client->get('/cgi-bin/getcallbackip', [
            'query' => ['access_token' => $accessToken],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 添加客服账号
     *
     * @param array $account
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140547
     */
    public function kfAccountAdd(array $account, $accessToken)
    {
        $response = $this->client->post('/customservice/kfaccount/add', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode($account),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 修改客服账号
     *
     * @param array $account
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140547
     */
    public function kfAccountUpdate(array $account, $accessToken)
    {
        $response = $this->client->post('/customservice/kfaccount/update', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode($account),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 删除客服账号
     *
     * @param array $account
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140547
     */
    public function kfAccountDel(array $account, $accessToken)
    {
        $response = $this->client->post('/customservice/kfaccount/del', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode($account),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 设置客服帐号的头像
     *
     * @param $account
     * @param $filename
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140547
     */
    public function kfAccountUploadHeadImg($account, $filename, $accessToken)
    {
        $response = $this->client->post('/customservice/kfaccount/uploadheadimg', [
            'query' => ['access_token' => $accessToken, 'kf_account' => $account],
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
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140547
     */
    public function getKfList($accessToken)
    {
        $response = $this->client->get('/cgi-bin/customservice/getkflist', [
            'query' => ['access_token' => $accessToken],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 发送客服消息
     *
     * @param array $data
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140547
     */
    public function messageCustomerSend(array $data, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/message/custom/send', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode($data),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 客服输入状态
     *
     * @param $openid
     * @param $command
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140547
     */
    public function messageCustomerTyping($openid, $command, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/message/custom/typing', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode(['touser' => $openid, 'command' => $command]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 设置所属行业
     *
     * @param $industryId1
     * @param $industryId2
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1433751277
     */
    public function templateSetIndustry($industryId1, $industryId2, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/template/api_set_industry', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode(['industry_id1' => $industryId1, 'industry_id2' => $industryId2]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取设置的行业信息
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1433751277
     */
    public function templateGetIndustry($accessToken)
    {
        $response = $this->client->get('/cgi-bin/template/get_industry', [
            'query' => ['access_token' => $accessToken],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获得模板ID
     *
     * @param $shortId
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1433751277
     */
    public function templateAddTemplate($shortId, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/template/api_add_template', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode(['template_id_short' => $shortId]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取模板列表
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1433751277
     */
    public function templateGetAllPrivate($accessToken)
    {
        $response = $this->client->get('/cgi-bin/template/get_all_private_template', [
            'query' => ['access_token' => $accessToken],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 删除模板
     *
     * @param $templateId
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1433751277
     */
    public function templateDelPrivate($templateId, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/template/del_private_template', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode(['template_id' => $templateId]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 发送模板消息
     *
     * @param array $data
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1433751277
     */
    public function messageSendTemplate(array $data, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/message/template/send', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode($data),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取TICKET
     *
     * @param $type
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141115
     */
    public function getTicket($type, $accessToken)
    {
        $response = $this->client->get('/cgi-bin/ticket/getticket', [
            'query' => ['access_token' => $accessToken, 'type' => $type],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 新增临时素材
     *
     * @param $filename
     * @param $type
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1444738726
     */
    public function mediaUpload($filename, $type, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/media/upload', [
            'query' => ['access_token' => $accessToken, 'type' => $type],
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
     *
     * @param $mediaId
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1444738727
     */
    public function mediaGet($mediaId, $accessToken)
    {
        $response = $this->client->get('/cgi-bin/media/get', [
            'query' => ['access_token' => $accessToken, 'media_id' => $mediaId],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 新增永久图文素材
     *
     * @param array $list
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1444738729
     */
    public function materialAddNews(array $list, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/material/add_news', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode(['articles' => $list]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 上传图文消息内的图片获取URL
     *
     * @param $filename
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1444738729
     */
    public function mediaUploadImg($filename, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/media/uploadimg', [
            'query' => ['access_token' => $accessToken],
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
     * 新增其他类型永久素材
     *
     * @param       $filename
     * @param       $type
     * @param array $description
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1444738729
     */
    public function materialAdd($filename, $type, array $description = [], $accessToken)
    {
        $data = [
            ['name' => 'media', 'contents' => fopen($filename, 'r')]
        ];
        if (!empty($description)) {
            $data[] = ['name' => 'description', 'contents' => $this->jsonEncode($description)];
        }
        $response = $this->client->post('/cgi-bin/media/uploadimg', [
            'query' => ['access_token' => $accessToken, 'type' => $type],
            'multipart' => $data
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取永久素材
     *
     * @param $mediaId
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1444738730
     */
    public function materialGet($mediaId, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/material/get_material', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode(['media_id' => $mediaId]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 删除永久素材
     *
     * @param $mediaId
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1444738731
     */
    public function materialDel($mediaId, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/material/del_material', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode(['media_id' => $mediaId]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 修改永久图文素材
     *
     * @param       $mediaId
     * @param       $index
     * @param array $news
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1444738732
     */
    public function materialUpdateNews($mediaId, $index, array $news, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/material/update_news', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode(['media_id' => $mediaId, 'index' => $index, 'articles' => $news]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取素材总数
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1444738733
     */
    public function materialCount($accessToken)
    {
        $response = $this->client->get('/cgi-bin/material/get_materialcount', [
            'query' => ['access_token' => $accessToken],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取素材列表
     *
     * @param $type
     * @param $offset
     * @param $count
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1444738734
     */
    public function materialList($type, $offset, $count, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/material/batchget_material', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode(['type' => $type, 'offset' => $offset, 'count' => $count]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 创建标签
     *
     * @param $name
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140837
     */
    public function tagCreate($name, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/tags/create', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode(['tag' => ['name' => $name]]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取公众号已创建的标签
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140837
     */
    public function tagAll($accessToken)
    {
        $response = $this->client->get('/cgi-bin/tags/get', [
            'query' => ['access_token' => $accessToken],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 编辑标签
     *
     * @param $id
     * @param $name
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140837
     */
    public function tagUpdate($id, $name, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/tags/update', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode(['tag' => ['id' => $id, 'name' => $name]]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 删除标签
     *
     * @param $id
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140837
     */
    public function tagDelete($id, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/tags/delete', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode(['tag' => ['id' => $id]]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取标签下粉丝列表
     *
     * @param      $tagId
     * @param null $nextOpenid
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140837
     */
    public function getUsersByTag($tagId, $nextOpenid, $accessToken)
    {
        $body = ['tagid' => $tagId];
        if (!empty($nextOpenid)) {
            $body['next_openid'] = $nextOpenid;
        }
        $response = $this->client->post('/cgi-bin/user/tag/get', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode($body),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 批量为用户打标签
     *
     * @param       $tagId
     * @param array $openids
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140837
     */
    public function setUsersTag($tagId, array $openids, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/tags/members/batchtagging', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode(['openid_list' => $openids, 'tagid' => $tagId]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 批量为用户取消标签
     *
     * @param       $tagId
     * @param array $openids
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140837
     */
    public function delUsersTag($tagId, array $openids, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/tags/members/batchuntagging', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode(['openid_list' => $openids, 'tagid' => $tagId]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取用户身上的标签
     *
     * @param $openid
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140837
     */
    public function getTagsByOpenid($openid, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/tags/getidlist', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode(['openid' => $openid]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 设置用户备注名
     *
     * @param $openid
     * @param $remark
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140838
     */
    public function setUserRemark($openid, $remark, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/user/info/updateremark', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode(['openid' => $openid, 'remark' => $remark]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取用户基本信息(UnionID机制)
     *
     * @param        $openid
     * @param string $lang
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140839
     */
    public function getUserInfo($openid, $lang = 'zh_CN', $accessToken)
    {
        $response = $this->client->get('/cgi-bin/user/info', [
            'query' => ['access_token' => $accessToken, 'openid' => $openid, 'lang' => $lang],
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 批量获取用户基本信息
     *
     * @param array $users
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140839
     */
    public function batchGetUserInfo(array $users, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/user/info/batchget', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode(['user_list' => $users]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取用户列表
     *
     * @param null $nextOpenid
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140840
     */
    public function getUsers($nextOpenid, $accessToken)
    {
        $query = ['access_token' => $accessToken];
        if (!empty($nextOpenid)) {
            $query['next_openid'] = $nextOpenid;
        }
        $response = $this->client->get('/cgi-bin/user/get', [
            'query' => $query,
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 构造授权链接
     *
     * @param $callback
     * @param $scope
     * @param $state
     * @param $appid
     * @return string
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140842
     */
    public function buildOauthUrl($callback, $scope, $state, $appid)
    {
        $baseUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
        $params = [
            'appid' => $appid,
            'redirect_uri' => $callback,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => $state
        ];
        return $baseUrl . http_build_query($params) . '#wechat_redirect';
    }

    /**
     * 拉取用户信息(需scope为 snsapi_userinfo)
     * @param $accessToken
     * @param $openid
     * @param string $lang
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140842
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
     * 检验授权凭证（access_token）是否有效
     * @param $accessToken
     * @param $openid
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140842
     */
    public function oauthAuth($accessToken, $openid)
    {
        $response = $this->client->get('/sns/auth', [
            'query' => [
                'access_token' => $accessToken,
                'openid' => $openid,
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 获取公众号的黑名单列表
     *
     * @param null $beginOpenid
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1471422259_pJMWA
     */
    public function getBlacklist($beginOpenid, $accessToken)
    {
        $body = [];
        if (!empty($beginOpenid)) {
            $body['begin_openid'] = $beginOpenid;
        }
        $response = $this->client->post('/cgi-bin/tags/members/getblacklist', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode($body),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 拉黑用户
     *
     * @param array $openids
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1471422259_pJMWA
     */
    public function setBlacklist(array $openids, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/tags/members/getblacklist', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode(['openid_list' => $openids]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 取消拉黑用户
     *
     * @param array $openids
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1471422259_pJMWA
     */
    public function unsetBlacklist(array $openids, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/tags/members/batchunblacklist', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode(['openid_list' => $openids]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 创建二维码
     *
     * @param array $data
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     */
    public function qrcodeCreate(array $data, $accessToken)
    {
        $response = $this->client->post('/cgi-bin/qrcode/create', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode($data),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 通过ticket换取二维码
     *
     * @param $ticket
     *
     * @return mixed|string
     * @throws \Wechat\WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1443433542
     */
    public function showQrcode($ticket)
    {
        $response = $this->client->get('https://mp.weixin.qq.com/cgi-bin/showqrcode', [
            'query' => ['ticket' => $ticket]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * 长链接转短链接接口
     *
     * @param        $url
     * @param string $action
     *
     * @param $accessToken
     * @return mixed|string
     * @throws WechatException
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1443433600
     */
    public function shortUrl($url, $action = 'long2short', $accessToken)
    {
        $response = $this->client->post('/cgi-bin/shorturl', [
            'query' => ['access_token' => $accessToken],
            'body' => $this->jsonEncode(['action' => $action, 'long_url' => $url]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        return $this->handleResponse($response);
    }

    /**
     * JSSDK签名
     *
     * @param $appid
     * @param      $url
     * @param      $ticket
     * @param bool $debug
     *
     * @return array
     */
    public function makeJsapiSignature($appid, $url, $ticket, $debug = false)
    {
        $params = [
            'noncestr' => uniqid(),
            'jsapi_ticket' => $ticket,
            'timestamp' => time(),
            'url' => $url
        ];
        ksort($params, SORT_STRING | SORT_ASC);
        $signStr = urldecode(http_build_query($params));
        $sign = sha1($signStr);
        return [
            'debug' => $debug,
            'appId' => $appid,
            'timestamp' => strval($params['timestamp']),
            'nonceStr' => $params['noncestr'],
            'signature' => $sign
        ];
    }
}
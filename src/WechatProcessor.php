<?php
/**
 * User: xialei
 * Date: 2018/7/14
 */

namespace Wechat;

use Psr\Http\Message\ResponseInterface;

/**
 * Wechat Request/Response Processor
 * Trait WechatProcessor
 * @package wechat
 */
trait WechatProcessor
{
    /**
     * 处理响应
     * @param ResponseInterface $response
     * @return mixed|string
     * @throws WechatException
     */
    protected function handleResponse(ResponseInterface $response)
    {
        if (!$this->isJsonResponse($response)) {
            return $response->getBody()->getContents();
        }
        $data = json_decode($response->getBody()->getContents(), true);
        if (!empty($data['errcode'])) {
            throw new WechatException($data['errmsg'], $data['errcode']);
        }
        return $data;
    }

    /**
     * 检测是否为JSON响应
     * @param ResponseInterface $response
     * @return bool
     */
    protected function isJsonResponse(ResponseInterface $response)
    {
        if (!$response->hasHeader('Content-Type')) {
            return false;
        }
        $types = $response->getHeader('Content-Type');
        foreach ($types as $type) {
            if (strpos($type, 'application/json') !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * JSON编码
     * @param array $data
     * @param int $options
     * @return string
     */
    protected function jsonEncode(array $data,$options = JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE){
        return json_encode($data,$options);
    }
}
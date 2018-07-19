<?php
/**
 * User: xialei
 * Date: 2018/7/15
 */

namespace Wechat\Tests;


use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Wechat\official_account\OfficialAccount;

/**
 * 公众平台单元测试
 * Class OfficialAccountTest
 *
 * @package Wechat\Tests
 */
class OfficialAccountTest extends TestCase
{
    /**
     * @var OfficialAccount
     */
    protected $officialAccount;

    protected function setUp()
    {
        $dotenv = new Dotenv(__DIR__ . '/../');
        $dotenv->load();

        $this->officialAccount = new OfficialAccount(
            getenv('OFFICIAL_ACCOUNT_APPID'),
            getenv('OFFICIAL_ACCOUNT_SECRET'),
            ''
        );
    }

    public function testToken()
    {
        $this->markTestIncomplete('微信Token刷新限制，测试已通过');
        $data = $this->officialAccount->accessToken();
        $this->assertTrue(is_string($data));
    }

    public function testTemplateSetIndustry()
    {
        $this->markTestIncomplete('微信一个月限制，测试已通过');
        $data = $this->officialAccount->templateSetIndustry(1, 2, '');
        $this->assertEquals(0, $data['errcode']);
    }

    public function testTemplateGetIndustry()
    {
        $data = $this->officialAccount->templateGetIndustry(getenv('OFFICIAL_ACCOUNT_TOKEN'));
        $this->assertArrayHasKey('primary_industry', $data);
        $this->assertArrayHasKey('secondary_industry', $data);
    }

    public function testTemplateAddTemplate()
    {
        // add
        $data = $this->officialAccount->templateAddTemplate('TM00001', getenv('OFFICIAL_ACCOUNT_TOKEN'));
        $this->assertEquals(0, $data['errcode']);
        $this->assertArrayHasKey('template_id', $data);
        $templateId = $data['template_id'];
        // sendTemplate
        $data = $this->officialAccount->messageSendTemplate([
            'touser' => getenv('OFFICIAL_ACCOUNT_OPENID'),
            'template_id' => $templateId,
            'url' => 'https://www.ddhigh.com',
            'data' => [
                'name' => [
                    'value' => 'ThinkPHP实战'
                ],
                'remark' => [
                    'value' => '谢谢您的支持'
                ]
            ]
        ], getenv('OFFICIAL_ACCOUNT_TOKEN'));
        $this->assertEquals(0, $data['errcode']);
        // delTemplate
        $data = $this->officialAccount->templateDelPrivate($templateId, getenv('OFFICIAL_ACCOUNT_TOKEN'));
        $this->assertEquals(0, $data['errcode']);
    }
}
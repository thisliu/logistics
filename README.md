<h1 align="center">Logistics</h1>

<p align="center">快递物流查询-快递查询接口组件。(多平台兼容)</p>	

 [![Build Status](https://travis-ci.org/finecho/logistics.svg?branch=master)](https://travis-ci.org/finecho/logistics)	
![StyleCI build status](https://github.styleci.io/repos/185047335/shield) 	

 ## 介绍
 
 目前支持一家平台（后续会慢慢添加）
 
 * 阿里云 [Aliyun](https://homenew.console.aliyun.com/)
 
 ## 配置	
 
 在使用本扩展之前，你需要去 [阿里云市场](https://homenew.console.aliyun.com/) 注册账号，然后购买服务，获取服务的 APP Code。	

 ## 安装	

```
$ composer require finecho/logistics -vvv	
```	

 ## 使用	

```php	
require __DIR__ .'/vendor/autoload.php';

use Finecho\Logistics\Logistics;

$config = [
    'provider' => 'aliyun',
    'aliyun' => [
        'app_code' => 'xxxxxxxx',
    ]
];

$logistics = new Logistics($config);
```	

 ###  获取物流信息	

```php	
$order = $logistics->order('XXXXX');	
```	
示例：	

```json	
{
    "code": 200,
    "msg": "OK",
    "company": "yto",
    "no": "xxxxxxxxx",
    "status": "已签收",
    "courier": "",
    "courierPhone": "",
    "list": [
        {
            "datetime": "2019-5-9 12:44:40",
            "remark": "客户 签收人: 邮件收发章 已签收 感谢使用圆通速递，期待再次为您服务"
        },
        {
            "datetime": "2019-5-9 8:37:02",
            "remark": "【湖南省长沙市火车站公司】 派件人: 李海波 派件中 派件员电话18684822604"
        },
        {
            "datetime": "2019-5-8 23:17:46",
            "remark": "【长沙转运中心】 已发出 下一站 【湖南省长沙市火车站公司】"
        },
        {
            "datetime": "2019-5-8 22:47:28",
            "remark": "【长沙转运中心】 已收入"
        },
        {
            "datetime": "2019-5-7 22:41:27",
            "remark": "【济南转运中心】 已发出 下一站 【长沙转运中心】"
        },
        {
            "datetime": "2019-5-7 22:37:27",
            "remark": "【济南转运中心】 已收入"
        },
        {
            "datetime": "2019-5-7 19:05:04",
            "remark": "【山东省淄博市淄川区三部】 已发出 下一站 【山东省淄博市公司】"
        },
        {
            "datetime": "2019-5-7 18:33:15",
            "remark": "【山东省淄博市淄川区三部公司】 已打包"
        },
        {
            "datetime": "2019-5-7 18:02:27",
            "remark": "【山东省淄博市淄川区三部公司】 已收件"
        }
    ],
    "original": {
        "status": "0",
        "msg": "ok",
        "result": {
            "number": "xxxxxxxxxx",
            "type": "yto",
            "list": [
                {
                    "time": "2019-5-9 12:44:40",
                    "status": "客户 签收人: 邮件收发章 已签收 感谢使用圆通速递，期待再次为您服务"
                },
                {
                    "time": "2019-5-9 8:37:02",
                    "status": "【湖南省长沙市火车站公司】 派件人: xxx 派件中 派件员电话xxxx"
                },
                {
                    "time": "2019-5-8 23:17:46",
                    "status": "【长沙转运中心】 已发出 下一站 【湖南省长沙市火车站公司】"
                },
                {
                    "time": "2019-5-8 22:47:28",
                    "status": "【长沙转运中心】 已收入"
                },
                {
                    "time": "2019-5-7 22:41:27",
                    "status": "【济南转运中心】 已发出 下一站 【长沙转运中心】"
                },
                {
                    "time": "2019-5-7 22:37:27",
                    "status": "【济南转运中心】 已收入"
                },
                {
                    "time": "2019-5-7 19:05:04",
                    "status": "【山东省淄博市淄川区三部】 已发出 下一站 【山东省淄博市公司】"
                },
                {
                    "time": "2019-5-7 18:33:15",
                    "status": "【山东省淄博市淄川区三部公司】 已打包"
                },
                {
                    "time": "2019-5-7 18:02:27",
                    "status": "【山东省淄博市淄川区三部公司】 已收件"
                }
            ],
            "deliverystatus": "3",
            "issign": "1",
            "expName": "圆通速递",
            "expSite": "www.yto.net.cn ",
            "expPhone": "95554",
            "logo": "http://img3.fegine.com/express/yto.jpg",
            "courier": "",
            "courierPhone": ""
        }
    }
}
```	
你也可以这样获取：

```
$order['company']; // 'zto'
$order['list']; // ....
$order['original']; // 获取接口原始返回信息
...

```

或者这样：

```
$order->getCode(); // 状态码
$order->getMsg(); // 状态信息
$order->getCompany(); // 物流公司简称
$order->getNo(); // 物流单号
$order->getStatus(); // 当前物流单详情
$order->getCourier(); // 快递员姓名
$order->getCourierPhone(); // 快递员手机号
$order->getList(); // 物流单状态详情
$order->getOriginal(); // 获取接口原始返回信息
```


### 参数说明	

```	
 string   order(string $no, string $type = null)	
```	

> - `$no` - 物流单号/比如：“805741929402797742”	
> - `$type` - 快递公司字母简写：不知道可不填 95% 能自动识别，填写查询速度会更快/比如：“zto”	
```	

### 在 Laravel 中使用	

生成配置文件
```bash
php artisan vendor:publish --provider="Finecho\Logistics\ServiceProvider"
```	

然后在 `.env` 中配置 `LOGISTICS_APP_CODE` ：	

```env	
LOGISTICS_APP_CODE=xxxxxxxxxxxxxxxxx	
```

可以用两种方式来获取 `Finecho\Logistics\Logistics;` 实例：	

#### 方法参数注入	

```php	
    .	
    .	
    .	
    public function show(Logistics $logistics, $no) 	
    {	
        $order = $logistics->order($no);
        
        return $order;	// $order->getList(); .....
    }	
    .	
    .	
    .	
```	

#### 服务名访问	

```php	
    .	
    .	
    .	
    public function show($no) 	
    {	
        $response = app('logistics')->order($no);	
    }	
    .	
    .	
   .	
 ```	

## 参考	

- [阿里云市场：全国快递物流查询-快递查询接口](https://market.aliyun.com/products/56928004/cmapi021863.html?spm=5176.2020520132.101.2.7cd87218IbLYU3#sku=yuncode1586300000)	

## License	

MIT
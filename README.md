<h1>❤️ Logistics</h1>

<p>快递物流查询-快递查询接口组件。</p>
	
[![Build Status](https://travis-ci.org/finecho/logistics.svg?branch=master)](https://travis-ci.org/finecho/logistics)	
[![Latest Stable Version](https://poser.pugx.org/finecho/logistics/v/stable)](https://packagist.org/packages/finecho/logistics)
[![Total Downloads](https://poser.pugx.org/finecho/logistics/downloads)](https://packagist.org/packages/finecho/logistics)
[![Latest Unstable Version](https://poser.pugx.org/finecho/logistics/v/unstable)](https://packagist.org/packages/finecho/logistics)
[![License](https://poser.pugx.org/finecho/logistics/license)](https://packagist.org/packages/finecho/logistics)

## 介绍
 
 目前已支持四家平台
 
 * [阿里云](https://homenew.console.aliyun.com/)
 * [聚合数据](https://www.juhe.cn/docs/api/id/43)
 * [快递100](https://www.kuaidi100.com/)
 * [快递鸟](https://www.kdniao.com/)

## 安装	

```
$ composer require finecho/logistics -vvv	
```	

## 使用	

```php	
require __DIR__ .'/vendor/autoload.php';

use Finecho\Logistics\Logistics;

$config = [
    'provider' => 'aliyun', // aliyun/juhe/kd100
    
    'aliyun' => [
        'app_code' => 'xxxxxxxx',
    ],
    
    'juhe' => [
         'app_code' => 'xxxxx',
    ],
    
    'kdniao' => [
             'app_code' => 'xxxxx',
             'customer' => 'xxxxx',
    ],
        
    'kd100' => [
         'app_code' => 'xxxxx',
         'customer' => 'xxxxx',
    ],
];

$logistics = new Logistics($config);
```	

###  获取物流公司列表
 
```php	
$companies = $logistics->companies();	
```	

示例:

```json
[
    "顺丰",
    "申通",
    "中通",
    "圆通"
]
```

###  获取物流信息	

```php	
$order = $logistics->query('805741929402797742', '圆通');
```

示例：	

```json	
{
    "code": 200,
    "msg": "OK",
    "company": "圆通",
    "no": "805741929402797742",
    "status": 4,
    "display_status": "已签收",
    "abstract_status": {
        "has_active" : true,
        "has_ended" : true,
        "has_signed" : true,
        "has_troubled" : false,
        "has_returned" : false
    },
    "list": [
        {
            "datetime": "2019-05-07 18:02:27",
            "remark": "山东省淄博市淄川区三部公司取件人:司健（15553333300）已收件",
            "zone": ""
        },
        {
            "datetime": "2019-05-09 12:44:40",
            "remark": "快件已签收签收人:邮件收发章感谢使用圆通速递，期待再次为您服务",
            "zone": ""
        }
    ],
    "original": {
        "resultcode": "200",
        "reason": "成功的返回",
        "result": {
            "company": "圆通",
            "com": "yt",
            "no": "805741929402797742",
            "status": "1",
            "status_detail": null,
            "list": [
                {
                    "datetime": "2019-05-07 18:02:27",
                    "remark": "山东省淄博市淄川区三部公司取件人:司健（15553333300）已收件",
                    "zone": ""
                },
                {
                    "datetime": "2019-05-09 12:44:40",
                    "remark": "快件已签收签收人:邮件收发章感谢使用圆通速递，期待再次为您服务",
                    "zone": ""
                }
            ]
        },
        "error_code": 0
    }
}
```	
你也可以这样获取：

```
$order['company']; // '圆通' （因为各个平台对公司的名称有所不一致，所以查询结果或许会有些许差别）
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
$order->getStatus(); // 当前物流单状态 

// 注：物流状态可能不一定准确

$order->getDisplayStatus(); // 当前物流单状态展示名
$order->getAbstractStatus(); // 当前抽象物流单状态
$order->getCourier(); // 快递员姓名
$order->getCourierPhone(); // 快递员手机号
$order->getList(); // 物流单状态详情
$order->getOriginal(); // 获取接口原始返回信息
```


### 参数说明	

```	
 string   order(string $no, string $company = null)	
```
> - `$no` - 物流单号	
> - `$company` - 快递公司名（通过 $companies = $logistics->companies(); 获取)

* `aliyun` ：`$company` 可选
* `juhe` : `$company` 必填
* `kd100` : `$company` 可选（建议必填，不填查询结果不一定准确）
* `kdniao` : `$company` 可选（建议必填，不填查询结果不一定准确）

### 在 Laravel 中使用	

生成配置文件

```PHP
php artisan vendor:publish --provider="Finecho\Logistics\ServiceProvider"
```

然后在 `.env` 中配置 `LOGISTICS_APP_CODE` ，同时修改 config/logistics.php 中配置信息：	

```env	
LOGISTICS_APP_CODE=xxxxxxxxxxxxxxxxx	
LOGISTICS_CUSTOMER=xxxxxxxxxxxxxxxxx	// 快递100 customer
```

可以用两种方式来获取 `Finecho\Logistics\Logistics;` 实例：	

#### 方法参数注入	

```php	
    .	
    .	
    .	
    public function show(Logistics $logistics, $no, $company) 	
    {	
        $order = $logistics->query($no, $company);
        
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
    public function show($no, $company) 	
    {	
        $response = app('logistics')->query($no, $company);	
    }	
    .	
    .	
    .
 ```	

## 参考	

- [PHP 扩展包实战教程 - 从入门到发布](https://learnku.com/courses/creating-package)	
- [overtrue/easy-sms](https://github.com/overtrue/easy-sms)	
- [icecho/easy-ip](https://github.com/icecho/easy-ip)	

## 感谢

- 感谢 [超哥](https://github.com/overtrue) 和 [icecho](https://github.com/icecho) 为我第一个扩展包的悉心指导。

## License	

MIT

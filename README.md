<h1 align="center">Logistics</h1>

<p align="center">快递物流查询-快递查询接口组件。(多平台兼容)</p>	

 [![Build Status](https://travis-ci.org/finecho/logistics.svg?branch=master)](https://travis-ci.org/finecho/logistics)	
![StyleCI build status](https://github.styleci.io/repos/185047335/shield) 	

## 介绍
 
 目前支持两家平台（后续会慢慢添加）
 
 * 阿里云 [Aliyun](https://homenew.console.aliyun.com/)
 * 聚合数据 [Juhe](https://www.juhe.cn/docs/api/id/43)

## 安装	

```
$ composer require finecho/logistics -vvv	
```	

 ## 使用	

```php	
require __DIR__ .'/vendor/autoload.php';

use Finecho\Logistics\Logistics;

$config = [
    'provider' => 'aliyun', // 'juhe'
    'aliyun' => [ // 'juhe'
        'app_code' => 'xxxxxxxx',
    ]
];

$logistics = new Logistics($config);
```	

 ### 获取物流公司
 
 $companies = $logistics->companies();
 
 示例：
 ```JSON
[
    "顺丰",
    "申通",
    "中通快递",
    "圆通快递"
]
 ```

###  获取物流信息	

```php	
$order = $logistics->order('805741929402797742', '圆通快递');
```	
示例：	

```json	
{
    "code": 200,
    "msg": "OK",
    "company": "圆通快递",
    "no": "805741929402797742",
    "status": "无信息",
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
$order['company']; // '圆通快递'
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
 string   order(string $no, string $company = null)	

```
> - `$no` - 物流单号	
> - `$company` - 快递公司名（通过 $companies = $logistics->companies(); 获取)

* aliyun ：$company 可选
* juhe : $company 必填

### 在 Laravel 中使用	

生成配置文件

```PHP
php artisan vendor:publish --provider="Finecho\Logistics\ServiceProvider"
```

然后在 `.env` 中配置 `LOGISTICS_APP_CODE` ，同时修改 config/logistics.php 中配置信息：	

```env	
LOGISTICS_APP_CODE=xxxxxxxxxxxxxxxxx	
```

可以用两种方式来获取 `Finecho\Logistics\Logistics;` 实例：	

#### 方法参数注入	

```php	
    .	
    .	
    .	
    public function show(Logistics $logistics, $no, $company) 	
    {	
        $order = $logistics->order($no, $company);
        
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
        $response = app('logistics')->order($no, $company);	
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

- 感谢[超哥](https://github.com/overtrue)和[icecho](https://github.com/icecho)为我第一个扩展包的悉心指导。

## License	

MIT
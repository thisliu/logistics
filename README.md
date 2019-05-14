
<h1 align="center">Logistics-inquiry</h1>

<p align="center">基于阿里云市场的 PHP 全国快递物流查询-快递查询接口组件。</p>

## 安装

```sh
$ composer require finecho/logistics-inquiry -vvv
```

## 配置

在使用本扩展之前，你需要去 [阿里云市场](https://homenew.console.aliyun.com/) 注册账号，然后购买服务，获取服务的 APP Code。


## 使用

```php
use Finecho\LogisticsInquiry\Logistics;

$appCode = 'xxxxxxxxxxxxxxxxxxxxxxxxxxx';

$logistics = new Logistics($appCode);
```

###  获取物流信息

```php
$response = $logistics->getLogisticsInfo('XXXXX');
```
示例：

```json
{
    "status": "0",
    "msg": "ok",
    "result": {
        "number": "********",
        "type": "yto",
        "list": [
            {
                "time": "2019-5-9 12:44:40",
                "status": "客户 签收人: 邮件收发章 已签收 感谢使用圆通速递，期待再次为您服务"
            },
            {
                "time": "2019-5-9 8:37:02",
                "status": "【湖南省长沙市火车站公司】 派件人: *** 派件中 派件员电话 ********"
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
```

### 获取物流公司信息

```
$response = $logistics->getLogisticsCompany('zto');
```
示例：

```json
{
    "status": "200",
    "msg": "sucess",
    "result": {
        "ZTO": "中通快递"
    }
}
```

```
$response = $logistics->getLogisticsCompany('ALL');
```
示例：

```json
{
    "status": "200",
    "msg": "sucess",
    "result": {
        "AAEWEB": "AAE",
        "ARAMEX": "Aramex",
        "DHL": "DHL国内件",
        "DHL_EN": "DHL国际件",
        "DPEX": "DPEX",
        "DEXP": "D速",
        "EMS": "EMS(国内和国际)",
        "EWE": "EWE",
        "FEDEX": "FEDEX",
        "FEDEXIN": "FedEx国际",
        "PCA": "PCA",
        "TNT": "TNT",
        "UPS": "UPS",
        ...
    }
}
```

### 参数说明

```
array | string   getLogisticsInfo(string $node, string $type = null)
```

> - `$node` - 物流单号/比如：“805741929402797742”
> - `$type` - 快递公司字母简写：不知道可不填 95% 能自动识别，填写查询速度会更快/比如：“zto”

```
array | string   getLogisticsCompany(string $type = 'ALL')
```

> - `$type` - 快递编码 或 不填写获取列表/比如：“zto” 或者（type：ALL）；


### 在 Laravel 中使用

在 Laravel 中使用也是同样的安装方式，配置写在 `config/services.php` 中：

```php
    .
    .
    .
    'logistics' => [
            'app_code' => env('LOGISTICS_APP_CODE'),
    ]
```

然后在 `.env` 中配置 `WEATHER_API_KEY` ：

```env
LOGISTICS_APP_CODE=xxxxxxxxxxxxxxxxx
```

可以用两种方式来获取 `Finecho\LogisticsInquiry\Logistics;` 实例：

#### 方法参数注入

```php
    .
    .
    .
    public function show(Logistics $logistics) 
    {
        $response = $logistics->getLogisticsInfo('xxxxx');
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
    public function show() 
    {
        $response = app('logistics')->getLogisticsInfo('xxxxx');
    }
    .
    .
    .

```

## 参考

- [阿里云市场：全国快递物流查询-快递查询接口](https://market.aliyun.com/products/56928004/cmapi021863.html?spm=5176.2020520132.101.2.7cd87218IbLYU3#sku=yuncode1586300000)

## License

MIT

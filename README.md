# 阿里云 语音转文字 SDK

**安装**

```shell
composer require guoguo882010/aliyun-speech
```

**使用**

```php
$config = [
        'access_key_id' => 'key id',

        'access_key_secret' => 'key secret',
        
        // 地域
        'region_id'       => 'cn-shanghai',
    
        //域名
        'endpoint'          => 'speechfiletranscriberlite.cn-shanghai.aliyuncs.com',
      
        // 获取Appkey请前往控制台：https://nls-portal.console.aliyun.com/applist
        'appkey'      => 'appkey', 
    ];

$speech = new \RSHDSDK\ALiYunSpeech\SpeechFacade($config);

//提交转文字任务，返回数组包含任务ID和请求ID
$speech->submitTask('音频地址（可下载的）','回调通知地址');

//主动查询任务结果，返回数据 包含完成时间和翻译文字句子结果
$speech->searchTask('任务ID');

或者

use \RSHDSDK\ALiYunSpeech;

//提交转文字任务，返回数组包含任务ID和请求ID
SpeechFacade::instance($config)->submitTask('音频地址（可下载的）','回调通知地址');

//主动查询任务结果 ，返回数据 包含完成时间和翻译文字句子结果
SpeechFacade::instance($config)->searchTask('任务ID');


```
<?php

namespace RSHDSDK\ALiYunSpeech;

use AlibabaCloud\SDK\SpeechFileTranscriberLite\V20211221\Models\GetTaskResultRequest;
use AlibabaCloud\SDK\SpeechFileTranscriberLite\V20211221\Models\SubmitTaskRequest;
use AlibabaCloud\SDK\SpeechFileTranscriberLite\V20211221\SpeechFileTranscriberLite;
use Darabonba\OpenApi\Models\Config;
use Exception;

class SpeechFacade
{


    private $config;

    private $client;
    private $appkey;

    public function __construct($config)
    {
        if (empty($config)) {
            throw new Exception('配置参数不能为空');
        }
        $access_key_id     = $config['access_key_id'] ?? '';
        $access_key_secret = $config['access_key_secret'] ?? '';
        $region_id         = $config['region_id'] ?? '';
        $endpoint          = $config['endpoint'] ?? '';
        $app_key           = $config['appkey'] ?? '';

        if (empty($access_key_id)) {
            throw new Exception('秘钥 access_key_id 不能为空');
        }

        if (empty($access_key_secret)) {
            throw new Exception('秘钥密码 access_key_secret 不能为空');
        }

        if (empty($region_id)) {
            throw new Exception('区域 region_id 不能为空');
        }

        if (empty($endpoint)) {
            throw new Exception('服务接入点 endpoint 不能为空');
        }
        if (empty($app_key)) {
            throw new Exception(' appkey 不能为空');
        }

        $this->config             = $config;
        $this->appkey             = $app_key;
        $this->config['endpoint'] = strtolower($config['endpoint']);//转换为小写

        $ali_config                  = new Config();
        $ali_config->accessKeyId     = $this->config ['access_key_id'];      //获取AccessKey ID和AccessKey Secret请前往控制台：https://ram.console.aliyun.com/manage/ak
        $ali_config->accessKeySecret = $this->config ['access_key_secret'];
        $ali_config->regionId        = $this->config ['region_id'];
        $ali_config->endpoint        = $this->config ['endpoint'];
        $this->client                = new SpeechFileTranscriberLite($ali_config);

    }

    public static function instance($config)
    {
        return new static($config);
    }


    /**
     * 提交任务
     * @param string $audio 文件地址
     * @param string $call_back 回调地址
     * @return array 任务ID 和请求ID
     * @throws Exception
     */
    public function submitTask(string $audio, string $call_back): array
    {
        if (empty($audio)) {
            throw new Exception('音频文件地址不能为空');
        }
        if (empty($call_back)) {
            throw new Exception('回调地址不能为空');
        }
        $task                                = [];
        $task['appkey']                      = $this->appkey;
        $task['file_link']                   = $audio;
        $task['version']                     = '4.0';
        $task['enable_words']                = false;
        $task['auto_split']                  = true;
        $task['enable_sample_rate_adaptive'] = true;
        $task['enable_callback']             = true;
        $task['callback_url']                = $call_back;

        $taskJson = json_encode($task);

        $request       = new SubmitTaskRequest();
        $request->task = $taskJson;
        try {
            $submitResponse = $this->client->submitTask($request);

            if (21050000 == $submitResponse->body->statusCode) {
                return ['task_id' => $submitResponse->body->taskId, 'request_id' => $submitResponse->body->requestId];
            } else {
                throw new Exception('异常：' . $submitResponse->body->statusCode . ', ' . $submitResponse->body->statusText);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 查询任务结果
     * @param string $task_id 任务ID
     * @return array
     * @throws Exception
     */
    public function searchTask(string $task_id): array
    {
        try {
            if (empty($task_id)) {
                throw new Exception('任务ID不能为空');
            }

            $request         = new GetTaskResultRequest();
            $request->taskId = $task_id;
            $getResponse     = $this->client->getTaskResult($request);
            if (21050000 == $getResponse->body->statusCode) {
                return ['SolveTime' => $getResponse->body->solveTime, 'Sentences' => $getResponse->body->result->sentences];
            } else {
                throw new Exception('异常：' . $getResponse->body->statusCode . ', ' . $getResponse->body->statusText);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


}
<?php
namespace App\Common;

use JPush\Client as JPush;
use Log;

class Push {
    const PUSH_BORROW_BATTERY = 1; // 借电池
    const PUSH_POPUP_SLOT = 2; // 弹开槽位

    public static function push($targetId, $event, $eventMsg) {
        self::jpush($targetId, $event, $eventMsg);
    }

    public static function jpush($targetId, $event, $eventMsg) {
        $client = new JPush(env('JPUSH_APP_KEY'), env('JPUSH_APP_SECRET'));
        // 完整的推送示例
        // 这只是使用样例,不应该直接用于实际生产环境中 !!
        try {
            $response = $client->push()
                ->setPlatform(['android'])
                // 一般情况下，关于 audience 的设置只需要调用 addAlias、addTag、addTagAnd  或 addRegistrationId
                // 这四个方法中的某一个即可，这里仅作为示例，当然全部调用也可以，多项 audience 调用表示其结果的交集
                // 即是说一般情况下，下面三个方法和没有列出的 addTagAnd 一共四个，只适用一个便可满足大多数的场景需求
                ->addAlias($targetId)
                ->message('message content', array(
                    'title' => $event,
                    // 'content_type' => 'text',
                    'extras' => $eventMsg,
                ))
                ->send();
                Log::debug(print_r($response, true));
        } catch (\JPush\Exceptions\APIConnectionException $e) {
            Log::error(print_r($response, $e));
        } catch (\JPush\Exceptions\APIRequestException $e) {
            Log::error(print_r($response, $e));
        }
    }
}

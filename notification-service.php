<?php

namespace services\notification;

use App\Table_Mappers\Notifications\PushNotificationTable;
use Illuminate\Http\Request;
use paid_api\notification\Message;
use paid_api\notification\ModuleName;
use paid_api\notification\SeenAt;
use paid_api\notification\Subject;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NotificationService
{

    public function notify(int $userId, Message $message, Subject $subject, ModuleName $moduleName, string $url)
    {
        $createdAt = new \DateTime();
        $pushNotificationTable = new PushNotificationTable();
        $pushNotificationTable->id_user = $userId;
        $pushNotificationTable->message = $message->value();
        $pushNotificationTable->module_name = $moduleName->value();
        $pushNotificationTable->subject = $subject->value();
        $pushNotificationTable->url = $url;
        $pushNotificationTable->seen_at = '';
        $pushNotificationTable->created_at = $createdAt;
        $pushNotificationTable->save();
    }

    public function getNotifications(string $language,int $userId)
    {
        if($language == 'en-us') {
            $response = new StreamedResponse(function () use ($userId) {
                while (true) {
                    $this->pushMessage($step = $userId, json_encode($this->getUserNotification($userId), JSON_UNESCAPED_UNICODE), 'userNotification');
                    sleep(3);
                }
            });
            $response->headers->set('Content-Type', 'text/event-stream');
            $response->headers->set('Cache-Control', 'no-cache');
            $response->send();
        }
        else
        {
            return $this->getUserNotification($userId);
        }

    }

    private function pushMessage($id, $msg, $event)
    {
        echo "id: $id" . PHP_EOL;
        if (!empty($event)) {
            echo "event: $event" . PHP_EOL;
        }
        echo "data: $msg" . PHP_EOL . PHP_EOL;

        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
    }

    public function getUserNotification(int $userId)
    {
        $notificationData = [];
        $notificationTable = PushNotificationTable::where('id_user', $userId)->exists();
        if ($notificationTable) {
            $pushNotificationTable = PushNotificationTable::where(['id_user' => $userId,'seen_at' => ''])->get();
            $notificationCount = PushNotificationTable::where(['id_user' => $userId,'seen_at' => ''])->count();
            array_push($notificationData, ['notificationData' => $pushNotificationTable], ['notificationCount' => $notificationCount]);
            return $notificationData;
        } else {
            return ['status' => false];
        }
    }

    public function updateNotificationCount(Request $request)
    {
        sleep(10);
        $pushNotificationTable = PushNotificationTable::where(['id_user' => $request->userId, 'seen_at' => ''])->get();
        for ($j = 0; $j < count($pushNotificationTable); $j++) {
            $notificationSeenAt = new \DateTimeImmutable();
            $seenAt = new SeenAt($notificationSeenAt);
            $pushNotificationTable[$j]->seen_at = $seenAt->value();
            $pushNotificationTable[$j]->update();
        }
        return json_encode(true);
    }
    public function getAllUserNotifications(string $language,int $userId):array
    {
        $notificationDetails = [];
        if($language == 'en-us') {
            $pushNotificationDetail = PushNotificationTable::where('id_user',$userId)->get();
            for($i=0 ; $i<count($pushNotificationDetail);$i++)
            {
                if(!empty($pushNotificationDetail[$i]['seen_at'])) {
                    array_push($notificationDetails,$pushNotificationDetail[$i]);
                    }
            }
            return $notificationDetails;
        }

    }

}

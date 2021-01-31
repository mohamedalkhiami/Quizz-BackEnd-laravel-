<?php

namespace App;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

use App\Fcmtoken;

/* 

 $downstreamResponse->numberSuccess();
 $downstreamResponse->numberFailure();
 $downstreamResponse->numberModification();

 //return Array - you must remove all this tokens in your database
 $downstreamResponse->tokensToDelete();

 //return Array (key : oldToken, value : new token - you must change the token in your database )
 $downstreamResponse->tokensToModify();

 //return Array - you should try to resend the message to the tokens in the array
 $downstreamResponse->tokensToRetry();

 // return Array (key:token, value:errror) - in production you should remove from your database the tokens present in this array
 $downstreamResponse->tokensWithError(); 
 
   
*/

class FcmHelper {

    public  static function  sendDownstreamMessageToDevice($token, $title, $message) {
        
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);
        
        $notificationBuilder = new PayloadNotificationBuilder($title);
        $notificationBuilder->setBody($body)
                    ->setSound('default');
        

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['a_data' => 'my_data']);
        
        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

        return $downstreamResponse;
    }

    public static function sendDownstreamMessageToDevices($tokens, $title, $message) {
        
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);
        
        $notificationBuilder = new PayloadNotificationBuilder($title);

        $notificationBuilder->setBody($message)
                    ->setSound('default');
        

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['a_data' => 'my_data']);
        
        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);

        return $downstreamResponse;
    }

    public static function getFCMTokens() {
        $fcmTokens = [];
        $tokens = Fcmtoken::all();
        foreach ($tokens as $token) {
            $fcmTokens[] = $token->fcm_token;
        }
        return $fcmTokens;
    }

    public static function sendPushNotificationQuizPub() {
        $tokens = FcmHelper::getFCMTokens();
        if(count($tokens) == 0) {
            throw new Exception("No FCM Tokens found!");
        }
        try{
            return FcmHelper::sendDownstreamMessageToDevices($tokens, '[QUIZ APP]', 'Hello! New Quiz published, you have to do it.');
        }catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function sendPushNotificationQuizResPub() {
        $tokens = FcmHelper::getFCMTokens();
        if(count($tokens) == 0) {
            throw new Exception("No FCM Tokens found!");
        }
        try{
            return FcmHelper::sendDownstreamMessageToDevices($tokens, '[QUIZ APP]', 'Hello! Quiz Results are out, you can check them.');
        }catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

}
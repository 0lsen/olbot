<?php

namespace OLBot;


use OLBot\Model\DB\LogError;
use OLBot\Model\DB\LogMessageIn;
use OLBot\Model\DB\LogMessageOut;
use Telegram\Model\Message;
use Telegram\Model\SendMessageBody;
use Telegram\Model\SendPhotoLinkBody;
use Telegram\ObjectSerializer;

class Logger
{
    public static function logError($idIn, \Throwable $t)
    {
        LogError::create([
            'id_in' => $idIn,
            'message' => $t->getCode() . ' - ' . $t->getMessage() . ' [' . $t->getFile() . '::' . $t->getLine() . "] \n" . $t->getTraceAsString()
        ]);
    }

    public static function messageInAlreadyLogged(Message $message)
    {
        return LogMessageIn::where(['id_in' => $message->getMessageId()])->count() > 0;
    }

    public static function logMessageIn(Message $message)
    {
        LogMessageIn::create([
            'id_in' => $message->getMessageId(),
            'content' => json_encode(ObjectSerializer::sanitizeForSerialization($message))
        ]);
    }

    /**
     * @param SendMessageBody|SendPhotoLinkBody $message
     */
    public static function logMessageOut($message)
    {
        LogMessageOut::create([
            'id_in' => $message->getReplyToMessageId(),
            'content' => json_encode(ObjectSerializer::sanitizeForSerialization($message))
        ]);
    }
}
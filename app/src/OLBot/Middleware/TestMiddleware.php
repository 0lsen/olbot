<?php

namespace OLBot\Middleware;


use Mockery;
use Slim\Http\Request;
use Slim\Http\Response;
use Telegram\Model\Message;
use Telegram\ObjectSerializer;

class TestMiddleware extends TextBasedMiddleware
{
    private $logs = [];

    public function __invoke(Request $request, Response $response, $next)
    {
        $loggerMock = Mockery::mock('alias:OLBot\Logger');
        $loggerMock
            ->shouldReceive('logError')
            ->andReturnUsing(function($idIn, \Throwable $t){
                $this->logs[] = 'Error: ' . $idIn . ': '.$t->getMessage();
                return null;
            });
        $loggerMock
            ->shouldReceive('logMessageIn')
            ->andReturnUsing(function(Message $message) {
                $this->logs[] = 'In: ' . $message->getText();
                return null;
            });
        $loggerMock
            ->shouldReceive('logMessageOut')
            ->andReturnUsing(function($message){
                $this->logs[] = 'Out: ' . json_encode(ObjectSerializer::sanitizeForSerialization($message));
                return null;
            });

        $response = $next($request, $response);

        $content = [
            'logs' => $this->logs,
            'storage' => [
                'karma' => $this->storageService->karma,
                'response' => $this->storageService->response,
                'sendResponse' => $this->storageService->sendResponse,
                'subjectCandidates' => $this->storageService->subjectCandidates
            ]
        ];

        return $response->withJson($content);
    }

    private function addLine($message, &$text)
    {
        if ($text) $text .= "\n";
        $text .= $message;
    }
}
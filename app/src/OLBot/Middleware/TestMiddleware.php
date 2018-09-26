<?php

namespace OLBot\Middleware;


use Mockery;
use Slim\Http\Request;
use Slim\Http\Response;
use Swagger\Client\ObjectSerializer;
use Swagger\Client\Telegram\Message;

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
            'logs' => $this->logs
        ];

        if ($this->storageService->sendResponse) {

            if (sizeof($this->storageService->response->text) || !is_null($this->storageService->karma)) {
                $text = '';

                foreach ($this->storageService->response->text as $message) {
                    $this->addLine($message, $text);
                }

                if (!is_null($this->storageService->karma)) {
                    $this->addLine(ucwords($this->storageService->karma->text), $text);
                }

                $content['text'] = $text;
            }

            if (sizeof($this->storageService->response->pics)) {
                $content['pics'] = [];
                foreach ($this->storageService->response->pics as $pic) {
                    $content['pics'][] = $pic;
                }
            }
        }

        return $response->withJson($content);
    }

    private function addLine($message, &$text)
    {
        if ($text) $text .= "\n";
        $text .= $message;
    }
}
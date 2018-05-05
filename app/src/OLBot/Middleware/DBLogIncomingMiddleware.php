<?php

namespace OLBot\Middleware;

use OLBot\DB;
use Slim\Http\Request;
use Slim\Http\Response;

class DBLogIncomingMiddleware {
    public function __invoke(Request $request, Response $response, $next) {
        $db = DB::getConnection();
        $response->getBody()->write($db->testFunction());
        return $next($request, $response);
    }
}
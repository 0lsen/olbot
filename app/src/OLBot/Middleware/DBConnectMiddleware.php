<?php

namespace OLBot\Middleware;

use OLBot\DB;
use Slim\Http\Request;
use Slim\Http\Response;

class DBConnectMiddleware {
    public function __invoke(Request $request, Response $response, $next) {
        $settings = require PROJECT_ROOT.'/config/database.php';
        DB::connect($settings);
        $response->getBody()->write(" - DB Connection established");
        return $next($request, $response);
    }
}
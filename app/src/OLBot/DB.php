<?php

namespace OLBot;

class DB extends \PDO {

    static $instance;

    static function connect($settings) {
        if (!self::$instance) {
            if (!isset(
                $settings['type'],
                $settings['dbname'],
                $settings['host'],
                $settings['user'],
                $settings['pass']
            )) {
                throw new \PDOException('insufficient database connection data');
            }

            $dsn = $settings['type'] . ':dbname=' . $settings['dbname'] . ';host=' . $settings['host'];
            self::$instance = new DB($dsn, $settings['user'], $settings['pass']);
        }
    }

    static function getConnection() : DB {
        if (!self::$instance) {
            throw new \PDOException('no database instance found');
        }
        return self::$instance;
    }

    function testFunction() {
        return " - DB Test Function";
    }
}
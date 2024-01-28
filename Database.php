<?php

namespace FpDbTest;

use Exception;
use mysqli;

class Database implements DatabaseInterface
{
    private mysqli $mysqli;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function buildQuery(string $query, array $args = []): string
    {
        $params = [];

        foreach ($args as $arg) {
            if (is_array($arg)) {
                $params[] = $this->processArrayParam($arg);
            } elseif ($arg === null) {
                $params[] = 'NULL';
            } elseif ($arg === true) {
                $params[] = '1';
            } elseif ($arg === false) {
                $params[] = '0';
            } else {
                $params[] = $this->mysqli->real_escape_string($arg);
            }
        }

        return vsprintf($query, $params);
    }

    public function skip()
    {
        return '__SKIP__';
    }

    private function processArrayParam(array $array): string
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result[] = $this->processArrayParam([$key => $value]);
            } elseif ($value === null) {
                $result[] = 'NULL';
            } else {
                $result[] = $this->mysqli->real_escape_string($value);
            }
        }

        return implode(', ', $result);
    }
}

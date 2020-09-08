<?php

declare (strict_types = 1);

namespace phpyii\utils;

/**
 * Description of Requests
 * 请求类
 * @author 最初的梦想
 */
class Request {

    /**
     * 获取所有请求头
     * Get all HTTP header key/values as an associative array for the current request.
     * from https://github.com/ralouphie/getallheaders
     * @return string[string] The HTTP header key/value pairs.
     */
    public static function headers($name = '', $default = '') {
        $headers = [];

        $copy_server = [
            'CONTENT_TYPE' => 'Content-Type',
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_MD5' => 'Content-Md5',
        ];

        foreach ($_SERVER as $key => $value) {
            if (in_array(substr($key, 0, 5), ['HTTP_', 'http_'])) {
                $key = substr($key, 5);
                if ($key) {
                    $keyUpper = strtoupper($key);
                    if (!isset($copy_server[$key]) || !isset($_SERVER[$key])) {
                        if (!isset($copy_server[$keyUpper]) || !isset($_SERVER[$keyUpper])) {
                            $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                            $headers[$key] = $value;
                        }
                    }
                }
            } elseif (isset($copy_server[$key])) {
                $headers[$copy_server[$key]] = $value;
            }
        }

        if (!isset($headers['Authorization'])) {
            if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
                $basic_pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
                $headers['Authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $basic_pass);
            } elseif (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
            }
        }
        if (!empty($name)) {
            return isset($headers[$name]) ? $headers[$name] : $default;
        }
        return $headers;
    }

}

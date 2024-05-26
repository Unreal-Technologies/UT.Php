<?php

namespace UT_Php_Core\Routing;

class Router implements IRouter
{
    private const DEFAULT_HTACCESS = 'UmV3cml0ZUVuZ2luZSBPbg0KUm'
            . 'V3cml0ZUNvbmQgJXtSRVFVRVNUX0ZJTEVOQU1FfSAhLWYNClJ'
            . 'ld3JpdGVDb25kICV7UkVRVUVTVF9GSUxFTkFNRX0gIS1kDQpS'
            . 'ZXdyaXRlUnVsZSBeKC4qKSQgaW5kZXgucGhwLyQxIFtMXQ';

    /**
     * @var array
     */
    private array $routes = [];

    /**
     * @var \UT_Php_Core\IO\IDirectory
     */
    private \UT_Php_Core\IO\IDirectory $root;

    /**
     * @var bool
     */
    private bool $caseInsensitive = false;

    /**
     * @param \UT_Php_Core\IO\IDirectory $root
     * @param bool $caseInsensitive
     */
    public function __construct(\UT_Php_Core\IO\IDirectory $root, bool $caseInsensitive = false)
    {
        $this -> root = $root;
        $this -> caseInsensitive = $caseInsensitive;

        $htaccessFile = \UT_Php_Core\IO\File::fromString('.htaccess');
        if (!$htaccessFile -> exists()) {
            $htaccessFile -> write(base64_decode(self::DEFAULT_HTACCESS));
        }
    }

    /**
     * @return \UT_Php_Core\IO\IDirectory
     */
    public function root(): \UT_Php_Core\IO\IDirectory
    {
        return $this -> root;
    }

    /**
     * @param string $method
     * @param string $url
     * @param \Closure $target
     * @return void
     */
    public function add(RequestMethods $method, string $url, \Closure $target): void
    {
        $m = strtoupper($method -> name);

        $this -> routes[$m][$url] = $target;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function match(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $url = $_SERVER['REQUEST_URI'];

        if (isset($this -> routes[$method])) {
            foreach ($this -> routes[$method] as $routeUrl => $target) {
                $pattern = preg_replace('/\/:([^\/]+)/', '/(?P<$1>[^/]+)', $routeUrl);
                $matches = [];

                if (preg_match('#^' . $pattern . '$#' . ($this -> caseInsensitive ? 'i' : ''), $url, $matches)) {
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                    $target($params);
                    exit;
                }
            }
        }

        header('HTTP/1.1 404 Not Found');
        exit;
    }
}

<?php

namespace UT_Php_Core\IO;

class Directory implements IDirectory
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var bool
     */
    private $exists;

    /**
     * @var resource
     */
    private $handler;

    /**
     * @var IDiskManager[]
     */
    private static $ram;

    /**
     * @param  string $dir
     * @return Directory
     */
    public static function fromString(string $dir): Directory
    {
        return new Directory($dir);
    }

    /**
     * @return bool
     */
    public function remove(): bool
    {
        if (!$this -> exists) {
            return false;
        }
        return rmdir($this -> path);
    }

    /**
     * @param IDirectory $dir
     * @param string $name
     * @return Directory|null
     */
    public static function fromDirectory(IDirectory $dir, string $name): ?Directory
    {
        if (!$dir -> exists()) {
            return null;
        }
        return self::fromString($dir -> path() . '\\' . $name);
    }

    public function parent(): Directory
    {
        $parts = preg_split('/[\/|\\\]+/', $this -> path);
        $buffer = array_slice($parts, 0, count($parts) - 1);

        return self::fromString(implode('/', $buffer));
    }

    /**
     * @param  string $regex
     * @return bool
     */
    public function contains(string $regex): bool
    {
        foreach ($this -> list() as $iDiskManager) {
            if (preg_match($regex, $iDiskManager -> name())) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        $segments = explode('\\', $this -> path);
        if (count($segments) === 0) {
            $segments = explode('/', $this -> path);
        }

        return $segments[count($segments) - 1];
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return $this -> path;
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return $this -> exists;
    }

    /**
     * @return bool
     */
    public function create(): bool
    {
        if (!$this -> exists()) {
            mkdir($this -> path, 0777);
            $this -> path = realpath($this -> path);
            $this -> exists = true;
            return true;
        }
        return false;
    }

    /**
     * @param  bool $refresh
     * @return IDiskManager[]
     */
    public function list(string $regex = null, bool $refresh = false): array
    {
        $key = $this -> path;
        if (isset(self::$ram[$key]) && !$refresh) {
            return self::$ram[$key];
        }

        $output = [];
        try {
            if (@$this -> open()) {
                $out = null;
                while ($this -> read($out) !== false) {
                    if ($out === '.' || $out === '..') {
                        continue;
                    }
                    if ($regex !== null && !preg_match($regex, $out)) {
                        continue;
                    }

                    $path = $this -> path . '\\' . $out;

                    if (is_dir($path)) {
                        $output[] = self::fromString($path);
                    } else {
                        $output[] = File::fromString($path);
                    }
                }
                $this -> close();
            }
        } catch (Exception $ex) {
        }

        self::$ram[$key] = $output;

        return $output;
    }

    /**
     * @param  string|null $out
     * @return bool
     */
    public function read(?string &$out): bool
    {
        $out = readdir($this -> handler);
        if ($out === false) {
            $out = null;
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function open(): bool
    {
        if ($this -> handler !== null || !$this -> exists) {
            return false;
        }
        $this -> handler = opendir($this -> path);
        if ($this -> handler === false) {
            $this -> handler = null;
            return false;
        }
        return true;
    }

    /**
     * @return void
     */
    public function close(): void
    {
        if ($this -> handler !== null) {
            closedir($this -> handler);
        }
    }

    /**
     * @param  string $dir
     * @throws \Exception
     */
    private function __construct(string $dir)
    {
        if (self::$ram === null) {
            self::$ram = [];
        }
        $this -> path = $dir;
        $this -> exists = file_exists($dir);
        if ($this -> exists) {
            $this -> path = realpath($dir);
            if (!is_dir($this -> path)) {
                throw new \Exception($this -> path . ' is not a ' . get_class($this));
            }
        }
    }
}

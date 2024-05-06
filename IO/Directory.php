<?php

namespace UT_Php\IO;

class Directory implements IDiskManager
{
    /**
     * @var string
     */
    private $path_;

    /**
     * @var bool
     */
    private $exists_;

    /**
     * @var resource
     */
    private $handler_;

    /**
     * @var IDiskManager[]
     */
    private static $ram_;

    /**
     * @param  string $dir
     * @return Directory
     */
    public static function fromString(string $dir): Directory
    {
        return new Directory($dir);
    }

    /**
     * @param  Directory $dir
     * @param  string    $name
     * @return Directory|null
     */
    public static function fromDirectory(Directory $dir, string $name): ?Directory
    {
        if (!$dir -> exists()) {
            return null;
        }
        return self::fromString($dir -> path() . '\\' . $name);
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
        $segments = explode('\\', $this -> path_);
        if (count($segments) === 0) {
            $segments = explode('/', $this -> path_);
        }

        return $segments[count($segments) - 1];
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return $this -> path_;
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return $this -> exists_;
    }

    /**
     * @return bool
     */
    public function create(): bool
    {
        if (!$this -> exists()) {
            mkdir($this -> path_, 0777);
            $this -> path_ = realpath($this -> path_);
            $this -> exists_ = true;
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
        $key = $this -> path_;
        if (isset(self::$ram_[$key]) && !$refresh) {
            return self::$ram_[$key];
        }

        $output = [];
        if ($this -> open()) {
            $out = null;
            while ($this -> read($out) !== false) {
                if ($out === '.' || $out === '..') {
                    continue;
                }
                if ($regex !== null && !preg_match($regex, $out)) {
                    continue;
                }

                $path = $this -> path_ . '\\' . $out;

                if (is_dir($path)) {
                    $output[] = self::fromString($path);
                } else {
                    $output[] = File::fromString($path);
                }
            }
            $this -> close();
        }

        self::$ram_[$key] = $output;

        return $output;
    }

    /**
     * @param  string|null $out
     * @return bool
     */
    public function read(?string &$out): bool
    {
        $out = readdir($this -> handler_);
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
        if ($this -> handler_ !== null || !$this -> exists_) {
            return false;
        }
        $this -> handler_ = opendir($this -> path_);
        if ($this -> handler_ === false) {
            $this -> handler_ = null;
            return false;
        }
        return true;
    }

    /**
     * @return void
     */
    public function close(): void
    {
        if ($this -> handler_ !== null) {
            closedir($this -> handler_);
        }
    }

    /**
     * @param  string $dir
     * @throws \Exception
     */
    private function __construct(string $dir)
    {
        if (self::$ram_ === null) {
            self::$ram_ = [];
        }
        $this -> path_ = $dir;
        $this -> exists_ = file_exists($dir);
        if ($this -> exists_) {
            $this -> path_ = realpath($dir);
            if (!is_dir($this -> path_)) {
                throw new \Exception($this -> path_ . ' is not a ' . get_class($this));
            }
        }
    }
}

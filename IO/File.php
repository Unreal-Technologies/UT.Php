<?php

namespace UT_Php\IO;

class File implements IDiskManager
{
    private $path_;
    private $exists_;

    /**
     * @param  string $path
     * @return File
     */
    public static function fromString(string $path): File
    {
        return new File($path);
    }

    /**
     * @param  Directory $dir
     * @return string|null
     * @throws \Exception
     */
    public function relativeTo(Directory $dir): ?string
    {
        if (stristr($this -> path_, $dir -> path())) {
            return substr($this -> path_, strlen($dir -> path()) + 1);
        }

        throw new \Exception('Not implemented');
        echo '<xmp>';
        print_r($this);
        print_r($dir);
        echo '</xmp>';
    }

    /**
     * @param  Directory $dir
     * @param  string    $name
     * @return bool
     */
    public function copyTo(Directory $dir, string $name = null): bool
    {
        if (!$dir -> exists()) {
            return false;
        }
        if ($name === null) {
            $name = $this -> name();
        }
        return copy($this -> path(), $dir -> path() . '/' . $name);
    }

    /**
     * @return Directory|null
     */
    public function parent(): ?Directory
    {
        if (!$this -> exists()) {
            return null;
        }
        $parts = explode('\\', $this -> path_);
        unset($parts[count($parts) - 1]);
        if (count($parts) === 0) {
            return null;
        }
        $new = implode('\\', $parts);
        return Directory::fromString($new);
    }

    /**
     * @param  Directory $dir
     * @param  string    $name
     * @return File|null
     */
    public static function fromDirectory(Directory $dir, string $name): ?File
    {
        if (!$dir -> exists()) {
            return null;
        }
        return self::fromString($dir -> path() . '\\' . $name);
    }

    /**
     * @return string
     */
    public function extension(): string
    {
        $name = $this -> name();
        $segments = explode('.', $name);

        if (count($segments) === 1) {
            return $name;
        }

        return $segments[count($segments) - 1];
    }

    /**
     * @return string
     */
    public function basename(): string
    {
        $name = $this -> name();
        $segments = explode('.', $name);

        if (count($segments) === 1) {
            return $name;
        }
        unset($segments[count($segments) - 1]);
        return implode('.', $segments);
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

    public function contains(string $regex): bool
    {
        throw new \Exception('Not Implemented');
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return $this -> exists_;
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return $this -> path_;
    }

    /**
     * @param  string $path
     * @throws \Exception
     */
    protected function __construct(string $path)
    {
        $this -> path_ = $path;
        $this -> exists_ = file_exists($path);
        if ($this -> exists_) {
            $this -> path_ = realpath($path);
            if (!is_file($this -> path_)) {
                throw new \Exception($this -> path_ . ' is not a ' . get_class($this));
            }
        }
    }
}

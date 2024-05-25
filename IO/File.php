<?php

namespace UT_Php_Core\IO;

class File implements \UT_Php_Core\Interfaces\IFile
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
     * @param  string $path
     * @return File
     */
    public static function fromString(string $path): File
    {
        return new File($path);
    }

    /**
     * @param \UT_Php_Core\Interfaces\IDirectory $dir
     * @return string|null
     * @throws \Exception
     */
    public function relativeTo(\UT_Php_Core\Interfaces\IDirectory $dir): ?string
    {
        if (stristr($this -> path, $dir -> path())) {
            return substr($this -> path, strlen($dir -> path()) + 1);
        }

        throw new \Exception('Not implemented');
    }

    /**
     * @param \UT_Php_Core\Interfaces\IDirectory $dir
     * @param string $name
     * @return bool
     */
    public function copyTo(\UT_Php_Core\Interfaces\IDirectory $dir, string $name = null): bool
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
     * @param string $stream
     * @return void
     */
    public function write(string $stream): void
    {
        file_put_contents($this -> path(), $stream);
        $this -> exists = true;
        $this -> path = realpath($this -> path());
    }

    /**
     * @return \UT_Php_Core\Interfaces\IDirectory|null
     */
    public function parent(): ?\UT_Php_Core\Interfaces\IDirectory
    {
        if (!$this -> exists()) {
            return null;
        }
        $parts = explode('\\', $this -> path);
        unset($parts[count($parts) - 1]);
        if (count($parts) === 0) {
            return null;
        }
        $new = implode('\\', $parts);
        return Directory::fromString($new);
    }

    public function content(): string
    {
        return file_get_contents($this -> path);
    }

    /**
     * @param \UT_Php_Core\Interfaces\IDirectory $dir
     * @param string $name
     * @return File|null
     */
    public static function fromDirectory(\UT_Php_Core\Interfaces\IDirectory $dir, string $name): ?File
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
        $segments = explode('\\', $this -> path);
        if (count($segments) === 0) {
            $segments = explode('/', $this -> path);
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
        return $this -> exists;
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return $this -> path;
    }

    /**
     * @param  string $path
     * @throws \Exception
     */
    protected function __construct(string $path)
    {
        $this -> path = $path;
        $this -> exists = file_exists($path);
        if ($this -> exists) {
            $this -> path = realpath($path);
            if (!is_file($this -> path)) {
                throw new \Exception($this -> path . ' is not a ' . get_class($this));
            }
        }
    }
}

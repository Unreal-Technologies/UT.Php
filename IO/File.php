<?php

namespace UT_Php_Core\IO;

class File implements IFile
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
     * @param string $path
     * @return IFile
     */
    public static function fromString(string $path): IFile
    {
        $cls = get_called_class();
        return new $cls($path);
    }

    /**
     * @param IFile $file
     * @return IFile
     */
    public static function fromFile(IFile $file): IFile
    {
        return self::fromString($file -> path());
    }

    /**
     * @return Common\IXmlFile|null
     */
    public function asXml(): ?Common\IXmlFile
    {
        if ($this -> extension() !== 'xml') {
            return null;
        }
        return new Common\Xml($this -> path);
    }

    /**
     * @return Common\IPngFile|null
     */
    public function asPng(): ?Common\IPngFile
    {
        if ($this -> extension() !== 'png') {
            return null;
        }
        return new Common\Png($this -> path);
    }

    /**
     * @return Common\IPhpFile|null
     */
    public function asPhp(): ?Common\IPhpFile
    {
        if ($this -> extension() !== 'php') {
            return null;
        }
        return new Common\Php($this -> path);
    }

    /**
     * @return Common\IDtdFile|null
     */
    public function asDtd(): ?Common\IDtdFile
    {
        if ($this -> extension() !== 'dtd') {
            return null;
        }
        return new Common\Dtd($this -> path);
    }

    /**
     * @return Common\IBmpFile|null
     */
    public function asBmp(): ?Common\IBmpFile
    {
        if ($this -> extension() !== 'bmp') {
            return null;
        }
        return new Common\Bmp($this -> path);
    }

    /**
     * @return bool
     */
    public function remove(): bool
    {
        if (!$this -> exists) {
            return false;
        }
        return unlink($this -> path);
    }

    /**
     * @param IDirectory $dir
     * @return string|null
     * @throws \Exception
     */
    public function relativeTo(IDirectory $dir): ?string
    {
        if (stristr($this -> path, $dir -> path())) {
            return substr($this -> path, strlen($dir -> path()) + 1);
        }

        throw new \Exception('Not implemented');
    }

    /**
     * @param Directory $dir
     * @param string $name
     * @return bool
     */
    public function copyTo(IDirectory $dir, string $name = null): bool
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
     * @return IDirectory|null
     */
    public function parent(): ?IDirectory
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

    /**
     * @return string
     */
    public function read(): string
    {
        return file_get_contents($this -> path);
    }

    /**
     * @param IDirectory $dir
     * @param string $name
     * @return IFile|null
     */
    public static function fromDirectory(IDirectory $dir, string $name): ?IFile
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

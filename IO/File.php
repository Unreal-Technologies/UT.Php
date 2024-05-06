<?php
namespace UT_Php\IO;

require_once 'IDiskManager.php';

class File implements IDiskManager
{
    private $_path;
    private $_exists;
    
    /** 
     * @param  string $path
     * @return File
     */
    public static function FromString(string $path): File
    {
        return new File($path);
    }
    
    /**
     * @param  Directory $dir
     * @return string|null
     * @throws \Exception
     */
    public function RelativeTo(Directory $dir): ?string
    {
        if(stristr($this -> _path, $dir -> Path())) {
            return substr($this -> _path, strlen($dir -> Path()) + 1);
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
    public function CopyTo(Directory $dir, string $name = null): bool
    {
        if(!$dir -> Exists()) {
            return false;
        }
        if($name === null) {
            $name = $this -> Name();
        }
        return copy($this -> Path(), $dir -> Path().'/'.$name);
    }
    
    /**
     * @return Directory|null
     */
    public function Parent(): ?Directory
    {
        if(!$this -> Exists()) {
            return null;
        }
        $parts = explode('\\', $this -> _path);
        unset($parts[count($parts) - 1]);
        if(count($parts) === 0) {
            return null;
        }
        $new = implode('\\', $parts);
        return Directory::FromString($new);
    }
    
    /**
     * @param  Directory $dir
     * @param  string    $name
     * @return File|null
     */
    public static function FromDirectory(Directory $dir, string $name): ?File
    {
        if(!$dir -> Exists()) {
            return null;
        }
        return self::FromString($dir -> Path().'\\'.$name);
    }
    
    /**
     * @return string
     */
    public function Extension(): string
    {
        $name = $this -> Name();
        $segments = explode('.', $name);
        
        if(count($segments) === 1) {
            return $name;
        }
        
        return $segments[count($segments) - 1];
    }
    
    /**
     * @return string
     */
    public function Basename(): string
    {
        $name = $this -> Name();
        $segments = explode('.', $name);
        
        if(count($segments) === 1) {
            return $name;
        }
        unset($segments[count($segments) - 1]);
        return implode('.', $segments);
    }
    
    /**
     * @return string
     */
    public function Name(): string 
    {
        $segments = explode('\\', $this -> _path);
        if(count($segments) === 0) {
            $segments = explode('/', $this -> _path);
        }
        return $segments[count($segments) - 1];
    }

    public function Contains(string $regex): bool 
    {
        throw new \Exception('Not Implemented');
    }

    /**
     * @return bool
     */
    public function Exists(): bool 
    {
        return $this -> _exists;
    }
    
    /**
     * @return string
     */
    public function Path(): string 
    {
        return $this -> _path;
    }
    
    /**
     * @param  string $path
     * @throws \Exception
     */
    protected function __construct(string $path)
    {
        $this -> _path = $path;
        $this -> _exists = file_exists($path);
        if($this -> _exists) {
            $this -> _path = realpath($path);
            if(!is_file($this -> _path)) {
                throw new \Exception($this -> _path.' is not a '.get_class($this));
            }
        }
    }
}
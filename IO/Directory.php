<?php
namespace UT_Php\IO;

require_once('IDiskManager.php');

class Directory implements IDiskManager
{
    /**
     * @var string
     */
    private $_path;
    
    /**
     * @var bool
     */
    private $_exists;
    
    /**
     * @var resource
     */
    private $_handler;
    
    /**
     * @var IDiskManager[]
     */
    private static $_ram;
    
    /**
     * @param string $dir
     * @return Directory
     */
    public static function FromString(string $dir): Directory
    {
        return new Directory($dir);
    }
    
    /**
     * @param Directory $dir
     * @param string $name
     * @return Directory|null
     */
    public static function FromDirectory(Directory $dir, string $name): ?Directory
    {
        if(!$dir -> Exists())
        {
            return null;
        }
        return self::FromString($dir -> Path().'\\'.$name);
    }
    
    /**
     * @param string $regex
     * @return bool
     */
    public function Contains(string $regex): bool
    {
        foreach($this -> List() as $iDiskManager)
        {
            if(preg_match($regex, $iDiskManager -> Name()))
            {
                return true;
            }
        }
        return false;
    }
    
    /**
     * @return string
     */
    public function Name(): string 
    {
        $segments = explode('\\', $this -> _path);
        if(count($segments) === 0)
        {
            $segments = explode('/', $this -> _path);
        }
        
        return $segments[count($segments) - 1];
    }
    
    /**
     * @return string
     */
    public function Path(): string
    {
        return $this -> _path;
    }
    
    /**
     * @return bool
     */
    public function Exists(): bool
    {
        return $this -> _exists;
    }
    
    /**
     * @return bool
     */
    public function Create(): bool
    {
        if(!$this -> Exists())
        {
            mkdir($this -> _path, 0777);
            $this -> _path = realpath($this -> _path);
            $this -> _exists = true;
            return true;
        }
        return false;
    }
    
    /**
     * @param bool $refresh
     * @return IDiskManager[]
     */
    public function List(string $regex = null, bool $refresh = false): array
    {
        $key = $this -> _path;
        if(isset(self::$_ram[$key]) && !$refresh)
        {
            return self::$_ram[$key];
        }
        
        $output = [];
        if($this -> Open())
        {
            $out = null;
            while($this -> Read($out) !== false)
            {
                if($out === '.' || $out === '..')
                {
                    continue;
                }
                if($regex !== null && !preg_match($regex, $out))
                {
                    continue;
                }
                
                $path = $this -> _path.'\\'.$out;
                
                if(is_dir($path))
                {
                    $output[] = self::FromString($path);
                }
                else
                {
                    $output[] = File::FromString($path);
                }
            }
            $this -> Close();
        }
        
        self::$_ram[$key] = $output;
        
        return $output;
    }
    
    /**
     * @param string|null $out
     * @return bool
     */
    public function Read(?string &$out): bool
    {
        $out = readdir($this -> _handler);
        if($out === false)
        {
            $out = null;
            return false;
        }
        return true;
    }
    
    /**
     * @return bool
     */
    public function Open(): bool
    {
        if($this -> _handler !== null || !$this -> _exists)
        {
            return false;
        }
        $this -> _handler = opendir($this -> _path);
        if($this -> _handler === false)
        {
            $this -> _handler = null;
            return false;
        }
        return true;
    }
    
    /**
     * @return void
     */
    public function Close(): void
    {
        if($this -> _handler !== null)
        {
            closedir($this -> _handler);
        }
    }
    
    /**
     * @param string $dir
     * @throws \Exception
     */
    private function __construct(string $dir)
    {
        if(self::$_ram === null)
        {
            self::$_ram = [];
        }
        $this -> _path = $dir;
        $this -> _exists = file_exists($dir);
        if($this -> _exists)
        {
            $this -> _path = realpath($dir);
            if(!is_dir($this -> _path))
            {
                throw new \Exception($this -> _path.' is not a '.get_class($this));
            }
        }
    }
}

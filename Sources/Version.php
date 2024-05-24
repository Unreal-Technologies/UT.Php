<?php

namespace UT_Php_Core;

class Version
{
    /**
     * @var int
     */
    private int $major;

    /**
     * @var int
     */
    private int $minor;

    /**
     * @var int
     */
    private int $patch;

    /**
     * @var int
     */
    private int $build;

    /**
     * @var Version[]
     */
    private array $subVersions;

    /**
     * @return int
     */
    public function build(): int
    {
        return $this -> patch;
    }
    
    /**
     * @return int
     */
    public function patch(): int
    {
        return $this -> patch;
    }
    
    /**
     * @return int
     */
    public function minor(): int
    {
        return $this -> minor;
    }
    
    /**
     * @return int
     */
    public function major(): int
    {
        return $this -> major;
    }
    
    /**
     * @param int       $major
     * @param int       $minor
     * @param int       $patch
     * @param int       $build
     * @param Version[] $subVersions
     */
    public function __construct(int $major, int $minor, int $patch, int $build, array $subVersions = [])
    {
        $this -> major = $major;
        $this -> minor = $minor;
        $this -> patch = $patch;
        $this -> build = $build;
        $this -> subVersions = $subVersions;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $version = $this -> major . '.' . $this -> minor . '.' . $this -> patch . '.' . $this -> build;
        if (count($this -> subVersions) > 0) {
            $version .= ' [';
            foreach ($this -> subVersions as $name => $sub) {
                $version .= $name . ' (' . $sub . ')';
            }
            $version .= ']';
        }

        return $version;
    }
}

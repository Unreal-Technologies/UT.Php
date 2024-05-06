<?php
namespace UT_Php\Html;

class Directory
{
    private const EOL = "\r\n";
    
    /**
     * @var DirectoryBranch[]
     */
    private array $branches = [];
    
    /**
     * @var \UT_Php\IO\File[]
     */
    private array $files = [];
    
    /**
     * @var int
     */
    private int $offset;
    
    /**
     * @var string
     */
    private string $name;
    
    /**
     * @var bool
     */
    private bool $parentHasFiles = false;
    
    /**
     * @var int
     */
    private int $depthOffset = 0;
    
    /**
     * @var bool
     */
    private bool $isLastBranch = true;

    private \UT_Php\IO\Directory $root;


    /**
     * @param \UT_Php\IO\Directory $directory
     * @param \UT_Php\IO\Directory $root
     * @param int                  $offset
     */
    public function __construct(\UT_Php\IO\Directory $directory, \UT_Php\IO\Directory $root , int $offset = 0)
    {
        $this -> root = $root;
        $this -> name = $directory -> Name();
        $this -> offset = $offset;
        foreach($directory -> List() as $item)
        {
            if($item instanceof \UT_Php\IO\Directory) {
                $this -> AddBranch(new Directory($item, $root, $offset + 1));
            }
            else
            {
                $this -> AddFile($item);
            }
        }
        if(count($this -> files) != 0) {
            foreach($this -> branches as $branch)
            {
                $branch -> ParentHasFiles(true);
            }
        }
    }
    
    /**
     * @return string
     */
    private function TableStart(): string
    {
        if($this -> offset == 0) {
            return '<table id="DirectoryRender">'.$this::EOL;
        }
        return '';
    }
    
    /**
     * @return string
     */
    private function TableEnd(): string
    {
        if($this -> offset == 0) {
            return '</table>'.$this::EOL;
        }
        return '';
    }
    
    /**
     * @param  int $depth
     * @return string
     */
    private function RenderHeader(int $depth): string
    {
        $html = '<tr>'.$this::EOL;
        if($this -> offset != 0) {
            for($i=0; $i<$this -> offset - 1; $i++)
            {
                $html .= '<td class="down"></td>'.$this::EOL;
            }
            
            $hasFiles = count($this -> files) != 0;

            $class = !$hasFiles && !$this -> parentHasFiles ? 'right' : ($hasFiles && !$this -> parentHasFiles && $this -> isLastBranch ? 'right' : 'down-right');
            $html .= '<td class="'.$class.'"></td>'.$this::EOL;
        }
        $html .= '<td colspan="'.($depth + $this -> depthOffset - $this -> offset + 1).'" class="header">'.$this -> name.'</td>'.$this::EOL;
        $html .= '</tr>'.$this::EOL;
        return $html;
    }
    
    /**
     * @param  int $depth
     * @return string
     */
    private function RenderBranches(int $depth): string
    {
        $html = '';
        $count = count($this -> branches);
        foreach($this -> branches as $i => $branch)
        {
            if($this -> isLastBranch) {
                $branch -> isLastBranch = $i == $count - 1;
            }
            $branch -> depthOffset = $depth - $branch -> GetDepth();
            $html .= $branch;
        }
        return $html;
    }

    /**
     * @param  int $depth
     * @return string
     */
    private function RenderFiles(int $depth): string
    {
        $html = '';
        $f = 0;
        foreach($this -> files as $file)
        {
            $html .= '<tr>'.$this::EOL;
            for($i=0; $i<$this -> offset + 1; $i++)
            {
                $isSubLast = $i == $this -> offset;
                $isLast =  $isSubLast && $f == count($this -> files) - 1;
                $class = $isLast ? 'right' : ($isSubLast ? 'down-right"' : ($this -> isLastBranch ? null : 'down'));
                
                $html .= '<td class="'.$class.'"></td>'.$this::EOL;
            }
            $html .= '<td colspan="'.($depth + $this -> depthOffset - $this -> offset).'"><a href="'.$file -> RelativeTo($this -> root).'" target="_blank">'.$file -> Name().'</a></td>'.$this::EOL;
            $html .= '</tr>'.$this::EOL;
            
            $f++;
        }
        return $html;
    }
    
    /**
     * @return string
     */
    public function __toString(): string 
    {
        $depth = $this -> GetDepth();
        
        $html = $this -> TableStart();
        $html .= $this -> RenderHeader($depth);
        $html .= $this -> RenderBranches($depth);
        $html .= $this -> RenderFiles($depth);
        $html .= $this -> TableEnd();
        
        return $html;
    }
    
    /**
     * @param  bool $state
     * @return void
     */
    private function ParentHasFiles(bool $state): void
    {
        $this -> parentHasFiles = $state;
    }
    
    /**
     * @return int
     */
    private function GetDepth(): int
    {
        if(count($this -> branches) == 0) {
            return $this -> offset + 1;
        }
        $depth = $this -> offset + 1;
        foreach($this -> branches as $branch)
        {
            $d = $branch -> GetDepth();
            if($d > $depth) {
                $depth = $d;
            }
        }
        
        return $depth;
    }

    /**
     * @param  DirectoryBranch $branch
     * @return void
     */
    private function AddBranch(Directory $branch): void
    {
        $branch -> index = count($this -> branches);
        $branch -> isLastBranch = false;
        $this -> branches[] = $branch;
    }
    
    /**
     * @param  \UT_Php\IO\File $file
     * @return void
     */
    private function AddFile(\UT_Php\IO\File $file): void
    {
        $this -> files[] = $file;
    }
}
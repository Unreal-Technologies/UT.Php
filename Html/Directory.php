<?php
namespace UT_Php\Html;

class Directory
{
    private const EOL = "\r\n";
    
    /**
     * @var DirectoryBranch[]
     */
    private array $branches_ = [];
    
    /**
     * @var \UT_Php\IO\File[]
     */
    private array $files_ = [];
    
    /**
     * @var int
     */
    private int $offset_;
    
    /**
     * @var string
     */
    private string $name_;
    
    /**
     * @var bool
     */
    private bool $parentHasFiles_ = false;
    
    /**
     * @var int
     */
    private int $depthOffset_ = 0;
    
    /**
     * @var bool
     */
    private bool $isLastBranch_ = true;

    private \UT_Php\IO\Directory $root_;


    /**
     * @param \UT_Php\IO\Directory $directory
     * @param \UT_Php\IO\Directory $root
     * @param int                  $offset
     */
    public function __construct(\UT_Php\IO\Directory $directory, \UT_Php\IO\Directory $root, int $offset = 0)
    {
        $this -> root_ = $root;
        $this -> name_ = $directory -> name();
        $this -> offset_ = $offset;
        foreach ($directory -> list() as $item) {
            if ($item instanceof \UT_Php\IO\Directory) {
                $this -> addBranch(new Directory($item, $root, $offset + 1));
            } else {
                $this -> addFile($item);
            }
        }
        if (count($this -> files_) != 0) {
            foreach ($this -> branches_ as $branch) {
                $branch -> parentHasFiles(true);
            }
        }
    }
    
    /**
     * @return string
     */
    private function tableStart(): string
    {
        if ($this -> offset_ == 0) {
            return '<table id="DirectoryRender">'.$this::EOL;
        }
        return '';
    }
    
    /**
     * @return string
     */
    private function tableEnd(): string
    {
        if ($this -> offset_ == 0) {
            return '</table>'.$this::EOL;
        }
        return '';
    }
    
    /**
     * @param  int $depth
     * @return string
     */
    private function renderHeader(int $depth): string
    {
        $html = '<tr>'.$this::EOL;
        if ($this -> offset_ != 0) {
            for ($i=0; $i<$this -> offset_ - 1; $i++) {
                $html .= '<td class="down"></td>'.$this::EOL;
            }
            
            $hasFiles = count($this -> files_) != 0;

            $class = !$hasFiles && !$this -> parentHasFiles_ ?
                'right' :
                (
                    $hasFiles && !$this -> parentHasFiles_ && $this -> isLastBranch_ ?
                    'right' :
                    'down-right'
                );
            $html .= '<td class="'.$class.'"></td>'.$this::EOL;
        }
        $html .= '<td colspan="'.
                ($depth + $this -> depthOffset_ - $this -> offset_ + 1).
                '" class="header">'.
                $this -> name_.'</td>'.
                $this::EOL;
        $html .= '</tr>'.$this::EOL;
        return $html;
    }
    
    /**
     * @param  int $depth
     * @return string
     */
    private function renderBranches(int $depth): string
    {
        $html = '';
        $count = count($this -> branches_);
        foreach ($this -> branches_ as $i => $branch) {
            if ($this -> isLastBranch_) {
                $branch -> isLastBranch_ = $i == $count - 1;
            }
            $branch -> depthOffset_ = $depth - $branch -> getDepth();
            $html .= $branch;
        }
        return $html;
    }

    /**
     * @param  int $depth
     * @return string
     */
    private function renderFiles(int $depth): string
    {
        $html = '';
        $f = 0;
        foreach ($this -> files_ as $file) {
            $html .= '<tr>'.$this::EOL;
            for ($i=0; $i<$this -> offset_ + 1; $i++) {
                $isSubLast = $i == $this -> offset_;
                $isLast =  $isSubLast && $f == count($this -> files_) - 1;
                $class = $isLast ? 'right' : ($isSubLast ? 'down-right"' : ($this -> isLastBranch_ ? null : 'down'));
                
                $html .= '<td class="'.$class.'"></td>'.$this::EOL;
            }
            $html .= '<td colspan="'.
                    ($depth + $this -> depthOffset_ - $this -> offset_).
                    '"><a href="'.
                    $file -> relativeTo($this -> root_).
                    '" target="_blank">'.
                    $file -> name().
                    '</a></td>'.
                    $this::EOL;
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
        $depth = $this -> getDepth();
        
        $html = $this -> tableStart();
        $html .= $this -> renderHeader($depth);
        $html .= $this -> renderBranches($depth);
        $html .= $this -> renderFiles($depth);
        $html .= $this -> tableEnd();
        
        return $html;
    }
    
    /**
     * @param  bool $state
     * @return void
     */
    private function parentHasFiles(bool $state): void
    {
        $this -> parentHasFiles_ = $state;
    }
    
    /**
     * @return int
     */
    private function getDepth(): int
    {
        if (count($this -> branches_) == 0) {
            return $this -> offset_ + 1;
        }
        $depth = $this -> offset_ + 1;
        foreach ($this -> branches_ as $branch) {
            $d = $branch -> getDepth();
            if ($d > $depth) {
                $depth = $d;
            }
        }
        
        return $depth;
    }

    /**
     * @param  DirectoryBranch $branch
     * @return void
     */
    private function addBranch(Directory $branch): void
    {
        $branch -> index = count($this -> branches_);
        $branch -> isLastBranch_ = false;
        $this -> branches_[] = $branch;
    }
    
    /**
     * @param  \UT_Php\IO\File $file
     * @return void
     */
    private function addFile(\UT_Php\IO\File $file): void
    {
        $this -> files_[] = $file;
    }
}

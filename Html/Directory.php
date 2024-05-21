<?php

namespace UT_Php\Html;

class Directory
{
    private const EOL = "\r\n";

    /**
     * @var Directory[]
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

    /**
     * @var int
     */
    private int $index = 0;

    /**
     * @var \UT_Php\Interfaces\IDirectory
     */
    private \UT_Php\Interfaces\IDirectory $root;

    /**
     * @param \UT_Php\Interfaces\IDirectory $directory
     * @param \UT_Php\Interfaces\IDirectory $root
     * @param int $offset
     */
    public function __construct(
        \UT_Php\Interfaces\IDirectory $directory,
        \UT_Php\Interfaces\IDirectory $root,
        int $offset = 0
    ) {
        $this -> root = $root;
        $this -> name = $directory -> name();
        $this -> offset = $offset;
        foreach ($directory -> list() as $item) {
            if ($item instanceof \UT_Php\IO\Directory) {
                $this -> addBranch(new Directory($item, $root, $offset + 1));
            } else {
                $this -> addFile($item);
            }
        }
        if (count($this -> files) != 0) {
            foreach ($this -> branches as $branch) {
                $branch -> parentHasFiles(true);
            }
        }
    }

    /**
     * @return string
     */
    private function tableStart(): string
    {
        if ($this -> offset == 0) {
            return '<table id="DirectoryRender">' . $this::EOL;
        }
        return '';
    }

    /**
     * @return string
     */
    private function tableEnd(): string
    {
        if ($this -> offset == 0) {
            return '</table>' . $this::EOL;
        }
        return '';
    }

    /**
     * @param  int $depth
     * @return string
     */
    private function renderHeader(int $depth): string
    {
        $html = '<tr>' . $this::EOL;
        if ($this -> offset != 0) {
            for ($i = 0; $i < $this -> offset - 1; $i++) {
                $html .= '<td class="down"></td>' . $this::EOL;
            }

            $hasFiles = count($this -> files) != 0;

            $class = !$hasFiles && !$this -> parentHasFiles ?
                'right' :
                (
                    $hasFiles && !$this -> parentHasFiles && $this -> isLastBranch ?
                    'right' :
                    'down-right'
                );
            $html .= '<td class="' . $class . '"></td>' . $this::EOL;
        }
        $html .= '<td colspan="' .
                ($depth + $this -> depthOffset - $this -> offset + 1) .
                '" class="header">' .
                $this -> name . '</td>' .
                $this::EOL;
        $html .= '</tr>' . $this::EOL;
        return $html;
    }

    /**
     * @param  int $depth
     * @return string
     */
    private function renderBranches(int $depth): string
    {
        $html = '';
        $count = count($this -> branches);
        foreach ($this -> branches as $i => $branch) {
            if ($this -> isLastBranch) {
                $branch -> isLastBranch = $i == $count - 1;
            }
            $branch -> depthOffset = $depth - $branch -> getDepth();
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
        foreach ($this -> files as $file) {
            $html .= '<tr>' . $this::EOL;
            for ($i = 0; $i < $this -> offset + 1; $i++) {
                $isSubLast = $i == $this -> offset;
                $isLast =  $isSubLast && $f == count($this -> files) - 1;
                $class = $isLast ? 'right' : ($isSubLast ? 'down-right"' : ($this -> isLastBranch ? null : 'down'));

                $html .= '<td class="' . $class . '"></td>' . $this::EOL;
            }
            $html .= '<td colspan="' .
                    ($depth + $this -> depthOffset - $this -> offset) .
                    '"><a href="' .
                    $file -> relativeTo($this -> root) .
                    '" target="_blank">' .
                    $file -> name() .
                    '</a></td>' .
                    $this::EOL;
            $html .= '</tr>' . $this::EOL;

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
        $this -> parentHasFiles = $state;
    }

    /**
     * @return int
     */
    private function getDepth(): int
    {
        if (count($this -> branches) == 0) {
            return $this -> offset + 1;
        }
        $depth = $this -> offset + 1;
        foreach ($this -> branches as $branch) {
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
        $branch -> index = count($this -> branches);
        $branch -> isLastBranch = false;
        $this -> branches[] = $branch;
    }

    /**
     * @param  \UT_Php\IO\File $file
     * @return void
     */
    private function addFile(\UT_Php\IO\File $file): void
    {
        $this -> files[] = $file;
    }
}

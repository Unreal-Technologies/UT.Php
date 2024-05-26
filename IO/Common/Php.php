<?php

namespace UT_Php_Core\IO\Common;

class Php extends \UT_Php_Core\IO\File implements \UT_Php_Core\Interfaces\IPhpFile
{
    /**
     * @var Php\TokenNamespace|null
     */
    private ?Php\TokenNamespace $namespace = null;
    
    /**
     * @var Php\TokenObject|null
     */
    private ?Php\TokenObject $object = null;

    /**
     * @var array
     */
    private array $tokens;

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        parent::__construct($path);
        $this -> tokens = token_get_all($this -> read());
        $this -> defaultParsing();
    }

    /**
     * @return array
     */
    public function tokens(): array
    {
        return $this -> tokens;
    }

    /**
     * @return Php\TokenNamespace|null
     */
    public function namespace(): ?Php\TokenNamespace
    {
        return $this -> namespace;
    }
    
    /**
     * @return Php\TokenObject|null
     */
    public function object(): ?Php\TokenObject
    {
        return $this -> object;
    }

    /**
     * @return void
     */
    private function defaultParsing(): void
    {
        echo 'temp!';
        if (!file_exists('temp')) {
            mkdir('temp', 0777);
        }
        $file = 'temp/' . str_replace($this -> extension(), 'txt', $this -> name());
        file_put_contents($file, print_r($this -> tokens, true));

        foreach ($this -> tokens as $idx => $token) {
            if (is_array($token) && $token[0] === 375 && $this -> namespace === null) { //Namespace
                $i = $idx;
                while ($this -> tokens[$i] !== ';') {
                    $i++;
                }
                $this -> namespace = new Php\TokenNamespace(array_slice($this -> tokens, $idx, $i - $idx));
            }
            if (is_array($token) && in_array($token[0], [369, 370, 371, 372]) && $this -> object === null) { //object
                $i = $idx;
                $ir = $idx;
                while ($this -> tokens[$i] !== '{') {
                    $i++;
                }
                while ($this -> tokens[$ir] !== ';' && $ir > 0) {
                    $ir--;
                }

                $object = array_slice($this -> tokens, $ir + 1, $i - $ir - 1);
                if ($object[0][0] === 397) {
                    $object = array_slice($object, 1);
                }
                if ($object[count($object) - 1][0] === 397) {
                    $object = array_slice($object, 0, count($object) - 1);
                }

                $this -> object = new Php\TokenObject($object);
            }
        }
    }
}

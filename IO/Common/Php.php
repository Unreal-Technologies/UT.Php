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
     * @var Php\TokenTrait[]
     */
    private array $traits = [];

    /**
     * @var array
     */
    private array $members = [];
    
    /**
     * @var array
     */
    private array $methods = [];

    /**
     * @var Php\TokenCase[]
     */
    private array $cases = [];

    /**
     * @var array
     */
    private array $tokens = [];

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        parent::__construct($path);
        $this -> tokens = token_get_all($this -> read());
        $this -> defaultParsing();

        $this -> tokens = [];
        unset($this -> tokens);
    }

    /**
     * @return Php\TokenNamespace|null
     */
    public function namespace(): ?Php\TokenNamespace
    {
        return $this -> namespace;
    }

    /**
     * @return Php\TokenMember[]
     */
    public function members(): array
    {
        return $this -> members;
    }
    
    /**
     * @return Php\TokenMethod[]
     */
    public function methods(): array
    {
        return $this -> methods;
    }

    /**
     * @return Php\TokenObject|null
     */
    public function object(): ?Php\TokenObject
    {
        return $this -> object;
    }

    /**
     * @return Php\TokenTrait[]
     */
    public function traits(): array
    {
        return $this -> traits;
    }

    /**
     * @return Php\TokenCase[]
     */
    public function cases(): array
    {
        return $this -> cases;
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

        $traits = [];
        $methods = [];
        $cases = [];
        $members = [];
        foreach ($this -> tokens as $idx => $token) {
            if (is_array($token) && $token[0] === 375 && $this -> namespace === null) { //Namespace
                $i = $idx;
                while ($this -> tokens[$i] !== ';') {
                    $i++;
                }
                $this -> namespace = new Php\TokenNamespace(array_slice($this -> tokens, $idx, $i - $idx));
            } elseif (is_array($token) && in_array($token[0], [369, 370, 371, 372]) && $this -> object === null) { //object
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
            else if(is_array($token) && $token[0] === 317) //members
            {
                $line = $token[2];
                $i = $idx;
                while ($this -> tokens[$i] !== ';') {
                    $i++;
                }
                
                $isFunction = false;
                $ir = $idx;
                while (!in_array($this -> tokens[$ir], ['{', '}', ';']) && !is_array($this -> tokens[$ir]) || (is_array($this -> tokens[$ir]) && $this -> tokens[$ir][2] === $line)) {
                    if($this -> tokens[$ir][0] === 347)
                    {
                        $isFunction = true;
                        break;
                    }
                    $ir--;
                }
                
                if(!$isFunction)
                {
                    $member = array_slice($this -> tokens, $ir, $i - $ir);
                    if ($member[0][0] === 397) {
                        $member = array_slice($member, 1);
                    }
                    if ($member[count($member) - 1][0] === 397) {
                        $member = array_slice($member, 0, count($member) - 1);
                    }
                    
                    $members[] = $member;
                }
            }
            elseif (is_array($token) && $token[0] === 341) { //cases
                $i = $idx;
                while ($this -> tokens[$i] !== ';') {
                    $i++;
                }

                $cases[] = array_slice($this -> tokens, $idx, $i - $idx);
            } elseif (is_array($token) && $token[0] === 354) { //use traits
                $i = $idx;
                while ($this -> tokens[$i] !== ';') {
                    $i++;
                }

                $traits[] = array_slice($this -> tokens, $idx, $i - $idx);
            } elseif (is_array($token) && $token[0] === 347) { //methods
                $i = $idx;
                while (!in_array($this -> tokens[$i], ['{', ';'])) {
                    $i++;
                }

                $ir = $idx;
                while (!in_array($this -> tokens[$ir], ['{', '}', ';'])) {
                    $ir--;
                }

                $declaration = array_slice($this -> tokens, $ir + 1, $i - $ir - 1);
                if ($declaration[0][0] === 397) {
                    $declaration = array_slice($declaration, 1);
                }
                if ($declaration[count($declaration) - 1][0] === 397) {
                    $declaration = array_slice($declaration, 0, count($declaration) - 1);
                }

                $depth = 0;
                $bs = $i;
                $be = $bs;
                while ($this -> tokens[$be] !== '}' || $depth > 1) {
                    if ($this -> tokens[$be] === '{') {
                        $depth++;
                    }
                    if ($this -> tokens[$be] === '}') {
                        $depth--;
                    }

                    $be++;
                }

                $body = array_slice($this -> tokens, $bs, $be - $bs + 1);

                $methods[] = [
                    'Head' => $declaration,
                    'Body' => $this -> object() -> isInterface() ? [] : $body
                ];
            }
        }

        $removeTraits = [];
        foreach ($traits as $idx => $trait) {
            foreach ($methods as $method) {
                if ($this -> containsSequence($trait, $method['Body'])) {
                    $removeTraits[] = $idx;
                    break;
                }
            }
        }
        foreach ($removeTraits as $i) {
            unset($traits[$i]);
        }

        $removeMethods = [];
        foreach ($methods as $i1 => $m1) {
            foreach ($methods as $i2 => $m2) {
                if ($i1 === $i2) {
                    continue;
                }
                if ($this -> containsSequence($m1['Body'], $m2['Body'])) {
                    $removeMethods[] = $i1;
                }
            }
        }
        foreach ($removeMethods as $i) {
            unset($methods[$i]);
        }

        foreach ($traits as $trait) {
            if ($this -> isTraitMultiple($trait)) {
                $this -> traits = array_merge($this -> traits, $this -> parseTraitMultiple($trait));
            } else {
                $this -> traits[] = new Php\TokenTrait($trait);
            }
        }

        foreach ($methods as $method) {
            $this -> methods[] = new Php\TokenMethod($method['Head'], $method['Body']);
        }

        $removeCases = [];
        foreach ($cases as $idx => $case) {
            foreach ($methods as $method) {
                if ($this -> containsSequence($case, $method['Body'])) {
                    $removeCases[] = $idx;
                    break;
                }
            }
        }
        foreach ($removeCases as $idx) {
            unset($cases[$idx]);
        }

        foreach ($cases as $case) {
            $this -> cases[] = new Php\TokenCase($case);
        }
        
        $removeMembers = [];
        foreach ($members as $idx => $member) {
            foreach ($methods as $method) {
                if ($this -> containsSequence($member, $method['Body'])) {
                    $removeMembers[] = $idx;
                    break;
                }
            }
        }
        
        foreach ($removeMembers as $idx) {
            unset($members[$idx]);
        }
        
        foreach($members as $member)
        {
            $m = new Php\TokenMember($member);
            if($m -> isPublic() || $m -> isProtected() || $m -> isPrivate())
            {
                $this -> members[] = $m;
            }
        }
        
        echo 'temp!';
        if (!file_exists('temp')) {
            mkdir('temp', 0777);
        }
        $file = 'temp/' . str_replace($this -> extension(), 'php', $this -> name());
        file_put_contents($file, print_r($this, true));
    }

    /**
     * @param array $tokens
     * @return Php\TokenTrait[]
     */
    private function parseTraitMultiple(array $tokens): array
    {
        $list = (new \UT_Php_Core\Collections\Linq($tokens))
            -> toArray(function ($x) {
                return $x[0] === 313;
            });

        $first = $list[0];
        $startFirst = array_search($first, $tokens);

        $base = array_slice($tokens, 0, $startFirst);
        $buffer = [];
        foreach ($list as $entry) {
            $data = $base;
            $data[] = $entry;

            $buffer[] = new Php\TokenTrait($data);
        }

        return $buffer;
    }

    /**
     * @param array $tokens
     * @return bool
     */
    private function isTraitMultiple(array $tokens): bool
    {
        $pos = array_search(',', $tokens);
        if (!$pos && $pos !== 0) {
            return false;
        }
        return true;
    }

    /**
     * @param array $needle
     * @param array $haystack
     * @return bool
     */
    private function containsSequence(array $needle, array $haystack): bool
    {
        for ($h = 0; $h < count($haystack); $h++) {
            $shiftedHaystack = array_slice($haystack, $h);

            if ($shiftedHaystack === $needle) {
                return true;
            }

            $start = array_search($needle[0], $shiftedHaystack);
            if (!$start && $start !== 0) {
                continue;
            }

            $possibleHaystack = array_slice($shiftedHaystack, $start, count($needle));
            if ($possibleHaystack === $needle) {
                return true;
            }
        }

        return false;
    }
}

<?php

namespace UT_Php_Core\IO\Common\Php;

class TokenMethod implements IMethod
{
    use TAccess;

    /**
     * @var array
     */
    protected array $tokens;

    /**
     * @var array|null
     */
    private ?array $body;

    /**
     * @param array $head
     * @param array|null $body
     */
    public function __construct(array $head, ?array $body = null)
    {
        $this -> tokens = $head;
        $this -> body = $body;
    }

    /**
     * @return string[]
     */
    public function variables(bool $includeParameters = true): array
    {
        $head = $this -> headVariables();
        $body = [];

        foreach ($this -> body as $idx => $token) {
            if (is_array($token) && $token[0] === 317 && $token[1] !== '$this') {
                $ir = $idx - 1;
                while (is_array($this -> body[$ir]) && $this -> body[$ir][0] === 397) {
                    $ir--;
                    break;
                }
                $prev = $this -> body[$ir];

                if ((is_array($prev) && !in_array($prev[1], ['->', '::'])) || !is_array($prev)) {
                    $body[] = $token[1];
                }
            }
        }

        $buffer = [];
        foreach (array_unique($body) as $entry) {
            if (
                (
                    $includeParameters || (
                        !$includeParameters &&
                        !in_array($entry, $head)
                    )
                ) &&
                !preg_match('/^\$\_/', $entry)
            ) {
                $buffer[] = $entry;
            }
        }

        return $buffer;
    }

    /**
     * @return array
     */
    private function headVariables(): array
    {
        $s = array_search('(', $this -> tokens);
        $rev = array_reverse($this -> tokens, true);
        $e = array_search(')', $rev);

        $param = array_slice($this -> tokens, $s, $e);
        return (new \UT_Php_Core\Collections\Linq($param))
        -> where(function ($x) {
            return is_array($x) && $x[0] === 317;
        })
        -> select(function (array $x) {
            return $x[1];
        })
        -> toArray();
    }

    /**
     * @return void
     */
    public function strip(): void
    {
        $this -> stripActual($this -> body);
        $this -> stripActual($this -> tokens);
    }

    /**
     * @param array $tokens
     * @return void
     */
    private function stripActual(array &$tokens): void
    {
        $generic = ['?', ':', '=', '-', '/', '*', '->', ';', ',', '??=', '===', '!==', '=>', '+'];
        $left = array_merge($generic, ['(', '{', '[']);
        $right = array_merge($generic, [')', '}', ']']);

        $remove = [];
        foreach ($tokens as $idx => $token) {
            if (is_array($token) && $token[0] === 397 && $token[1] !== ' ') {
                $remove[] = $idx;
            } elseif (is_array($token) && $token[0] == 392) {
                $remove[] = $idx;
            } elseif (is_array($token) && $token[0] === 397 && $token[1] === ' ') {
                $prev = $tokens[$idx - 1];
                $next = $tokens[$idx + 1];
                if (
                    (!is_array($next) && in_array($next, $left)) ||
                    (is_array($next) && in_array($next[1], $left)) ||
                    (!is_array($prev) && in_array($prev, $right)) ||
                    (is_array($prev) && in_array($prev[1], $right))
                ) {
                    $remove[] = $idx;
                }
            }
        }
        foreach ($remove as $idx) {
            unset($tokens[$idx]);
        }
        $tokens = array_values($tokens);
    }

    /**
     * @param string $old
     * @param string $new
     * @return void
     */
    public function replace(string $old, string $new, ReplaceTypes $type): void
    {
        $this -> replaceActual($this -> body, $old, $new, $type);
        $this -> replaceActual($this -> tokens, $old, $new, $type);
    }

    /**
     * @param array $tokens
     * @param string $old
     * @param string $new
     * @return void
     * @throws \UT_Php_Core\Exceptions\NotImplementedException
     */
    private function replaceActual(array &$tokens, string $old, string $new, ReplaceTypes $type): void
    {
        $generic = [
            '{', '}', '=', ';', '[', ']', ')', '+', ',', '?', ':', '(', '-', '.', '>', '!', '/', '<', '@'
        ];
        $isMethod = $type === ReplaceTypes::Method;
        $isMember = $type === ReplaceTypes::Member;
        $isVariable = $type === ReplaceTypes::Variable;
        $isConstant = $type === ReplaceTypes::Constant;
        $isDeclaration = $type === ReplaceTypes::Declaration;

        $replacements = [];
        foreach ($tokens as $idx => $token) {
            if (is_array($token) && in_array($token[0], [313, 317]) && $token[1] === $old) {
                if ($isVariable) {
                    $ir = $idx - 1;
                    while (is_array($tokens[$ir]) && $tokens[$ir][0] === 397) {
                        $ir--;
                    }
                    $prev = $tokens[$ir];
                    $i = $idx + 1;
                    while (is_array($tokens[$i]) && $tokens[$i][0] === 397) {
                        $i++;
                    }
                    $next = $tokens[$i];

                    if (
                        is_array($prev) &&
                        in_array(
                            $prev[0],
                            [
                                350, 275, 269, 338, 289,
                                271, 314, 313, 392, 299,
                                286, 288, 377, 291, 285,
                                324, 292, 307, 328, 301,
                                347
                            ]
                        )
                    ) {
                        $token[1] = $new;
                        $replacements[$idx] = $token;
                    } elseif (
                        !is_array($prev) &&
                        in_array($prev, $generic) &&
                        !is_array($next) &&
                        in_array($next, $generic)
                    ) {
                        $token[1] = $new;
                        $replacements[$idx] = $token;
                    } elseif (!is_array($prev) && in_array($prev, $generic) && is_array($next)) {
                        $token[1] = $new;
                        $replacements[$idx] = $token;
                    } else {
                        throw new \UT_Php_Core\Exceptions\NotImplementedException(
                            'Undefined token "' .
                            $this -> debugOutput($token) .
                            '", prev = "' .
                            $this -> debugOutput($prev) .
                            '", next = "' .
                            $this -> debugOutput($next) .
                            '"'
                        );
                    }
                } elseif ($isMember) {
                    $ir = $idx - 1;
                    while (is_array($tokens[$ir]) && $tokens[$ir][0] === 397) {
                        $ir--;
                    }
                    $prev = $tokens[$ir];
                    $i = $idx + 1;
                    while (is_array($tokens[$i]) && $tokens[$i][0] === 397) {
                        $i++;
                    }
                    $next = $tokens[$i];

                    if (
                        is_array($prev) &&
                        in_array($prev[0], [390]) &&
                        $token[1][0] !== '$' &&
                        $next !== '('
                    ) { //Non static Member
                        $token[1] = $new;
                        $replacements[$idx] = $token;
                    } elseif (is_array($prev) && in_array($prev[0], [402]) && $token[1][0] === '$') { //Static Member
                        $token[1] = $new;
                        $replacements[$idx] = $token;
                    } elseif (is_array($prev) && in_array($prev[0], [347])) {
                        continue;
                    } elseif (is_array($prev) && in_array($prev[0], [390]) && $token[1][0] !== '$' && $next === '(') {
                        continue;
                    } else {
                        throw new \UT_Php_Core\Exceptions\NotImplementedException(
                            'Undefined token "' .
                            $this -> debugOutput($token) .
                            '", prev = "' .
                            $this -> debugOutput($prev) .
                            '"'
                        );
                    }
                } elseif ($isConstant) {
                    $ir = $idx - 1;
                    while (is_array($tokens[$ir]) && $tokens[$ir][0] === 397) {
                        $ir--;
                    }
                    $prev = $tokens[$ir];

                    if (is_array($prev) && in_array($prev[0], [402]) && $token[1][0] !== '$') {
                        $token[1] = $new;
                        $replacements[$idx] = $token;
                    } else {
                        throw new \UT_Php_Core\Exceptions\NotImplementedException(
                            'Undefined token "' .
                            $this -> debugOutput($token) .
                            '", prev = "' .
                            $this -> debugOutput($prev) .
                            '"'
                        );
                    }
                } elseif ($isDeclaration) {
                    $ir = $idx - 1;
                    while (is_array($tokens[$ir]) && $tokens[$ir][0] === 397) {
                        $ir--;
                    }
                    $prev = $tokens[$ir];
                    $i = $idx + 1;
                    while (is_array($tokens[$i]) && $tokens[$i][0] === 397) {
                        $i++;
                    }
                    $next = $tokens[$i];

                    if (is_array($prev) && in_array($prev[0], [347]) && $next === '(') {
                        $token[1] = $new;
                        $replacements[$idx] = $token;
                    } elseif (is_array($prev) && in_array($prev[0], [390]) && in_array($next, ['(', '=', '?'])) {
                        continue;
                    } else {
                        throw new \UT_Php_Core\Exceptions\NotImplementedException(
                            'Undefined token "' .
                            $this -> debugOutput($token) .
                            '", prev = "' .
                            $this -> debugOutput($prev) .
                            '", next = "' .
                            $this -> debugOutput($next) .
                            '"'
                        );
                    }
                } elseif ($isMethod) {
                    $ir = $idx - 1;
                    while (is_array($tokens[$ir]) && $tokens[$ir][0] === 397) {
                        $ir--;
                    }
                    $prev = $tokens[$ir];
                    $i = $idx + 1;
                    while (is_array($tokens[$i]) && $tokens[$i][0] === 397) {
                        $i++;
                    }
                    $next = $tokens[$i];

                    if (is_array($prev) && in_array($prev[0], [390,402]) && $next === '(') {
                        $token[1] = $new;
                        $replacements[$idx] = $token;
                    } elseif (is_array($prev) && in_array($prev[0], [390]) && in_array($next, ['(', '=', '?'])) {
                        continue;
                    } elseif (
                        is_array($prev) &&
                        in_array($prev[0], [390]) &&
                        is_array($next) &&
                        in_array($next[0], [286])
                    ) {
                        continue;
                    } else {
                        throw new \UT_Php_Core\Exceptions\NotImplementedException(
                            'Undefined token "' .
                            $this -> debugOutput($token) .
                            '", prev = "' .
                            $this -> debugOutput($prev) .
                            '", next = "' .
                            $this -> debugOutput($next) .
                            '"'
                        );
                    }
                }
            }
        }
        foreach ($replacements as $idx => $token) {
            $tokens[$idx] = $token;
        }
    }

    /**
     * @param mixed $item
     * @return string|null
     * @throws \UT_Php_Core\Exceptions\NotImplementedException
     */
    private function debugOutput(mixed $item): ?string
    {
        if (is_array($item)) {
            $output = 'array:[';
            foreach ($item as $k => $v) {
                $output .= $this -> debugOutput($k) . ' => ' . $this -> debugOutput($v);
            }
            $output .= ']';
            return $output;
        } elseif (is_string($item)) {
            return '(string)' . $item;
        } elseif (is_int($item)) {
            return '(int)' . $item;
        } else {
            var_dump($item);
            throw new \UT_Php_Core\Exceptions\NotImplementedException();
        }

        return null;
    }

    /**
     * @return string
     */
    public function declaration(): string
    {
        return str_replace([',', '  '], [', ', ' '], implode('', (new \UT_Php_Core\Collections\Linq($this -> tokens))
            -> select(function ($x) {
                if (is_array($x)) {
                    if (stristr($x[1], "\n")) {
                        return '';
                    }
                    return $x[1];
                }
                return $x;
            })
            -> toArray()));
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return (new \UT_Php_Core\Collections\Linq($this -> tokens))
            -> firstOrDefault(function ($x) {
                return is_array($x) && in_array($x[0], [313, 375, 374, 376]);
            })[1];
    }

    /**
     * @return string|null
     */
    public function body(): ?string
    {
        if ($this -> body === null) {
            return null;
        }
        return implode('', (new \UT_Php_Core\Collections\Linq($this -> body))
            -> select(function ($x) {
                if (is_array($x)) {
                    return $x[1];
                }
                return $x;
            })
            -> toArray());
    }
}

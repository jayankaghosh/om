<?php
/**
 *
 * @package     om
 * @author      Jayanka Ghosh
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://github.com/jayankaghosh/
 */

namespace Om\Code\Generators;


class ClassGenerator
{

    const RETURN_TYPE_NONE = 'none';

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $parent = "";

    /**
     * @var MethodGenerator[]
     */
    private $methods = [];

    /**
     * @var string[]
     */
    private $comments = [];

    /**
     * @var string[]
     */
    private $traits = [];

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getParent(): string
    {
        return $this->parent;
    }

    /**
     * @param string $parent
     * @return $this
     */
    public function setParent(string $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return MethodGenerator[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @param string $body
     * @param $returnType
     * @param string $scope
     * @param array $comments
     * @return $this
     */
    public function addMethod($name, array $arguments, $body, $returnType, $scope = "public", $comments = [])
    {
        $methodGenerator = new MethodGenerator();
        $methodGenerator->setScope($scope)->setName($name)->setBody($body)->setPadding(1);
        foreach ($arguments as $argument) {
            if (isset($argument['call_by_reference'])) {
                $argumentName = '&$' . $argument['name'];
            } else {
                $argumentName = '$' . $argument['name'];
            }
            $methodGenerator->addArgument(
                $argument['type'],
                $argumentName,
                array_key_exists('default', $argument) ? $argument['default'] : new NullExpr(),
                isset($argument['nullable']) && $argument['nullable']
            );
        }
        foreach ($comments as $comment) {
            $methodGenerator->addComment($comment);
        }

        if ($returnType !== self::RETURN_TYPE_NONE) {
            $methodGenerator->setReturnType($returnType);
        }

        $this->methods[] = $methodGenerator;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getComments(): array
    {
        return $this->comments;
    }

    /**
     * @param string $comment
     * @return $this
     */
    public function addComment($comment)
    {
        $this->comments[] = $comment;
        return $this;
    }

    public function getTraits(): array
    {
        return $this->traits;
    }

    public function addTrait($trait)
    {
        $this->traits[] = $trait;
        return $this;
    }

    /**
     * @return string
     */
    public function generate() {
        $classNameWithNamespace = explode("\\", $this->getName());
        $className = array_pop($classNameWithNamespace);
        $namespace = implode("\\", $classNameWithNamespace);

        $content = "<?php\n\n";
        $content .= "namespace $namespace;\n\n";
        if (count($this->getComments())) {
            $content .= "/**";
            foreach ($this->getComments() as $comment) {
                $content .= "\n * $comment";
            }
            $content .= "\n */\n";
        }
        $content .= "class $className";
        if ($this->getParent()) {
            $content .= " extends ".$this->getParent();
        }
        $content .= " {\n";
        foreach ($this->getTraits() as $trait) {
            $content .= sprintf("\n\tuse %s;\n", $trait);
        }
        foreach ($this->getMethods() as $method) {
            $content .= "\n".$method."\n";
        }
        $content .= "\n}";
        return $content;
    }

    public function __toString()
    {
        return $this->generate();
    }

}
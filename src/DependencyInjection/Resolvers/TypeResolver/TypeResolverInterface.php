<?php
/**
 *
 * @package     om
 * @author      Jayanka Ghosh
 * @license     https://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * @link        https://github.com/jayankaghosh/
 */

namespace Om\DependencyInjection\Resolvers\TypeResolver;


interface TypeResolverInterface
{
    /**
     * @param string $type
     * @param string $value
     * @return mixed
     */
    public function resolve($type, $value);
}
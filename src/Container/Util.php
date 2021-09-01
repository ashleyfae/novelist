<?php
/**
 * Util.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Novelist\Container;

use ReflectionNamedType;

class Util
{

    /**
     * Get the class name of the given parameter's type, if possible.
     *
     * @param \ReflectionParameter $parameter
     *
     * @return string|null
     */
    public static function getParameterClassName( $parameter ) {
        $type = $parameter->getType();

        if ( ! $type instanceof ReflectionNamedType || $type->isBuiltin() ) {
            return;
        }

        $name = $type->getName();

        if ( ! is_null( $class = $parameter->getDeclaringClass() ) ) {
            if ( $name === 'self' ) {
                return $class->getName();
            }

            if ( $name === 'parent' && $parent = $class->getParentClass() ) {
                return $parent->getName();
            }
        }

        return $name;
    }

}

<?php declare(strict_types=1);

namespace OpenEngine\Mika\Core\Helpers;

class VarHelper
{
    /**
     * @param string $typeName
     * @return bool
     */
    public static function isScalar(string $typeName): bool
    {
        return \in_array($typeName, ['int', 'string', 'float', 'bool']);
    }
}

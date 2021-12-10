<?php

declare(strict_types=1);

namespace TiMacDonald\JsonApi\Concerns;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\PotentiallyMissing;
use Illuminate\Support\Collection;
use TiMacDonald\JsonApi\Support\Fields;

/**
 * @internal
 */
trait Attributes
{
    /**
     * @internal
     */
    private static bool $minimalAttributes = false;

    /**
     * @internal
     * @infection-ignore-all
     */
    public static function maximalAttributes(): void
    {
        self::$minimalAttributes = false;
    }

    /**
     * @internal
     */
    private function requestedAttributes(Request $request): Collection
    {
        return Collection::make($this->toAttributes($request))
            ->only($this->fields($request))
            ->map(
                /**
                 * @param mixed $value
                 * @return mixed
                 */
                fn ($value) => value($value)
            )
            ->reject(
                /**
                 * @param mixed $value
                 */
                fn ($value): bool => $value instanceof PotentiallyMissing && $value->isMissing()
            );
    }

    /**
     * @internal
     */
    private function fields(Request $request): ?array
    {
        $fields = Fields::getInstance()->parse($request, $this->toType($request));

        if ($fields !== null) {
            return $fields;
        }

        return self::$minimalAttributes
            ? []
            : null;
    }
}

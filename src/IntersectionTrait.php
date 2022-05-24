<?php

declare(strict_types=1);

namespace Location;

use Location\CardinalDirection\CardinalDirection;

/**
 * Trait IntersectionTrait
 *
 * @package Location
 */
trait IntersectionTrait
{
    /**
     * @return bool
     */
    public function intersects(
        GeometryInterface $geometry,
        bool $compareBounds = true
    ): bool {
        if ($compareBounds === true) {
            return $this->intersectsBounds($geometry->getBounds());
        }

        return $this->intersectsGeometry($geometry);
    }

    public function intersectsBounds(Bounds $bounds2): bool
    {
        $direction = new CardinalDirection();
        $bounds1 = $this->getBounds();

        if (
            $direction->isEastOf($bounds1->getWest(), $bounds2->getEast()) ||
            $direction->isSouthOf($bounds1->getNorth(), $bounds2->getSouth()) ||
            $direction->isWestOf($bounds1->getEast(), $bounds2->getWest()) ||
            $direction->isNorthOf($bounds1->getSouth(), $bounds2->getNorth())
        ) {
            return false;
        }

        return true;
    }

    /**
     * Two geometries intersect if:
     *
     */
    public function intersectsGeometry(Geometry $geometry): bool
    {
        if (is_a($geometry, 'Coordinate')) {
            return $this->contains($geometry);
        }

        $segments1 = $this->getSegments();
        $segments2 = $geometry->getSegments();
        $intersects = false;

        while (!$intersects) {
            foreach ($segments1 as $segment1) {
                foreach ($segments2 as $segment2) {
                    $intersects = $segment1->intersectsLine($segment2);
                }
            }
        }

        return $intersects;
    }
}

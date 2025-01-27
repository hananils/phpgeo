<?php

declare(strict_types=1);

namespace Location;

use InvalidArgumentException;
use Location\CardinalDirection\CardinalDirectionDistances;
use Location\CardinalDirection\CardinalDirectionDistancesCalculator;
use Location\Distance\DistanceInterface;
use Location\Distance\Haversine;
use Location\Formatter\Coordinate\FormatterInterface;

/**
 * Coordinate Implementation
 *
 * @author Marcus Jaschen <mjaschen@gmail.com>
 */
class Coordinate implements GeometryInterface
{
    /**
     * @var float
     */
    protected $lat;

    /**
     * @var float
     */
    protected $lng;

    /**
     * @var Ellipsoid
     */
    protected $ellipsoid;

    /**
     * @param float $lat -90.0 .. +90.0
     * @param float $lng -180.0 .. +180.0
     * @param ?Ellipsoid $ellipsoid if omitted, WGS-84 is used
     *
     * @throws InvalidArgumentException
     */
    public function __construct(float $lat, float $lng, ?Ellipsoid $ellipsoid = null)
    {
        if (! $this->isValidLatitude($lat)) {
            throw new InvalidArgumentException('Latitude value must be numeric -90.0 .. +90.0 (given: ' . $lat . ')');
        }

        if (! $this->isValidLongitude($lng)) {
            throw new InvalidArgumentException(
                'Longitude value must be numeric -180.0 .. +180.0 (given: ' . $lng . ')'
            );
        }

        $this->lat = $lat;
        $this->lng = $lng;

        if ($ellipsoid instanceof Ellipsoid) {
            $this->ellipsoid = $ellipsoid;

            return;
        }

        $this->ellipsoid = Ellipsoid::createDefault();
    }

    public function getLat(): float
    {
        return $this->lat;
    }

    public function getLng(): float
    {
        return $this->lng;
    }

    /**
     * @return array<Coordinate>
     */
    public function getPoints(): array
    {
        return [$this];
    }

    public function getEllipsoid(): Ellipsoid
    {
        return $this->ellipsoid;
    }

    /**
     * Calculates the distance between the given coordinate
     * and this coordinate.
     */
    public function getDistance(Coordinate $coordinate, DistanceInterface $calculator): float
    {
        return $calculator->getDistance($this, $coordinate);
    }

    /**
     * Calculates the cardinal direction distances from this coordinate
     * to given coordinate.
     */
    public function getCardinalDirectionDistances(
        Coordinate $coordinate,
        DistanceInterface $calculator
    ): CardinalDirectionDistances {
        return (new CardinalDirectionDistancesCalculator())
            ->getCardinalDirectionDistances($this, $coordinate, $calculator);
    }

    /**
     * Checks if two points describe the same location within an allowed distance.
     *
     * Uses the Haversine distance calculator for distance calculation as it's
     * precise enough for short-distance calculations.
     *
     * @see Haversine
     */
    public function hasSameLocation(Coordinate $coordinate, float $allowedDistance = .001): bool
    {
        return $this->getDistance($coordinate, new Haversine()) <= $allowedDistance;
    }

    /**
     * Checks if this point intersects a given geometry.
     */
    public function intersects(
        GeometryInterface $geometry,
        bool $precise = false
    ): bool {
        if (is_a($geometry, 'Location\Coordinate')) {
            return $this->equals($geometry);
        }

        return $geometry->contains($this);
    }

    /**
     * Checks if two coordinates are equal.
     */
    public function equals(Coordinate $coordinate)
    {
        return $coordinate->getLng() === $this->lng &&
            $coordinate->getLat() === $this->lat;
    }

    public function format(FormatterInterface $formatter): string
    {
        return $formatter->format($this);
    }

    protected function isValidLatitude(float $latitude): bool
    {
        return $this->isNumericInBounds($latitude, -90.0, 90.0);
    }

    protected function isValidLongitude(float $longitude): bool
    {
        return $this->isNumericInBounds($longitude, -180.0, 180.0);
    }

    /**
     * Checks if the given value is (1) numeric, and (2) between lower
     * and upper bounds (including the bounds values).
     */
    protected function isNumericInBounds(float $value, float $lower, float $upper): bool
    {
        return !($value < $lower || $value > $upper);
    }
}

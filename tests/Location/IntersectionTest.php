<?php

declare(strict_types=1);

namespace Location;

use Location\Point;
use Location\Polygon;
use Location\Line;
use PHPUnit\Framework\TestCase;

class IntersectionTest extends TestCase
{
    protected $polygon;

    protected function setUp(): void
    {
        $coordinates = [
            [52.56571, 9.998658],
            [52.369576, 9.560167],
            [52.102252, 9.62397],
            [51.983342, 10.132727],
            [52.083498, 10.699142],
            [52.457087, 10.590607],
            [52.56571, 9.998658]
        ];

        $this->polygon = new Polygon();
        foreach ($coordinates as $coordinate) {
            $this->polygon->addPoint(new Point($coordinate[0], $coordinate[1]));
        }
    }

    public function testLineIntersections(): void
    {
        // Lines
        $lineCenterCrossing = new Line(
            new Point(52.237594, 9.287635),
            new Point(52.258154, 11.010313)
        );
        $lineTop = new Line(
            new Point(52.62456, 9.455537),
            new Point(52.608249, 10.734953)
        );
        $lineBottom = new Line(
            new Point(51.880405, 9.613366),
            new Point(51.907345, 10.724879)
        );
        $lineRight = new Line(
            new Point(52.720262, 10.627496),
            new Point(51.859671, 10.812188)
        );

        // By bounds
        $this->assertTrue(
            $lineCenterCrossing->intersects($this->polygon, false)
        );
        $this->assertFalse($lineTop->intersects($this->polygon, false));
        $this->assertFalse($lineBottom->intersects($this->polygon, false));
        $this->assertTrue($lineRight->intersects($this->polygon, false));

        // By shape
        $this->assertTrue(
            $lineCenterCrossing->intersects($this->polygon, true)
        );
        $this->assertFalse($lineTop->intersects($this->polygon, true));
        $this->assertFalse($lineBottom->intersects($this->polygon, true));
        $this->assertFalse($lineRight->intersects($this->polygon, true));
    }

    public function testPointIntersections(): void
    {
        // Points
        $pointContained = new Point(52.328745, 10.151638);
        $pointOutside = new Point(52.549057, 10.475242);
        $pointOutsideLine = new Point(52.252717, 10.728334);

        // By bounds
        $this->assertTrue($pointContained->intersects($this->polygon, false));
        $this->assertFalse($pointOutside->intersects($this->polygon, false));
        $this->assertFalse(
            $pointOutsideLine->intersects($this->polygon, false)
        );

        // By shape
        $this->assertTrue($pointContained->intersects($this->polygon, true));
        $this->assertFalse($pointOutside->intersects($this->polygon, true));
        $this->assertFalse($pointOutsideLine->intersects($this->polygon, true));
    }

    public function testPolygonIntersections(): void
    {
        // Polygons
        $polygonLeftIntersecting = new Polygon();
        $coordinates = [
            [51.990136, 9.438747],
            [52.493904, 9.408525],
            [52.518432, 9.77791],
            [51.807794, 9.871935],
            [51.809766, 9.886408],
            [51.990136, 9.438747]
        ];
        foreach ($coordinates as $coordinate) {
            $polygonLeftIntersecting->addPoint(
                new Point($coordinate[0], $coordinate[1])
            );
        }

        $polygonRightOutside = new Polygon();
        $coordinates = [
            [52.533043, 10.747941],
            [52.326314, 10.787294],
            [52.317063, 11.117259],
            [52.317063, 11.117259],
            [52.533043, 10.747941]
        ];
        foreach ($coordinates as $coordinate) {
            $polygonRightOutside->addPoint(
                new Point($coordinate[0], $coordinate[1])
            );
        }

        // By bounds
        $this->assertTrue(
            $polygonLeftIntersecting->intersects($this->polygon, false)
        );
        $this->assertFalse(
            $polygonRightOutside->intersects($this->polygon, false)
        );

        // By shape
        $this->assertTrue(
            $polygonLeftIntersecting->intersects($this->polygon, true)
        );
        $this->assertFalse(
            $polygonRightOutside->intersects($this->polygon, true)
        );
    }
}

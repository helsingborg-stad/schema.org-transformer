<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\StratsysTransform;

final class StratsysTransformTest extends TestCase
{
    protected array $data;
    protected StratsysTransform $model;

    protected function setUp(): void
    {
        $this->model = new StratsysTransform();

        $this->data = [
            "header" => [
                "Transformation_Namn",
                "Omrade_Namn",
                "Initiativ_Status",
                "Initiativ_Namn",
                "Initiativ_Beslutspunkt",
                "Initiativ_Sammanfattning",
                "Initiativ_Avgransningar",
                "Initiativ_Estimeradbudget",
                "Initiativ_Kontaktperson",
                "Initiativ_Ansvarigforinitiativ",
                "Initiativ_Startdatum",
                "Initiativ_Slutdatum",
                "Initiativ_Finansiering",
                "Initiativ_Samverkanspartner",
                "Initiativ_Utmaningar",
                "Initiativ_Lanktillbild",
                "Initiativ_Dokument",
                "Initiativ_Andrafonsterikon",
                "Initiativ_Vad",
                "Initiativ_Hur",
                "Initiativ_Varfor",
                "Initiativ_Invanarinvolvering",
                "Initiativ_Enhet",
                "Initiativ_InterntID",
                "Effektmal_FargNamn",
                "Effektmal_Malvarde",
                "Effektmal_Utfall"
            ],
            "values" => [
                [
                    "Transformation_Namn",
                    "Omrade_Namn",
                    "Initiativ_Status",
                    "Initiativ_Namn",
                    "Initiativ_Beslutspunkt",
                    "Initiativ_Sammanfattning",
                    "Initiativ_Avgransningar",
                    "Initiativ_Estimeradbudget",
                    "Initiativ_Kontaktperson",
                    "Initiativ_Ansvarigforinitiativ",
                    "Initiativ_Startdatum",
                    "Initiativ_Slutdatum",
                    "Initiativ_Finansiering",
                    "Initiativ_Samverkanspartner",
                    "Initiativ_Utmaningar",
                    "Initiativ_Lanktillbild",
                    "Initiativ_Dokument",
                    "Initiativ_Andrafonsterikon",
                    "Initiativ_Vad",
                    "Initiativ_Hur",
                    "Initiativ_Varfor",
                    "Initiativ_Invanarinvolvering",
                    "Initiativ_Enhet",
                    "Initiativ_InterntID",
                    "Effektmal_FargNamn",
                    "Effektmal_Malvarde",
                    "Effektmal_Utfall"
                ]
            ]
        ];
    }
    public function testStratsysTransform(): void
    {
        $this->assertEquals([
            [
                "@context"    => "https://schema.org",
                "@type"       => "Project",
                "@id"         => "Initiativ_InterntID",
                "name"        => "Initiativ_Namn",
                "description" => implode([
                    "<h2>Vad?</h2><p>Initiativ_Vad</p>",
                    "<h2>Hur?</h2><p>Initiativ_Hur</p>",
                    "<h2>Varför?</h2><p>Initiativ_Varfor</p>",
                    "<h2>Effektmål</h2><p><ul><li>FargNamn</li></ul></p>",
                    "<h2>Avgränsningar</h2><p>Initiativ_Avgransningar</p>",
                    "<h2>Utmaningar</h2><p><ul><li>Initiativ_Utmaningar</li></ul></p>"
                ]),
                "image"       => "Initiativ_Lanktillbild",
                "funding"     => [
                    "@type"  => "MonetaryGrant",
                    "amount" => "Initiativ_Estimeradbudget"
                ],
                "department"  => [
                    "@type" => "Organization",
                    "name"  => "Initiativ_Enhet"
                ],
                "employee"    => [
                    "@type"         => "Person",
                    "alternateName" => "Initiativ_Kontaktperson"
                ],
                "@meta"       => [
                    [
                        "@type" => "PropertyValue",
                        "name"  => "technology",
                        "value" => "Transformation_Namn"
                    ],
                    [
                        "@type" => "PropertyValue",
                        "name"  => "status",
                        "value" => "Initiativ_Status"
                    ],
                    [
                        "@type" => "PropertyValue",
                        "name"  => "progress",
                        "value" => 0
                    ],
                    [
                        "@type" => "PropertyValue",
                        "name"  => "category",
                        "value" => "Omrade_Namn"
                    ]
                ],
                "@version"    => "c6ae4e6b3fe52e84fdace1b520066c8b"
            ]
        ], $this->model->transform($this->data));
    }
    public function testTransformProgress(): void
    {
        $this->assertEquals(0, $this->model->getProgress(""));
        $this->assertEquals(25, $this->model->getProgress("Idé"));
        $this->assertEquals(50, $this->model->getProgress("Pilot"));
        $this->assertEquals(75, $this->model->getProgress("Skala upp"));
        $this->assertEquals(100, $this->model->getProgress("Realiserad"));
        $this->assertEquals(0, $this->model->getProgress("Avbruten"));
    }
    public function testTransformImageUrl(): void
    {
        $this->assertEquals("", $this->model->transformImage(""));
        $this->assertEquals("test.jpg", $this->model->transformImage("test.jpg"));
        $this->assertEquals("test.jpg", $this->model->transformImage("test.webp"));
        $this->assertEquals("test.jpg", $this->model->transformImage("test.WEBP"));
    }
    public function testSanitizeString(): void
    {
        $this->assertEquals("<br/> <br/> %", $this->model->sanitizeString("%0A %0a %25"));
    }
    public function testArrayToList(): void
    {
        $this->assertEquals("<ul><li>test1</li><li>test2</li><li>test3</li><li>test4</li></ul>", $this->model->arrayToList(["test1", "test2", " test3", "  test4  "]));
    }
    public function testAppend(): void
    {
        $this->assertEquals([], $this->model->append([], ""));
        $this->assertEquals(["A", "B"], $this->model->append([], "A;B"));
        $this->assertEquals(["A", "B", "C"], $this->model->append(["A"], "B;C"));
        $this->assertEquals(["A"], $this->model->append(["A"], ""));
    }
}

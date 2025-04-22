<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\StratsysTransform;
use Spatie\Snapshots\MatchesSnapshots;

final class StratsysTransformTest extends TestCase
{
    use MatchesSnapshots;

    protected array $data;
    protected StratsysTransform $model;


    protected function setUp(): void
    {
        $this->model = new StratsysTransform("");

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
                "Effektmal_Utfall",
                "Initiativ_Synligaenheter"
            ],
            "values" => [
                [
                    "Transformation_Namn",
                    "Omrade_Namn",
                    "Idé",
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
                    "Effektmal_Utfall",
                    "Initiativ_Synligaenheter",
                ]
            ]
        ];
    }
    public function testStratsysTransform(): void
    {
        $this->assertMatchesJsonSnapshot(json_encode($this->model->transform($this->data), JSON_PRETTY_PRINT));
    }
    public function testTransformProgress(): void
    {
        $this->assertEquals(0, $this->model->getStatus("")->getProperty("number"));
        $this->assertEquals(25, $this->model->getStatus("Idé")->getProperty("number"));
        $this->assertEquals("Idé", $this->model->getStatus("Idé")->getProperty("name"));
        $this->assertEquals(50, $this->model->getStatus("Pilot")->getProperty("number"));
        $this->assertEquals("Pilot", $this->model->getStatus("Pilot")->getProperty("name"));
        $this->assertEquals(75, $this->model->getStatus("Skala upp")->getProperty("number"));
        $this->assertEquals("Skala upp", $this->model->getStatus("Skala upp")->getProperty("name"));
        $this->assertEquals(100, $this->model->getStatus("Realiserad")->getProperty("number"));
        $this->assertEquals("Realiserad", $this->model->getStatus("Realiserad")->getProperty("name"));
        $this->assertEquals(0, $this->model->getStatus("Avbruten")->getProperty("number"));
        $this->assertEquals("Avbruten", $this->model->getStatus("Avbruten")->getProperty("name"));
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

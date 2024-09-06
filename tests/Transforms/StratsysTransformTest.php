<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\StratsysTransform;

final class StratsysTransformTest extends TestCase
{
    protected array $data;

    protected function setUp(): void
    {
        $this->data = [
            "header" => [
                "Transformation_Namn",
                "Omrade_Namn",
                "Initiativ_Status",
                "Initiativ_Namn",
                "Initiativ_Beslutspunkt",
                "Initiativ_Sammanfattning",
                "Initiativ_Avgransningar",
                "Initiativ_Budgetuppskattning",
                "Initiativ_Kontaktperson",
                "Initiativ_Ansvarigforinitiativ",
                "Initiativ_Startdatum",
                "Initiativ_Slutdatum",
                "Initiativ_Finansiering",
                "Initiativ_Samverkanspartner",
                "Initiativ_Utmaningar",
                "Initiativ_Bildtest",
                "Initiativ_Dokument",
                "Initiativ_Andrafonsterikon",
                "Initiativ_Vad",
                "Initiativ_Hur",
                "Initiativ_Varfor",
                "Initiativ_Invanarinvolvering",
                "Initiativ_Enhet",
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
                    "Initiativ_Budgetuppskattning",
                    "Initiativ_Kontaktperson",
                    "Initiativ_Ansvarigforinitiativ",
                    "Initiativ_Startdatum",
                    "Initiativ_Slutdatum",
                    "Initiativ_Finansiering",
                    "Initiativ_Samverkanspartner",
                    "Initiativ_Utmaningar",
                    "Initiativ_Bildtest",
                    "Initiativ_Dokument",
                    "Initiativ_Andrafonsterikon",
                    "Initiativ_Vad",
                    "Initiativ_Hur",
                    "Initiativ_Varfor",
                    "Initiativ_Invanarinvolvering",
                    "Initiativ_Enhet",
                    "Effektmal_FargNamn",
                    "Effektmal_Malvarde",
                    "Effektmal_Utfall"
                ]
            ]
        ];
    }
    public function testStratsysTransform(): void
    {
        $model = new StratsysTransform();
        $this->assertEquals($model->transform($this->data), [
            [
                "@context" => "https://schema.org",
                "@type" => "Project",
                "name" => "Initiativ_Namn",
                "description" => implode([
                    "<h2>Vad</h2><p>Initiativ_Vad</p>",
                    "<h2>Hur</h2><p>Initiativ_Hur</p>",
                    "<h2>Varför</h2><p>Initiativ_Varfor</p>",
                    "<h2>Effektmål</h2><p>Effektmal_FargNamn</p>",
                    "<h2>Avgränsningar</h2><p>Initiativ_Avgransningar</p>",
                    "<h2>Utmaningar</h2><p>Initiativ_Utmaningar</p>"
                ]),
                "image" => "Initiativ_Bildtest",
                "funding" => [
                    "@type" => "MonetaryGrant",
                    "amount" => "Initiativ_Budgetuppskattning"
                ],
                "department" => [
                    "@type" => "Organization",
                    "name" => "Initiativ_Enhet"
                ],
                "employee" => [
                    "@type" => "Person",
                    "alternateName" => "Initiativ_Kontaktperson"
                ],
                "@meta" => [
                    [
                        "@type" => "PropertyValue",
                        "name" => "technology",
                        "value" => "Omrade_Namn"
                    ],
                    [
                        "@type" => "PropertyValue",
                        "name" => "status",
                        "value" => "Initiativ_Status"
                    ],
                    [
                        "@type" => "PropertyValue",
                        "name" => "progress",
                        "value" => 0
                    ],
                    [
                        "@type" => "PropertyValue",
                        "name" => "category",
                        "value" => "Transformation_Namn"
                    ]
                ],
                "@version" => "ccaab47c5f653ac32c98f7e4871b060d"
            ]
        ]);
    }
}

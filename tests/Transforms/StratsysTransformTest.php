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
                "@type" => "Article",
                "headline" => "Initiativ_Namn",
                "abstract" => "Initiativ_Sammanfattning",
                "articleBody" => [
                    "Initiativ_Vad",
                    "Initiativ_Hur",
                    "Initiativ_Varfor"
                ],
                "articleSection" => "Omrade_Namn",
                "genre" => "Transformation_Namn",
                "creativeWorkStatus" => "Initiativ_Status",
                "image" => "Initiativ_Bildtest",
                "@objectives" => [
                    "Effektmal_FargNamn"
                ],
                "@demarcations" => [
                    "Initiativ_Avgransningar"
                ],
                "@challenges" => [
                    "Initiativ_Utmaningar"
                ],
                "funding" => [
                    "@type" => "MonetaryGrant",
                    "amount" => "Initiativ_Budgetuppskattning"
                ],
                "sourceOrganization" => [
                    "@type" => "Organization",
                    "name" => "Initiativ_Enhet"
                ],
                "publisher" => [
                    "@type" => "Person",
                    "alternateName" => "Initiativ_Kontaktperson"
                ],
                "@version" => "8eacc3948d6bddaee5401a8fa532bf29"
            ]
        ]);
    }
}

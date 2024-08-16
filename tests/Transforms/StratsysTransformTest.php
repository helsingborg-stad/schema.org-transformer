<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\StratsysTransform;

final class StratsysTransformTest extends TestCase
{
    protected array $data;

    protected function setUp(): void
    {
        $this->data = ["Result" => [
            [
                "ParentId" => "315836",
                "Department" => [
                    "Id" => "1",
                    "ParentId" => null,
                    "Name" => "Helsingborgs stad",
                    "ShortName" => "Helsingborgs stad"
                ],
                "Scorecard" => [
                    "Id" => "66",
                    "Name" => "Portföljstyrning"
                ],
                "ScorecardColumn" => [
                    "Id" => "715",
                    "ScorecardId" => "66",
                    "Scorecard" => [
                        "Id" => "66",
                        "Name" => "Portföljstyrning"
                    ],
                    "Name" => "Område",
                    "Definition" => "",
                    "Index" => 2,
                    "NodeType" => "Goal"
                ],
                "DescriptionFields" => [],
                "Keywords" => [],
                "Responsibles" => null,
                "CurrentStatus" => null,
                "Statuses" => null,
                "SortOrder" => 1,
                "DocumentFolder" => [
                    "Folders" => [],
                    "Documents" => []
                ],
                "Id" => "315861",
                "Name" => "Integration"
            ],
            [
                "ParentId" => "315861",
                "Department" => [
                    "Id" => "1",
                    "ParentId" => null,
                    "Name" => "Helsingborgs stad",
                    "ShortName" => "Helsingborgs stad"
                ],
                "Scorecard" => [
                    "Id" => "66",
                    "Name" => "Portföljstyrning"
                ],
                "ScorecardColumn" => [
                    "Id" => "708",
                    "ScorecardId" => "66",
                    "Scorecard" => [
                        "Id" => "66",
                        "Name" => "Portföljstyrning"
                    ],
                    "Name" => "Initiativ",
                    "Definition" => "",
                    "Index" => 3,
                    "NodeType" => "Activity"
                ],
                "DescriptionFields" => [
                    [
                        "DescriptionField" => [
                            "Id" => "218",
                            "Name" => "Sammanfattning",
                            "Description" => "Max 2 meningar om vad initiativet ska leverera."
                        ],
                        "TextValue" => "sammanfattning text"
                    ],
                    [
                        "DescriptionField" => [
                            "Id" => "220",
                            "Name" => "Budgetuppskattning",
                            "Description" => "Uppskatta projektets totala kostnader och ange i kr."
                        ],
                        "TextValue" => "Budget text"
                    ],
                    [
                        "DescriptionField" => [
                            "Id" => "217",
                            "Name" => "Avgränsningar",
                            "Description" => "Ange vad som definierar projektets omfattning, vad som inkluderas/exkluderas. Vad syftar projektet till att uppnå och vad faller inte inom dess ansvarsområde? Genom att fastställa vad som ingår och inte kan projektteamet, intressenter och andra berörda parter få en gemensam förståelse för projektets begränsningar och fokusera på att uppnå målen inom dessa gränser."
                        ],
                        "TextValue" => "vet ej"
                    ],
                    [
                        "DescriptionField" => [
                            "Id" => "223",
                            "Name" => "Varför?",
                            "Description" => ""
                        ],
                        "TextValue" => "Varför text"
                    ],
                    [
                        "DescriptionField" => [
                            "Id" => "224",
                            "Name" => "Hur?",
                            "Description" => ""
                        ],
                        "TextValue" => "Hur text"
                    ],
                    [
                        "DescriptionField" => [
                            "Id" => "225",
                            "Name" => "Vad?",
                            "Description" => ""
                        ],
                        "TextValue" => "Vad text"
                    ],
                    [
                        "DescriptionField" => [
                            "Id" => "226",
                            "Name" => "Invånarinvolvering",
                            "Description" => "Hur har invånarna involverats i initiativet?"
                        ],
                        "TextValue" => "Invånarinvolvering text"
                    ],
                    [
                        "DescriptionField" => [
                            "Id" => "227",
                            "Name" => "Bildtest",
                            "Description" => ""
                        ],
                        "TextValue" => null
                    ]
                ],
                "Keywords" => [
                    [
                        "Id" => "1329",
                        "Name" => "Nytt initiativ",
                        "KeywordGroup" => [
                            "Id" => "123",
                            "Name" => "Beslutspunkt"
                        ]
                    ],
                    [
                        "Id" => "1347",
                        "Name" => "Prioriterat",
                        "KeywordGroup" => [
                            "Id" => "126",
                            "Name" => "Prioritering (manuell)"
                        ]
                    ],
                    [
                        "Id" => "1352",
                        "Name" => "Stadens innovationsfond",
                        "KeywordGroup" => [
                            "Id" => "127",
                            "Name" => "Finansiering"
                        ]
                    ],
                    [
                        "Id" => "1354",
                        "Name" => "Ingen finansiering",
                        "KeywordGroup" => [
                            "Id" => "127",
                            "Name" => "Finansiering"
                        ]
                    ],
                    [
                        "Id" => "1358",
                        "Name" => "Näringsliv",
                        "KeywordGroup" => [
                            "Id" => "128",
                            "Name" => "Samverkanspartner"
                        ]
                    ],
                    [
                        "Id" => "1361",
                        "Name" => "Förening",
                        "KeywordGroup" => [
                            "Id" => "128",
                            "Name" => "Samverkanspartner"
                        ]
                    ],
                    [
                        "Id" => "1366",
                        "Name" => "Hur kan vi blir bättre på att attrahera, behålla och utveckla kompetens?",
                        "KeywordGroup" => [
                            "Id" => "129",
                            "Name" => "Utmaningar"
                        ]
                    ],
                    [
                        "Id" => "1369",
                        "Name" => "Hur kan vi öka invånarnas trygghet och säkerhet?",
                        "KeywordGroup" => [
                            "Id" => "129",
                            "Name" => "Utmaningar"
                        ]
                    ],
                    [
                        "Id" => "1379",
                        "Name" => "Ja",
                        "KeywordGroup" => [
                            "Id" => "130",
                            "Name" => "Vill du publicera initiativet i innovationsdatabasen?"
                        ]
                    ],
                    [
                        "Id" => "1383",
                        "Name" => "Recolab",
                        "KeywordGroup" => [
                            "Id" => "131",
                            "Name" => "Testbädd"
                        ]
                    ],
                    [
                        "Id" => "1400",
                        "Name" => "Helsingborg ska ha en hög sysselsättning där fler kommer i arbete ",
                        "KeywordGroup" => [
                            "Id" => "132",
                            "Name" => "Politisk inriktning"
                        ]
                    ],
                    [
                        "Id" => "1403",
                        "Name" => "Ja",
                        "KeywordGroup" => [
                            "Id" => "133",
                            "Name" => "Får initiativet stöttning från avdelningen för innovation- och transformation (SLF)?"
                        ]
                    ],
                    [
                        "Id" => "1406",
                        "Name" => "Medel",
                        "KeywordGroup" => [
                            "Id" => "134",
                            "Name" => "Innovationshöjd"
                        ]
                    ]
                ],
                "Responsibles" => null,
                "CurrentStatus" => null,
                "Statuses" => null,
                "SortOrder" => 7,
                "DocumentFolder" => [
                    "Folders" => [],
                    "Documents" => []
                ],
                "Id" => "322022",
                "Name" => "test api"
            ]
        ]];
    }
    public function testStratsysTransform(): void
    {
        $model = new StratsysTransform();
        $this->assertEquals($model->transform($this->data), [
            [
                "@context" => "https://schema.org",
                "@type" => "Article",
                "headline" => "test api",
                "articleSection" => "Integration",
                "articleBody" => "sammanfattning text",
                "@budget" => "Budget text",
                "@limitations" => "vet ej",
                "@engagement" => "Invånarinvolvering text",
                "@facets" => [
                    "Beslutspunkt" => [
                        "Nytt initiativ"
                    ],
                    "Prioritering (manuell)" => [
                        "Prioriterat"
                    ],
                    "Finansiering" => [
                        "Stadens innovationsfond",
                        "Ingen finansiering"
                    ],
                    "Samverkanspartner" => [
                        "Näringsliv",
                        "Förening"
                    ],
                    "Utmaningar" => [
                        "Hur kan vi blir bättre på att attrahera, behålla och utveckla kompetens?",
                        "Hur kan vi öka invånarnas trygghet och säkerhet?"
                    ],
                    "Vill du publicera initiativet i innovationsdatabasen?" => [
                        "Ja"
                    ],
                    "Testbädd" => [
                        "Recolab"
                    ],
                    "Politisk inriktning" => [
                        "Helsingborg ska ha en hög sysselsättning där fler kommer i arbete "
                    ],
                    "Får initiativet stöttning från avdelningen för innovation- och transformation (SLF)?" => [
                        "Ja"
                    ],
                    "Innovationshöjd" => [
                        "Medel"
                    ]
                ],
                "@version" => "ac84e6764d2c5d82fe0b5269beb0d436"
            ]

        ]);
    }
}

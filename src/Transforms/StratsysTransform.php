<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use Spatie\SchemaOrg\Schema;

class StratsysTransform implements AbstractDataTransform
{
    private array $indexRef;

    protected function getProgress(string $status): int
    {
        switch ($status) {
            case 'Idé':
                return 25;
            case 'Pilot':
                return 50;
            case 'Skala upp':
                return 75;
            case 'Avbruten':
                return 0;
            case 'Realiserad':
                return 100;
        }
        return 0;
    }
    protected function transformImage(string $data): string
    {
        return str_replace(".webp", ".jpg", $data);
    }
    protected function sanitizeString(string $data): string
    {
        return str_ireplace(["%0A", "%25"], ["<br/>", "%"], $data);
    }
    protected function stringToList(string $data): string
    {
        if (!empty(trim($data))) {
            return "<ul>" .
                join(array_map(function ($item) {
                    return "<li>" . $item . "</li>";
                }, explode(";", $data)))
                . "</ul>";
        }
        return "";
    }
    protected function transformOrganisation(string $data): string
    {
        return str_replace([
            "Barn- och utbildningsnämnden",
            "Idrotts- och fritidsnämnden",
            "nämnden",
            "Kommunstyrelsen"
        ], [
            "Skol- och fritidsförvaltningen",
            "Skol- och fritidsförvaltningen",
            "förvaltningen",
            "Stadsledningsförvaltningen"
        ], $data);
    }
    public function transform(array $data): array
    {
        $this->indexRef = $data["header"];
        $output =         [];

        // Combine keys and values
        $combined = array_map(function ($item) {
            return array_combine($this->indexRef, $item);
        }, $data["values"]);

        // Filter duplicates, combine fields
        $lookup = [];
        array_walk($combined, function ($item) use (&$lookup) {
            $id = $item['Initiativ_InterntID'] ?? "";
            if (empty($id)) {
                return;
            }
            $key = array_search(
                $id,
                array_column($lookup, 'Initiativ_InterntID')
            );
            if ($key === false) {
                $item["Effektmal_FargNamn"] = substr($item["Effektmal_FargNamn"], 10);
                $item["Initiativ_Utmaningar"] = $this->stringToList($item["Initiativ_Utmaningar"]);
                $lookup[] = $item;
            } else {
                $lookup[$key]["Effektmal_FargNamn"] .= "<br/>" . substr($item["Effektmal_FargNamn"], 10);
            }
        });

        foreach ($lookup as $row) {
            $project = Schema::project()->name($row["Initiativ_Namn"] ?? "");
            $project->description($this->getDescriptionValueFromRow($row));
            $project->image($this->transformImage($row["Initiativ_Lanktillbild"] ?? ""));
            $project->setProperty('@id', $row['Initiativ_InterntID'] ?? "");

            $funding = Schema::monetaryGrant()->amount($row["Initiativ_Estimeradbudget"] ?? "");
            $project->funding($funding);

            $organization = Schema::organization()->name($this->transformOrganisation($row["Initiativ_Enhet"] ?? ""));
            $project->department($organization ?? "");

            $contact = Schema::person()
                ->alternateName($row["Initiativ_Kontaktperson"] ?? "");
            $project->employee($contact);

            $project->setProperty('@meta', [
                Schema::propertyValue()->name('technology')->value($row["Transformation_Namn"] ?? ""),
                Schema::propertyValue()->name('status')->value($row["Initiativ_Status"] ?? "N/A"),
                Schema::propertyValue()->name('progress')->value($this->getProgress($row["Initiativ_Status"] ?? "0")), // phpcs:ignore
                Schema::propertyValue()->name('category')->value($row["Omrade_Namn"] ?? ""),
            ]);
            $project->setProperty('@version', md5(json_encode($project->toArray())));
            $output[] = $project->toArray();
        }
        return $output;
    }

    private function getDescriptionValueFromRow($row): string
    {
        $descriptionArray = [
            'Initiativ_Vad'           => '<h2>Vad?</h2>',
            'Initiativ_Hur'           => '<h2>Hur?</h2>',
            'Initiativ_Varfor'        => '<h2>Varför?</h2>',
            'Effektmal_FargNamn'      => '<h2>Effektmål</h2>',
            'Initiativ_Avgransningar' => '<h2>Avgränsningar</h2>',
            'Initiativ_Utmaningar'    => '<h2>Utmaningar</h2>',
        ];

        return implode(array_map(
            fn($key, $htmlTitle) =>
            !empty($row[$key]) ? $htmlTitle . '<p>' . $this->sanitizeString($row[$key]) . '</p>' : '',
            array_keys($descriptionArray),
            array_values($descriptionArray)
        ));
    }
}

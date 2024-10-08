<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use Spatie\SchemaOrg\Schema;

class StratsysTransform extends TransformBase implements AbstractDataTransform
{
    public function __construct(string $idprefix)
    {
        parent::__construct($idprefix);
    }

    public function getProgress(string $status): int
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
    public function transformImage(string $data): string
    {
        return str_ireplace(".webp", ".jpg", $data);
    }
    public function sanitizeString(string $data): string
    {
        return str_ireplace(["%0A", "%25"], ["<br/>", "%"], $data);
    }
    public function append(array $current, string $data): array
    {
        $input = trim($data);
        // Ignore empty
        if (empty($input)) {
            return $current;
        }
        $array = explode(";", $input);
        if (empty($current)) {
            return $array;
        }
        // Check for duplicate text
        $array = array_filter($array, function ($row) use ($current) {
            if (in_array($row, $current)) {
                return false;
            }
            return true;
        });

        return [...$current, ...$array];
    }
    public function arrayToList(array $data): string
    {
        if (!empty($data)) {
            return "<ul>" .
                join(array_map(function ($row) {
                    return "<li>" . trim($row) . "</li>";
                }, $data))
                . "</ul>";
        }
        return "";
    }
    public function stringToList(string $data): string
    {
        if (!empty($data)) {
            // Concatenate string
            $clean = str_replace("\xC2\xA0\xC2\xA0•", " / ", $data);
            $clean = trim(str_replace("\xC2\xA0", "", $clean), "\n\r\t\v\0•");
            return $this->arrayToList(explode("•", $clean));
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
        // Combine keys and values
        $combined = array_map(function ($row) use ($data) {
            return array_combine($data["header"], $row);
        }, $data["values"]);

        // Remove initiatives without id
        $combined = array_filter($combined, function ($row) {
            return !empty(trim($row['Initiativ_InterntID'] ?? ""));
        });

        // Merge rows
        $lookup = [];
        array_walk($combined, function ($row) use (&$lookup) {
            $id = trim($row['Initiativ_InterntID']);

            $key = array_search(
                $id,
                array_column($lookup, 'Initiativ_InterntID')
            );
            // Extract mergeable items
            // ==================================
            // Remove first 10 characters (Which is always "Inga data ")
            $performance = trim(substr($row["Effektmal_FargNamn"] ?? "", 10));
            $challenges = trim($row["Initiativ_Utmaningar"] ?? "");
            $categories = trim($row["Omrade_Namn"] ?? "");
            $technologies = trim($row["Transformation_Namn"] ?? "");

            if ($key === false) {
                $row["Effektmal_FargNamn"] = $this->append([], $performance);
                $row["Initiativ_Utmaningar"] = $this->append([], $challenges);
                $row["Omrade_Namn"] = $this->append([], $categories);
                $row["Transformation_Namn"] = $this->append([], $technologies);
                $lookup[] = $row;
            } else {
                // Append to array
                $lookup[$key]["Effektmal_FargNamn"] = $this->append($lookup[$key]["Effektmal_FargNamn"], $performance);
                $lookup[$key]["Initiativ_Utmaningar"] = $this->append($lookup[$key]["Initiativ_Utmaningar"], $challenges);
                $lookup[$key]["Omrade_Namn"] = $this->append($lookup[$key]["Omrade_Namn"], $categories);
                $lookup[$key]["Transformation_Namn"] = $this->append($lookup[$key]["Transformation_Namn"], $technologies);
            }
        });
        // Expand merged strings
        array_walk($lookup, function (&$row) {
            $row["Effektmal_FargNamn"] = $this->arrayToList($row["Effektmal_FargNamn"]);
            $row["Initiativ_Utmaningar"] = $this->arrayToList($row["Initiativ_Utmaningar"]);
            $row["Initiativ_Synligaenheter"] = $this->stringToList($row["Initiativ_Synligaenheter"] ?? "");
        });

        $output = [];
        foreach ($lookup as $row) {
            $project = Schema::project()->name($row["Initiativ_Namn"] ?? "");
            $project->description($this->getDescriptionValueFromRow($row));
            $project->image($this->transformImage($row["Initiativ_Lanktillbild"] ?? ""));
            $project->foundingDate($row["Initiativ_Startdatum"] ?? "");
            $project->setProperty('@id', $this->formatId($row['Initiativ_InterntID'] ?? ""));

            $funding = Schema::monetaryGrant()->amount($row["Initiativ_Estimeradbudget"] ?? "");
            $project->funding($funding);

            $organization = Schema::organization()->name($this->transformOrganisation($row["Initiativ_Enhet"] ?? ""));
            $project->department($organization ?? "");

            $contact = Schema::person()
                ->alternateName($row["Initiativ_Kontaktperson"] ?? "");
            $project->employee($contact);

            $categories = array_map(function ($category) {
                return Schema::propertyValue()->name('category')->value($category);
            }, $row["Omrade_Namn"]);
            $technologies = array_map(function ($technology) {
                return Schema::propertyValue()->name('technology')->value($technology);
            }, $row["Transformation_Namn"]);

            $project->setProperty('@meta', [
                ...$categories,
                ...$technologies,
                Schema::propertyValue()->name('status')->value($row["Initiativ_Status"] ?? "N/A"),
                Schema::propertyValue()->name('progress')->value($this->getProgress($row["Initiativ_Status"] ?? "0")),
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
            'Initiativ_Synligaenheter' => '<h2>Drivs av</h2>'
        ];

        return implode(array_map(
            fn($key, $htmlTitle) =>
            !empty($row[$key]) ? $htmlTitle . '<p>' . $this->sanitizeString($row[$key]) . '</p>' : '',
            array_keys($descriptionArray),
            array_values($descriptionArray)
        ));
    }
}

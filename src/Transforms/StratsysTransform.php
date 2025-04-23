<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use Municipio\Schema\Contracts\ProgressStatusContract;
use Municipio\Schema\ProgressStatus;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use Municipio\Schema\Schema;

class StratsysTransform extends TransformBase implements AbstractDataTransform
{
    public function __construct(string $idprefix)
    {
        parent::__construct($idprefix);
    }

    public function getStatus(string $status): ProgressStatus
    {
        $progressStatus = Schema::progressStatus()->minNumber(0)->maxNumber(100)->name($status);

        return match ($status) {
            'Realiserad'    => $progressStatus->number(100),
            'Skala upp'     => $progressStatus->number(75),
            'Pilot'         => $progressStatus->number(50),
            'Idé'           => $progressStatus->number(25),
            'Avbruten'      => $progressStatus->number(0),
            default         => $progressStatus->name('Status ej angiven')->number(0),
        };
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
    public function transformPerformance(string $data): string
    {
        return preg_replace("/^(Röd|Grön|Gul|Inga data)/i", "", $data);
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
            $performance  = trim($this->transformPerformance($row["Effektmal_Namn"] ?? ""));
            $challenges   = trim($row["Initiativ_Utmaningar"] ?? "");
            $categories   = trim($row["Omrade_Namn"] ?? "");
            $technologies = trim($row["Transformation_Namn"] ?? "");

            if ($key === false) {
                $row["Effektmal_Namn"]       = $this->append([], $performance);
                $row["Initiativ_Utmaningar"] = $this->append([], $challenges);
                $row["Omrade_Namn"]          = $this->append([], $categories);
                $row["Transformation_Namn"]  = $this->append([], $technologies);
                $lookup[]                    = $row;
            } else {
                // Append to array
                $lookup[$key]["Effektmal_Namn"]       =
                    $this->append($lookup[$key]["Effektmal_Namn"], $performance);
                $lookup[$key]["Initiativ_Utmaningar"] =
                    $this->append($lookup[$key]["Initiativ_Utmaningar"], $challenges);
                $lookup[$key]["Omrade_Namn"]          =
                    $this->append($lookup[$key]["Omrade_Namn"], $categories);
                $lookup[$key]["Transformation_Namn"]  =
                    $this->append($lookup[$key]["Transformation_Namn"], $technologies);
            }
        });
        // Expand merged strings
        array_walk($lookup, function (&$row) {
            $row["Effektmal_Namn"]           = $this->arrayToList($row["Effektmal_Namn"]);
            $row["Initiativ_Utmaningar"]     = $this->arrayToList($row["Initiativ_Utmaningar"]);
            $row["Initiativ_Synligaenheter"] = $this->stringToList(
                $this->transformOrganisation($row["Initiativ_Synligaenheter"] ?? "")
            );
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
                ->alternateName($row["Initiativ_Kontaktperson"] ?? "")
                ->email($row["Initiativ_EmailKontaktperson"] ?? "");

            $project->employee($contact);

            $categories   = array_map(function ($category) {
                return Schema::propertyValue()->name('category')->value($category);
            }, $row["Omrade_Namn"]);
            $technologies = array_map(function ($technology) {
                return Schema::propertyValue()->name('technology')->value($technology);
            }, $row["Transformation_Namn"]);

            $project->setProperty('@meta', [
                ...$categories,
                ...$technologies,
                Schema::propertyValue()->name('status')->value($row["Initiativ_Status"] ?? "N/A")
            ]);
            $project->status($this->getStatus($row["Initiativ_Status"] ?? ""));
            $project->setProperty('@version', md5(json_encode($project->toArray())));
            $output[] = $project->toArray();
        }
        return $output;
    }

    private function getDescriptionValueFromRow($row): string
    {
        $descriptionArray = [
            'Initiativ_Vad'            => '<h2>Vad?</h2>',
            'Initiativ_Hur'            => '<h2>Hur?</h2>',
            'Initiativ_Varfor'         => '<h2>Varför?</h2>',
            'Effektmal_Namn'           => '<h2>Effektmål</h2>',
            'Initiativ_Avgransningar'  => '<h2>Avgränsningar</h2>',
            'Initiativ_Samarbetesokes' => '<h2>Samarbete sökes!</h2>',
            'Initiativ_Utmaningar'     => '<h2>Utmaningar</h2>',
            'Initiativ_Synligaenheter' => '<h2>Drivs av</h2>',
        ];

        return implode(array_map(
            fn($key, $htmlTitle) =>
            !empty($row[$key]) ? $htmlTitle . '<p>' . $this->sanitizeString($row[$key]) . '</p>' : '',
            array_keys($descriptionArray),
            array_values($descriptionArray)
        ));
    }
}

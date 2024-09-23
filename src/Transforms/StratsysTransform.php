<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use Spatie\SchemaOrg\Schema;

use function PHPUnit\Framework\isEmpty;

class StratsysTransform implements AbstractDataTransform
{
    private array $indexRef;

    protected function getValue(string $name, array $data): string
    {
        $index = array_search($name, $this->indexRef);

        if ($index === false) {
            return "";
        }
        if (empty($data[$index])) {
            return "-";
        }
        return $data[$index];
    }

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
    public function transform(array $data): array
    {
        $this->indexRef = $data["header"];
        $output         = [];

        foreach ($data["values"] as $row) {
            $project = Schema::project()->name($this->getValue("Initiativ_Namn", $row));
            $project->description($this->getDescriptionValueFromRow($row));
            $project->image($this->getValue("Initiativ_Bildtest", $row));
            $project->setProperty('@id', $this->getValue('Initiativ_InterntID', $row));

            $funding = Schema::monetaryGrant()->amount($this->getValue("Initiativ_Budgetuppskattning", $row));
            $project->funding($funding);

            $organization = Schema::organization()->name($this->getValue("Initiativ_Enhet", $row));
            $project->department($organization);

            $contact = Schema::person()
                ->alternateName($this->getValue("Initiativ_Kontaktperson", $row));
            $project->employee($contact);

            $project->setProperty('@meta', [
                Schema::propertyValue()->name('technology')->value($this->getValue("Omrade_Namn", $row)),
                Schema::propertyValue()->name('status')->value($this->getValue("Initiativ_Status", $row)),
                Schema::propertyValue()->name('progress')->value($this->getProgress($this->getValue("Initiativ_Status", $row))), // phpcs:ignore
                Schema::propertyValue()->name('category')->value($this->getValue("Transformation_Namn", $row)),
            ]);
            $project->setProperty('@version', md5(json_encode($project->toArray())));
            $output[] = $project->toArray();
        }
        return $output;
    }

    private function getDescriptionValueFromRow($row): string
    {
        $descriptionArray = [
            'Initiativ_Vad'           => '<h2>Vad</h2>',
            'Initiativ_Hur'           => '<h2>Hur</h2>',
            'Initiativ_Varfor'        => '<h2>Varför</h2>',
            'Effektmal_FargNamn'      => '<h2>Effektmål</h2>',
            'Initiativ_Avgransningar' => '<h2>Avgränsningar</h2>',
            'Initiativ_Utmaningar'    => '<h2>Utmaningar</h2>',
        ];

        return implode(array_map(
            fn($key, $htmlTitle) =>
            !empty($this->getValue($key, $row)) ? $htmlTitle . '<p>' . $this->getValue($key, $row) . '</p>' : '',
            array_keys($descriptionArray),
            array_values($descriptionArray)
        ));
    }
}

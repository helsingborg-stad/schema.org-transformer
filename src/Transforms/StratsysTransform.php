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
            case 'Ej påbörjad':
                return 0;
            case 'Idé':
                return 1;
            case 'Avslutad':
                return 2;
            case 'Avbruten':
                return 3;
            case 'Försenad':
                return 4;
            case 'Pågående':
                return 5;
            case 'Pilot':
                return 6;
            case 'Skala upp':
                return 7;
            case 'Realiserad':
                return 100;
        }
        return 0;
    }
    public function transform(array $data): array
    {
        $this->indexRef = $data["header"];
        $output = [];

        foreach ($data["values"] as $row) {
            $project = Schema::project()->name($this->getValue("Initiativ_Namn", $row));
            $project->description(implode([
                '<h2>Vad</h2>' . '<p>' . $this->getValue("Initiativ_Vad", $row) . '</p>',
                '<h2>Hur</h2>' . '<p>' . $this->getValue("Initiativ_Hur", $row) . '</p>',
                '<h2>Varför</h2>' . '<p>' . $this->getValue("Initiativ_Varfor", $row) . '</p>',
                '<h2>Effektmål</h2>' . '<p>' . $this->getValue("Effektmal_FargNamn", $row) . '</p>',
                '<h2>Avgränsningar</h2>' . '<p>' . $this->getValue("Initiativ_Avgransningar", $row) . '</p>',
                '<h2>Utmaningar</h2>' . '<p>' . $this->getValue("Initiativ_Utmaningar", $row) . '</p>',
            ]));
            $project->image($this->getValue("Initiativ_Bildtest", $row));

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
                Schema::propertyValue()->name('progress')->value($this->getProgress($this->getValue("Initiativ_Status", $row))),
                Schema::propertyValue()->name('category')->value($this->getValue("Transformation_Namn", $row)),
            ]);
            $project->setProperty('@version', md5(json_encode($project->toArray())));
            $output[] = $project->toArray();
        }
        return $output;
    }
}

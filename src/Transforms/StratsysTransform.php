<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use Spatie\SchemaOrg\Schema;

class StratsysTransform implements AbstractDataTransform
{
    private array $indexRef;

    protected function getValue(string $name, array $data): string
    {
        $index = array_search($name, $this->indexRef);

        if ($index === false) {
            return "";
        }
        return $data[$index];
    }
    public function transform(array $data): array
    {
        $this->indexRef = $data["header"];
        $output = [];

        foreach ($data["values"] as $row) {
            $article = Schema::article()->headline($this->getValue("Initiativ_Namn", $row));
            $article->abstract($this->getValue("Initiativ_Sammanfattning", $row));
            $article->articleBody([
                $this->getValue("Initiativ_Vad", $row),
                $this->getValue("Initiativ_Hur", $row),
                $this->getValue("Initiativ_Varfor", $row),
            ]);
            $article->articleSection($this->getValue("Omrade_Namn", $row));
            $article->genre($this->getValue("Transformation_Namn", $row));
            $article->creativeWorkStatus($this->getValue("Initiativ_Status", $row));
            $article->image($this->getValue("Initiativ_Bildtest", $row));

            $article->setProperty("@objectives", [$this->getValue("Effektmal_FargNamn", $row)]);
            $article->setProperty("@demarcations", [$this->getValue("Initiativ_Avgransningar", $row)]);
            $article->setProperty("@challenges", [$this->getValue("Initiativ_Utmaningar", $row)]);

            $funding = Schema::monetaryGrant()->amount($this->getValue("Initiativ_Budgetuppskattning", $row));
            $article->funding($funding);

            $organization = Schema::organization()->name($this->getValue("Initiativ_Enhet", $row));
            $article->sourceOrganization($organization);

            $contact = Schema::person()
                ->alternateName($this->getValue("Initiativ_Kontaktperson", $row));
            $article->publisher($contact);

            $article->setProperty('@version', md5(json_encode($article->toArray())));
            $output[] = $article->toArray();
        }
        return $output;
    }
}

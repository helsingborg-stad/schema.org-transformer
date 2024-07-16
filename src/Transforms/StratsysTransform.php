<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use Spatie\SchemaOrg\Schema;

class StratsysTransform implements AbstractDataTransform
{
    public function transform(array $data): array
    {
        $output = [];
        // Remove non-activities
        $filter = array_filter($data["Result"], function ($item) {
            return $item["ScorecardColumn"]["NodeType"] === "Activity";
        });

        foreach ($filter as $row) {
            $article = Schema::article()->headline($row["Name"]);

            $parent = "";
            foreach ($data["Result"] as $orgs) {
                if ($orgs["Id"] === $row["ParentId"]) {
                    $parent = $orgs["Name"];
                }
            }
            $article->articleSection($parent);
            $descriptionFields = array_filter($row["DescriptionFields"], function ($item) {
                return !is_null($item["TextValue"]);
            });

            foreach ($descriptionFields as $field) {
                $value = $field["TextValue"];
                switch ($field["DescriptionField"]["Id"]) {
                    case "218": // Summary
                        $article->articleBody($value);
                        break;
                    case "220": // Budget
                        $article->setProperty("@budget", $value);
                        break;
                    case "217": // Limitations
                        $article->setProperty("@limitations", $value);
                        break;
                    case "226": // Engagement 
                        $article->setProperty("@engagement", $value);
                        break;
                    case "227": // Image
                        $article->image($value);
                        break;
                    default:
                        break;
                }
            }
            $keywords = [];
            foreach ($row["Keywords"] as $keyword) {
                $keywords[$keyword["KeywordGroup"]["Name"]][] = $keyword["Name"];
            }
            $article->setProperty('@facets', $keywords);
            $article->setProperty('@version', md5(json_encode($article->toArray())));
            $output[] = $article->toArray();
        }
        print($output);
        return $output;
    }
}

<?php

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Interfaces\AbstractIdFormatter;

class SplitRowsByOccasion implements AbstractDataTransform
{
    public function __construct(private string $occasionPathInData, private AbstractIdFormatter $idFormatter)
    {
    }

    public function transform($data): array
    {
        $rowsWithSingleOccasion = [];

        foreach ($data as $rowWithMultipleOccasions) {
            if (empty($rowWithMultipleOccasions['id'])) {
                continue;
            }

            $occasions = $this->getOccasionsFromPath($rowWithMultipleOccasions, $this->occasionPathInData);

            if (empty($occasions)) {
                continue;
            }

            $rowsWithSingleOccasion = [...$rowsWithSingleOccasion, ...$this->getRowsFromOccasions($rowWithMultipleOccasions, $occasions)];
        }

        return $rowsWithSingleOccasion;
    }

    private function getRowsFromOccasions(array $row, array $occasions): array
    {
        $allIds = [];
        $rows   = [];

        foreach ($occasions as $i => $occasion) {
            $rowWithSingleOccasion               = $row;
            $rowWithSingleOccasion['originalId'] = $rowWithSingleOccasion['id'];
            $rowWithSingleOccasion['id']         = $this->idFormatter->formatId($rowWithSingleOccasion['id'] . '-' . $i);
            $allIds[]                            = $rowWithSingleOccasion['id'];

            $this->setOccasionData($rowWithSingleOccasion, [$occasion]);

            $rows[] = $rowWithSingleOccasion;
        }

        return $this->applyEventsInSameSeries($rows, $allIds);
    }

    private function getOccasionsFromPath(array $data, string $path)
    {
        $keys = explode('.', $path);
        foreach ($keys as $key) {
            if (!isset($data[$key])) {
                return null;
            }
            $data = $data[$key];
        }
        return $data;
    }

    private function setOccasionData(array &$row, array $occasion)
    {
        $keys    = explode('.', $this->occasionPathInData);
        $lastKey = array_pop($keys);
        $data    = &$row;
        foreach ($keys as $key) {
            if (!isset($data[$key])) {
                $data[$key] = [];
            }
            $data = &$data[$key];
        }
        $data[$lastKey] = $occasion;
    }

    private function applyEventsInSameSeries(array $rowsWithSingleOccasion): array
    {
        $allIds = array_column($rowsWithSingleOccasion, 'id');
        return array_map(function ($row) use ($allIds) {
            $row['eventsInSameSeries'] = array_filter($allIds, fn($id) => $id !== $row['id']);
            return $row;
        }, $rowsWithSingleOccasion);
    }
}

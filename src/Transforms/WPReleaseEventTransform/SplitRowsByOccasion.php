<?php

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataTransform;

class SplitRowsByOccasion implements AbstractDataTransform
{
    public function __construct(private string $occasionPathInData)
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

            if (count($occasions) === 1) {
                $rowsWithSingleOccasion[] = $rowWithMultipleOccasions;
                continue;
            }

            foreach ($occasions as $i => $occasion) {
                $rowWithSingleOccasion               = $rowWithMultipleOccasions;
                $rowWithSingleOccasion['originalId'] = $rowWithSingleOccasion['id'];
                $rowWithSingleOccasion['id']         = $rowWithSingleOccasion['id'] . '-' . $i;

                $this->setOccasionData($rowWithSingleOccasion, [$occasion]);

                $rowsWithSingleOccasion[] = $rowWithSingleOccasion;
            }
        }

        return $this->applyEventsInSameSeries($rowsWithSingleOccasion);
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

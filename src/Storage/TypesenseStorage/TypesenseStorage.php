<?php

namespace SchemaTransformer\Storage\TypesenseStorage;

use Psr\Log\LoggerInterface;
use SchemaTransformer\Loggers\NullLogger;
use SchemaTransformer\Storage\StorageInterface;
use Typesense\Collection;

class TypesenseStorage implements StorageInterface
{
    public function __construct(
        private TypesenseStorageConfigInterface $config,
        private LoggerInterface $logger = new NullLogger()
    ) {
    }

    public function store(mixed $data): void
    {
        $this->ensureCollectionExists();
        $this->clearStorage();

        if (empty($data)) {
            $this->logger->info("No data to store in Typesense collection: " . $this->config->getCollection()->value);
            return;
        }

        $result          = $this->getCollection()->documents->import($data);
        $errors          = $this->getImportErrors($result);
        $successfulCount = count($data) - count($errors);

        $this->logger->info("Stored " . $successfulCount . " records in Typesense collection: " . $this->config->getCollection()->value);
    }

    private function getCollection(): Collection
    {
        return $this->getClient()->collections[$this->config->getCollection()->value];
    }

    private function getImportErrors(array $result): array
    {
        if (is_array($result)) {
            return array_filter(array_map(function ($item) {
                if (isset($item['success']) && !$item['success']) {
                    $this->logger->warning("Failed to import document: " . $item['error']['message']);
                    return $item;
                }

                return null;
            }, $result));
        }

        return [];
    }

    private function clearStorage(): void
    {
        if ($this->config->getClearStorageQueryParams() !== null && $this->collectionHasDocuments()) {
            $this->logger->info("Clearing storage for collection: " . $this->config->getCollection()->value);
            $this->getCollection()->documents->delete($this->config->getClearStorageQueryParams());
        }
    }

    private function collectionHasDocuments(): bool
    {
        return $this->getCollection()->retrieve()['num_documents'] > 0;
    }

    private function ensureCollectionExists(): void
    {
        try {
            $this->getCollection()->retrieve();
        } catch (\Typesense\Exceptions\ObjectNotFound $e) {
            $this->logger->info("Creating Typesense collection: " . $this->config->getCollection()->value);
            $this->getClient()->collections->create([
                'name'                  => $this->config->getCollection()->value,
                'fields'                => [
                    [
                        'name'            => '.*',
                        'type'            => 'auto',
                        'facet'           => false,
                        'optional'        => true,
                        'index'           => true,
                        'sort'            => false,
                        'infix'           => false,
                        'stem'            => false,
                        'locale'          => '',
                        'stem_dictionary' => ''
                    ]
                ],
                'default_sorting_field' => '',
                'token_separators'      => [],
                'symbols_to_index'      => [],
                'enable_nested_fields'  => true
            ]);
        }
    }

    private function getClient(): \Typesense\Client
    {
        return $this->config->getClient();
    }
}

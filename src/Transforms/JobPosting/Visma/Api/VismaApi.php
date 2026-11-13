<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Visma\Api;

use Psr\Log\LoggerInterface;
use SchemaTransformer\Loggers\NullLogger;
use SimpleXMLElement;

/**
 * Fetches assignments from Visma Recruit.
 *
 * Visma exposes a list feed and one detail feed per assignment. Both are
 * collected here so the transform only has to map data it already has, the
 * same split as VarbiApi.
 */
class VismaApi
{
    private const ITEM_URL = 'https://recruit.visma.com/External/Feeds/AssignmentItem.ashx';

    /**
     * @param string $listUrl   AssignmentList.ashx URL. guidGroup is appended when the URL has none.
     * @param string $guidGroup Visma Recruit guidGroup used to load each assignment item.
     */
    public function __construct(
        private string $listUrl,
        private string $guidGroup,
        private LoggerInterface $logger = new NullLogger(),
    ) {
        if ($this->listUrl === '' || $this->guidGroup === '') {
            throw new \InvalidArgumentException('Visma list URL and guid group are required');
        }
    }

    /**
     * All assignments with their detail item.
     *
     * Assignments whose detail feed cannot be loaded are logged and skipped.
     *
     * @return array<int, array{assignment: string, item: string}>
     */
    public function getAllAssignmentsWithItems(): array
    {
        $assignments = $this->getAssignments();
        $result      = [];

        foreach ($assignments as $assignment) {
            $guid = trim((string) $assignment->Guid);
            if ($guid === '') {
                $this->logger->warning('Skipping Visma assignment without Guid');
                continue;
            }

            try {
                $item = $this->getItem($guid);
            } catch (\Throwable $e) {
                $this->logger->warning("Skipping Visma assignment {$guid}: " . $e->getMessage());
                continue;
            }

            $result[] = ['assignment' => (string) $assignment->asXML(), 'item' => (string) $item->asXML()];
        }

        if ($assignments !== [] && $result === []) {
            throw new \RuntimeException('Visma feed contained assignments but none could be loaded');
        }

        $this->logger->info('Total Visma assignments read: ' . count($result));

        return $result;
    }

    /**
     * @return array<int, SimpleXMLElement>
     */
    private function getAssignments(): array
    {
        $xml = $this->parse($this->fetchXml($this->getListUrl()));

        return $xml->xpath('//Assignment') ?: [];
    }

    private function getListUrl(): string
    {
        $query = (string) parse_url($this->listUrl, PHP_URL_QUERY);
        parse_str($query, $params);

        if (isset($params['guidGroup'])) {
            return $this->listUrl;
        }

        $separator = $query === '' ? (str_contains($this->listUrl, '?') ? '' : '?') : '&';

        return $this->listUrl . $separator . 'guidGroup=' . rawurlencode($this->guidGroup);
    }

    private function getItem(string $guid): SimpleXMLElement
    {
        $url   = self::ITEM_URL . '?guidGroup=' . rawurlencode($this->guidGroup) . '&guidAssignment=' . rawurlencode($guid);
        $items = $this->parse($this->fetchXml($url))->xpath('//Assignment') ?: [];

        if (!isset($items[0])) {
            throw new \RuntimeException('Assignment item feed is empty');
        }

        return $items[0];
    }

    protected function parse(string $xmlString): SimpleXMLElement
    {
        $clean = $this->sanitizeXml($xmlString);

        if ($clean === '') {
            throw new \InvalidArgumentException('No XML content provided');
        }

        if (str_contains($clean, 'Kunde inte hitta gruppen')) {
            throw new \InvalidArgumentException('Visma could not find the assignment group');
        }

        try {
            return new SimpleXMLElement($clean, LIBXML_NOCDATA | LIBXML_NOWARNING);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('XML parse error: ' . $e->getMessage(), 0, $e);
        }
    }

    protected function fetchXml(string $url): string
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Accept: application/xml, text/xml']);

        $response = curl_exec($curl);
        if ($response === false) {
            $error = curl_error($curl);
            curl_close($curl);
            throw new \RuntimeException('Could not retrieve Visma feed: ' . $error);
        }

        $code = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($code >= 400) {
            throw new \RuntimeException('Could not retrieve Visma feed. HTTP ' . $code);
        }

        return (string) $response;
    }

    /**
     * Escape stray ampersands and strip characters XML 1.0 rejects.
     */
    private function sanitizeXml(string $xml): string
    {
        $xml = preg_replace('/&(?!(?:amp|quot|apos|lt|gt);)/', '&amp;', $xml) ?? $xml;

        return trim(preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $xml) ?? $xml);
    }
}

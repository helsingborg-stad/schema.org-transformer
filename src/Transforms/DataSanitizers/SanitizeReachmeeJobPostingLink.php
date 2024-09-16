<?php

namespace SchemaTransformer\Transforms\DataSanitizers;

/**
 * Class SanitizeReachmeeJobPostingLink
 *
 * Sanitizes the link field in a Reachmee job posting to point to the application form.
 */
class SanitizeReachmeeJobPostingLink implements SanitizerInterface
{
    public function sanitize(array $data): array
    {
        if (empty($data['link'])) {
            return $data;
        }

        $data['link'] = $this->transformLinkToApplicationUrl($data['link']);

        return $data;
    }

    private function transformLinkToApplicationUrl(?string $link): string
    {
        $parsed = parse_url($link);
        if ($parsed === false || empty($parsed['query']) || empty($parsed['path'])) {
            return $link;
        }

        // Parse and modify query
        parse_str($parsed['query'], $queryArray);
        $queryArray['job_id'] = $queryArray['rmjob'] ?? null;
        unset($queryArray['rmpage'], $queryArray['rmjob']);

        // Build and return the final URL
        $path  = preg_replace('/\/main$/', '/apply', $parsed['path']);
        $query = http_build_query($queryArray);

        return $this->buildUrl($parsed['scheme'], $parsed['host'], $path, $query);
    }

    private function buildUrl(string $scheme, string $host, string $path, string $query): string
    {
        return "{$scheme}://{$host}{$path}?{$query}";
    }
}

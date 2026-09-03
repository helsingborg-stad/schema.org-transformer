# schema.org-transformer

[![PHP 8.4 Tests](https://img.shields.io/github/actions/workflow/status/helsingborg-stad/schema.org-transformer/test-php-8.4.yml?branch=main&label=PHP%208.4)](https://github.com/helsingborg-stad/schema.org-transformer/actions/workflows/test-php-8.4.yml)
[![PHP 8.5 Tests](https://img.shields.io/github/actions/workflow/status/helsingborg-stad/schema.org-transformer/test-php-8.5.yml?branch=main&label=PHP%208.5)](https://github.com/helsingborg-stad/schema.org-transformer/actions/workflows/test-php-8.5.yml)

Fetch and transform upstream JSON data into schema.org shaped documents, then persist the result to Typesense.

## Requirements

- PHP 8.2+
- Composer
- cURL extension enabled for PHP
- A reachable Typesense instance for the production-style `run/*` scripts

## Installation

```bash
git clone https://github.com/helsingborg-stad/schema.org-transformer.git
cd schema.org-transformer
composer install
```

## How The Project Is Structured

- `run/*.php`: executable ingestion entrypoints used in deployments and cron jobs
- `run/Cli/`: command-line target selection (`console` or `typesense`)
- `run/Factories/`: runtime factories for Typesense clients and storage
- `src/Transforms/`: mapping logic for each upstream domain
- `src/IO/V2/HttpReader.php`: paginated HTTP reader used by the run scripts
- `src/Storage/ConsoleStorage.php`: JSON output storage used for local runs and inspection
- `src/Storage/TypesenseStorage/`: persistence layer and collection config for Typesense
- `src/Paginators/`: pagination strategies for WordPress-style and non-paginated APIs
- `src/LockRunner/`: file-lock protection that prevents the same pipeline from running concurrently
- `tests/`, `run/**/*Test.php`, and `src/**/*Test.php`: PHPUnit coverage for transforms, mappers, utilities, CLI options, and storage helpers

## Runtime Model

Each `run/*.php` script follows the same pattern:

1. Acquire a lock for the pipeline.
2. Read JSON from an upstream HTTP endpoint.
3. Apply a dedicated transform.
4. Page through the source when needed.
5. Store the transformed documents as pretty-printed JSON on stdout by default.
6. When invoked with `--target=typesense`, clear previously generated documents in the target Typesense collection using a pipeline-specific filter and store the transformed documents there.

The school transforms also enrich their output by querying the `Event` collection in Typesense and attaching related events by school name.

The scripts accept `--target=console` and `--target=typesense`. The default is `console`, which does not require a reachable Typesense instance unless the selected transform needs Typesense for enrichment.

## Available Pipelines

| Script | Source env var | Transform | Typesense collection | Pagination |
| --- | --- | --- | --- | --- |
| `run/Event.php` | `WP_EVENTS_API_URL` | `WPHeadlessEventTransform` | `Event` | `WordpressPaginator` |
| `run/Event.legacy.php` | `WP_LEGACY_EVENTS_API_URL` | `WPLegacyEventTransform` | `Event` | `WordpressPaginator` |
| `run/Event.library.php` | `AXIELL_EVENTS_URL` | `AxiellEventTransform` | `Event` | `NullPaginator` |
| `run/Event.tix.php` | `TIX_EVENTS_API_URL` | `TixEventTransform` | `Event` | `WordpressPaginator` |
| `run/ExhibitionEvent.php` | `WORDPRESS_EXHIBITION_EVENT_PATH` | `WPExhibitionEventTransform` | `ExhibitionEvent` | `WordpressPaginator` |
| `run/JobPosting.public.php` | `REACHMEE_HELSINGBORG_PATH` | `ReachmeeJobPostingTransform` | `JobPosting.public` | `NullPaginator` |
| `run/JobPosting.private.php` | `REACHMEE_INTRANAT_HELSINGBORG_PATH` | `ReachmeeJobPostingTransform` | `JobPosting.private` | `NullPaginator` |
| `run/ElementarySchool.php` | `ELEMENTARY_SCHOOL_API_URL` | `ElementarySchoolTransform` | `ElementarySchool` | `WordpressPaginator` |
| `run/PreSchool.php` | `PRE_SCHOOL_API_URL` | `PreSchoolTransform` | `PreSchool` | `WordpressPaginator` |
| `run/Project.stratsys.innovation.php` | `STRATSYS_INNOVATION_PATH` | `StratsysTransform` | `Project` | `WordpressPaginator` |

## Environment Variables

### Shared Typesense configuration

The `run/*` scripts expect these variables unless noted otherwise:

- `TYPESENSE_HOST`
- `TYPESENSE_PORT`
- `TYPESENSE_PROTOCOL`
- `TYPESENSE_API_KEY`

### Pipeline-specific source configuration

- `WP_EVENTS_API_URL`
- `WP_LEGACY_EVENTS_API_URL`
- `AXIELL_EVENTS_URL`
- `TIX_EVENTS_API_URL`
- `WORDPRESS_EXHIBITION_EVENT_PATH`
- `REACHMEE_HELSINGBORG_PATH`
- `REACHMEE_INTRANAT_HELSINGBORG_PATH`
- `ELEMENTARY_SCHOOL_API_URL`
- `PRE_SCHOOL_API_URL`
- `STRATSYS_INNOVATION_PATH`

### Additional Stratsys OAuth configuration

The Stratsys pipeline also requires:

- `STRATSYS_INNOVATION_AUTH`
- `STRATSYS_INNOVATION_CLIENTID`
- `STRATSYS_INNOVATION_CLIENTSECRET`

### Optional monitor webhooks

After a successful run, a script triggers its monitor webhook when the corresponding variable is set:

- `WP_EVENTS_MONITOR_URL`
- `WP_LEGACY_EVENTS_MONITOR_URL`
- `AXIELL_EVENTS_MONITOR_URL`
- `TIX_EVENTS_MONITOR_URL`
- `WORDPRESS_EXHIBITION_EVENTS_MONITOR_URL`
- `REACHMEE_HELSINGBORG_MONITOR_URL`
- `REACHMEE_INTRANAT_HELSINGBORG_MONITOR_URL`
- `ELEMENTARY_SCHOOL_MONITOR_URL`
- `PRE_SCHOOL_MONITOR_URL`
- `STRATSYS_INNOVATION_MONITOR_URL`

## Running A Pipeline

Example:

```bash
export TYPESENSE_HOST=localhost
export TYPESENSE_PORT=8108
export TYPESENSE_PROTOCOL=http
export TYPESENSE_API_KEY=xyz
export WP_EVENTS_API_URL='https://example.test/wp-json/wp/v2/event?per_page=100'

php run/Event.php
```

To write the transformed documents to Typesense, pass the target explicitly:

```bash
php run/Event.php --target=typesense
```

Another Typesense example for Stratsys:

```bash
export STRATSYS_INNOVATION_PATH='https://example.test/export'
export STRATSYS_INNOVATION_AUTH='https://example.test/oauth/token'
export STRATSYS_INNOVATION_CLIENTID='client-id'
export STRATSYS_INNOVATION_CLIENTSECRET='client-secret'

php run/Project.stratsys.innovation.php --target=typesense
```

## Development Commands

Composer scripts defined in `composer.json`:

```bash
composer test
composer test-coverage
composer lint
composer lint-fix
composer phpstan
```

## Extending The Project

To add a new integration, the current codebase pattern is:

1. Add a transform under `src/Transforms/`.
2. Reuse or add a paginator under `src/Paginators/` if the source API needs one.
3. Add a dedicated executable in `run/` that wires the source URL, transform, logger, lock, and Typesense collection.
4. Add PHPUnit coverage for the transform and any mapper or sanitizer classes.

## License

This project is licensed under the [MIT License](LICENSE).

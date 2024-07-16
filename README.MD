# schema.org-transformer

Fetching and transforming data from API to JSON-LD using spatie/schema-org

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/helsingborg-stad/schema.org-transformer.git
   ```

2. Install dependencies:

   ```bash
   composer install
   ```

## Usage

### Running the Router Script

To run the router script, use the following command:

```bash
Usage: php router.php

Input settings
 --source <file|url>            Input file or URL
 --sourceheaders <headers>      Comma separated HTTP headers when source is a URL

Output settings
 --output <file|url>            Output file or URL
 --outputheaders <headers>      Comma separated HTTP headers when output is a URL
 --outputformat <json|jsonl>    Output format

Transformation settings
 --transform <jobposting>       Name of transform to apply

 OAuth authentication parameters (Applicable for source only)
 --authpath <url>               URL of token service
 --authclientid <string>        Client id
 --authclientsecret <string>    Client secret
```

Example:

```bash
php router.php --source=https://reachmee --output=/tmp/output.json --transform=jobposting
```

### Customizing Implementations

You can add additional transforms to the Transforms folder.

## Contributing

1. Fork the repository.
2. Create a new branch (`git checkout -b feature-name`).
3. Commit your changes (`git commit -am 'Add new feature'`).
4. Push to the branch (`git push origin feature-name`).
5. Create a new pull request.

## License

This project is licensed under the [MIT License](LICENSE).

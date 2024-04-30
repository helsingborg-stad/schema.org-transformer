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
Usage: php router.php --source=<source_path> [--destination=<destination_path> --transform=<jobposting> --outputformat=<json|jsonl>] --config=<config>
```

Replace `<source_path>` with the source path of the data to transform, `<destination_path>` with the path of the output file you want to use and `<transform_name>` with the transform to apply. In cases of http operations, headers can be added as a comma separated list to the <config> parameter.

Example:

```bash
php router.php --source=https://reachmee --destination=/tmp/output.json --transform=jobposting
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

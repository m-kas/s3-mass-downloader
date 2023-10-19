# Overview

This library allows you to download the provided list of files from S3 storage into a single zipped file.

**What is important, the downloaded files are not stored on the disk on the server, but are streamed directly to the zip file to be downloaded by the user's browser.**

# Prerequisites

- [PHP 8.2](https://www.php.net/downloads.php) or higher
- [PHP Zip](https://www.php.net/manual/en/zip.installation.php) extension
- [PHP AWS SDK 3.0](https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/getting-started_installation.html) or higher (installed with [composer](https://getcomposer.org/))
- [AWS S3 bucket](https://docs.aws.amazon.com/AmazonS3/latest/userguide/creating-bucket.html) with files to download

# Installation

- clone the source code from the repository
- install dependencies with `composer install`
- use the library in your code (see the `example-of-use.php` file)

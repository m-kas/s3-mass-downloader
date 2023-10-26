# Overview

This library allows you to download the provided list of files from S3 storage into a single zipped file.

**What is important, the downloaded files are not stored on the disk on the server, but are streamed directly to the zip file to be downloaded by the user's browser.**

# Prerequisites

- [PHP 7.4](https://www.php.net/downloads.php) or higher,
- [PHP Zip](https://www.php.net/manual/en/zip.installation.php) extension,
- [PHP AWS SDK 3.0](https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/getting-started_installation.html) or higher (installed as a [composer](https://getcomposer.org/) dependency),
- [AWS S3 bucket](https://docs.aws.amazon.com/AmazonS3/latest/userguide/creating-bucket.html) with files to download.

# Installation

- install library with [composer](https://getcomposer.org/): `composer require s3-mass-downloader/s3-mass-downloader`
- use the library in your code (see the `examples/basic-usage.php` file)

# Usage

- create an instance of the `StreamingClient` class. As a parameters please provide an instance of the `S3Client` class from the AWS SDK and the name of the bucket from which you want to download the files.
- call `downloadZippedFiles($listOfFilesToDownload)` method. As a parameter please provide an array with the list of absolute files path that you want to be downloaded.
- please see the `examples/basic-usage.php` file for more details.

## PHPFileTool

### Library containing the main functions of the PHP file system.

Manipulating files and directories, how to create directories, create files, sanitize directory and file names, among other features related to the file system.

![Static Badge](https://img.shields.io/badge/XxZeroxX-FFEF00?style=for-the-badge&label=Author&labelColor=485460)
![Packagist Version](https://img.shields.io/packagist/v/phpfiletool/filetool?server=https%3A%2F%2Fpackagist.org&style=for-the-badge&logo=packagist&logoColor=white&labelColor=485460&color=484C89)
![GitHub Release](https://img.shields.io/github/v/release/AntonioCarioca/PHP-FileTool?style=for-the-badge&label=RELEASE&labelColor=485460&color=484C89)
![GitHub repo size](https://img.shields.io/github/repo-size/AntonioCarioca/PHP-FileTool?style=for-the-badge&labelColor=485460&color=484C89)
![GitHub License](https://img.shields.io/github/license/AntonioCarioca/PHP-FileTool?style=for-the-badge&labelColor=485460&color=484C89)

---

## Installation

PHPFileTool is available via Composer:

```composer
composer require phpfiletool/filetool
```

## Overview

```php
require vendor/autoload.php;

use PHPFileTool\FileTool\FileTool;

// Create a new file
FileTool::createFile(dir:'test', file:'text.txt');
```

## Documentation

Full documentation can be found at
[xxzeroxx.serv00.net](https://xxzeroxx.serv00.net/)

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The GPL-3.0 license (GNU). Please see [License File](LICENSE) for more information.
# TV Detector

Lightweight TV user-agent detector extracted from matomo/device-detector brand-level regexes. Uses a single `preg_match()` instead of the full DeviceDetector pipeline.

## Installation

```bash
composer require colonizator1/tv-detector
```

## Usage

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use TvDetector\TvDetector;

$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

if (TvDetector::isAndroidTV($userAgent)) {
    // Handle Android TV specifics
}

if (TvDetector::isTV($userAgent)) {
    // Handle generic TV
}
```

## API

- `TvDetector::isTV(string $userAgent): bool` — returns `true` if the user agent matches any known TV signatures.
- `TvDetector::isAndroidTV(string $userAgent): bool` — returns `true` if the user agent looks like Android TV (including OEM skins).

## Notes

- Patterns are derived from the Matomo DeviceDetector regex sets (televisions and shell TV), limited to brand-level detection.
- Empty user agents return `false`.

## License

MIT

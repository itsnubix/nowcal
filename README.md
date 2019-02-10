# NowCal

A modern PHP library for generating iCalendar v2.0 events.

## Installation

`$ composer require itsnubix/nowcal`

## Usage

```php
use NowCal\NowCal;

$props = ['start' => 'October 5, 2019 6:03PM'];

$event = NowCal::build($props))
  ->summary('Daft Punk is playing')
  ->location('My House');

// Get the tempfile path
$event->file;
```

## Todo

- Support additional properties as outlined on [RFC 5545](https://tools.ietf.org/html/rfc5545)
- ~~Build out ability to create and export tempfile for NowCal~~
- Backfill with tests

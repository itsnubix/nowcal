# NowCal

A modern PHP library for generating iCalendar v2.0 events.

## Usage

```php
use NowCal\NowCal;

$props = [
  'start' => 'October 5, 2019 6:03PM',
  'summary' => 'Daft Punk is playing'
  'location' => 'My House',
];

// Export ICS as array
$raw = NowCal::raw($props);

// Export ICS as plain text
$plain = NowCal::plain($props);



// API available to also use non-statically

$props = ['start' => 'October 5, 2019 6:03PM'];

$event = (new NowCal($props))
  ->summary('Daft Punk is playing')
  ->location('My House');

// Export ICS as array
$event->raw;

// Export ICS as plain text
$event->plain;
```

## Todo

- Support additional properties as outlined on [RFC 5545](https://tools.ietf.org/html/rfc5545)
- Build out ability to create and export tempfile for NowCal
- Backfill with tests

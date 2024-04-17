# NowCal

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://travis-ci.org/itsnubix/nowcal.svg?branch=master)](https://travis-ci.org/itsnubix/nowcal)
[![StyleCI](https://github.styleci.io/repos/169808234/shield?branch=master)](https://github.styleci.io/repos/169808234)
[![Total Downloads](https://img.shields.io/packagist/dt/itsnubix/nowcal.svg?style=flat-square)](https://packagist.org/packages/itsnubix/nowcal)

A modern PHP library for generating iCalendar v2.0 events.

## Getting Started

### Installation

Install with composer using `composer require itsnubix/nowcal`

### Basic usage

```php
use NowCal\NowCal;

$event = NowCal::create(['start' => 'October 5, 2019 6:03PM']))
  ->summary('Daft Punk is playing')
  ->location('My House');
```

## API

### Properties

The following properties can be get/set on the NowCal instance. Users can take advantage of the set property helpers in the class, i.e.: `$nowcal->location('Event Location');` as they provide a nice syntax to string multiple calls together and support callbacks if necessary.

| Property | Description                                                                                            |
| -------- | ------------------------------------------------------------------------------------------------------ |
| start    | A string parseable by DateTime                                                                         |
| end      | A string parseable by DateTime, as per RFC 5545, only an end value or duration value may be used       |
| duration | A string parseable by DateInterval, as per RFC 5545, only an end value or duration value may be used   |
| summary  | A short description of the event                                                                       |
| location | The location where the event is taking place                                                           |

### Methods

```php
$props = [
  'start' => 'now',
  'end' => 'now + 1 hour',
  // OR
  'duration' => '28d 6h 42m 12s',
  'summary' => '',
  'location' => '',
];

// Creates a NowCal instance
$nowcal = new NowCal($props); // or NowCal::create($props);

// Exports a raw output array
$nowcal->raw; // or NowCal::raw($props)

// Exports a plain text version
$nowcal->plain; // or NowCal::plain($props)

// Exports a path to a tempfile
$nowcal->file; // or NowCal::file($props)
```

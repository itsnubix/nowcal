# NowCal

[![Latest version on Packagist](https://img.shields.io/packagist/v/itsnubix/nowcal.svg?style=flat-square)](https://packagist.org/packages/itsnubix/nowcal)
[![Build Status](https://travis-ci.org/itsnubix/nowcal.svg?branch=master)](https://travis-ci.org/itsnubix/nowcal)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
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

The following properties can be get/set on the NowCal instance. Users should take advantage of the setting property helpers in the class, i.e.: `$nowcal->location('Event Location');` as they provide a nice syntax to string multiple calls together.

| Property | Type   | Description                                           |
| -------- | ------ | ----------------------------------------------------- |
| start    | string | A DateTime compatible string that is parsed by Carbon |
| end      | string | A DateTime compatible string that is parsed by Carbon |
| summary  | string | A short description of the event                      |
| location | string | The location where the event is taking place          |

### Methods

```php
$props = [
  'start' => 'now',
  'end' => 'now + 1 hour',
  'summary' => '',
  'location' => ''
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

## Todo

- Support additional properties as outlined on [RFC 5545](https://tools.ietf.org/html/rfc5545)
- ~~Build out ability to create and export tempfile for NowCal~~
- ~~Backfill with tests~~

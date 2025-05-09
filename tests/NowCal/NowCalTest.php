<?php

namespace Tests\NowCal;

use DateInterval;
use DateTime;
use DateTimeZone;
use NowCal\NowCal;
use Tests\TestCase;

class NowCalTest extends TestCase
{
    public function test_it_can_get_a_raw_array_output()
    {
        $raw = $this->nowcal->raw;

        $this->assertIsArray($raw);
    }

    public function test_it_can_get_a_plaintext_output()
    {
        $plain = $this->nowcal->plain;

        $this->assertIsString($plain);
    }

    public function test_it_can_export_a_path_to_a_file()
    {
        $file = $this->nowcal->file;

        $this->assertIsString($file);
    }

    public function test_it_can_get_set_a_duration()
    {
        $this->nowcal->duration($time = '1h');
        $this->assertEquals($time, $this->nowcal->duration);

        $this->nowcal->duration(new DateInterval('PT1H'));
        $this->assertEquals('PT1H', $this->nowcal->duration);
    }

    public function test_it_casts_end_as_an_iso_duration()
    {
        $this->nowcal->duration($duration = '1h');

        $this->assertStringContainsString('PT1H', $this->nowcal->plain);
    }

    public function test_it_cannot_have_both_an_end_and_duration()
    {
        $duration = '1h';
        $end = 'now';

        $this->nowcal->duration($duration)->end($end);

        $this->assertEquals(null, $this->nowcal->end);
        $this->assertEquals($duration, $this->nowcal->duration);

        $this->nowcal = $this->createNowCalInstance();
        $this->nowcal->end($end)->duration($duration);

        $this->assertEquals(null, $this->nowcal->duration);
        $this->assertEquals($end, $this->nowcal->end);
    }

    public function test_it_casts_end_as_a_datetime()
    {
        $this->nowcal->end($time = 'October 5, 2019 6:03PM');

        $this->assertStringContainsString((new DateTime($time))->format(NowCal::DATETIME_FORMAT), $this->nowcal->plain);
    }

    public function test_it_can_get_and_set_a_location()
    {
        $this->nowcal->location($location = '123 Fake Street NW');

        $this->assertEquals($location, $this->nowcal->location);
    }

    public function test_it_includes_location_in_its_output()
    {
        $this->nowcal->location($location = '123 Fake Street NW');

        $this->assertStringContainsString($location, $this->nowcal->plain);
    }

    public function test_it_can_get_and_set_a_start_time()
    {
        $this->nowcal->start($time = 'now');
        $this->assertEquals($time, $this->nowcal->start);

        $this->nowcal->start(fn() => $time = 'now');
        $this->assertEquals($time, $this->nowcal->start);

        $this->nowcal->start($time = new DateTime('now'));
        $this->assertEquals($time->format(NowCal::DATETIME_FORMAT), $this->nowcal->start);
    }

    public function test_it_can_get_and_set_an_end_time()
    {
        $this->nowcal->end($time = 'now');
        $this->assertEquals($time, $this->nowcal->end);

        $this->nowcal->end(fn() => $time = 'now');
        $this->assertEquals($time, $this->nowcal->end);

        $this->nowcal->end($time = new DateTime('now'));
        $this->assertEquals($time->format(NowCal::DATETIME_FORMAT), $this->nowcal->end);
    }

    public function test_it_casts_start_as_a_datetime()
    {
        $this->nowcal->start($time = 'October 5, 2019 6:03PM');

        $this->assertStringContainsString((new DateTime($time))->format(NowCal::DATETIME_FORMAT), $this->nowcal->plain);
    }

    public function test_it_can_get_and_set_a_summary()
    {
        $this->nowcal->summary($summary = 'lorem ipsum dolor sit');

        $this->assertEquals($summary, $this->nowcal->summary);
    }

    public function test_it_includes_the_summary_in_its_output()
    {
        $this->nowcal->summary($summary = 'This is my summary');

        $this->assertStringContainsString($summary, $this->nowcal->plain);
    }

    public function test_it_can_set_a_timezone()
    {
        $this->nowcal->timezone($timezone = 'America/Edmonton')->start('now');
        $this->assertStringContainsString($timezone, $this->nowcal->plain);

        $this->nowcal->timezone($timezone = new DateTimeZone('America/Edmonton'))->start('now');
        $this->assertStringContainsString($timezone->getName(), $this->nowcal->plain);

        $this->assertStringContainsString('BEGIN:VTIMEZONE', $this->nowcal->plain);
        $this->assertStringContainsString('BEGIN:DAYLIGHT', $this->nowcal->plain);
        $this->assertStringContainsString('BEGIN:STANDARD', $this->nowcal->plain);
    }

    public function test_places_that_do_not_witness_dst_dont_get_daylight_hours()
    {
        $this->nowcal->timezone($timezone = 'Africa/Algiers')->start('now');

        $this->assertStringContainsString($timezone, $this->nowcal->plain);
        $this->assertStringContainsString('BEGIN:VTIMEZONE', $this->nowcal->plain);
        $this->assertStringNotContainsString('BEGIN:DAYLIGHT', $this->nowcal->plain);
    }

    public function test_it_can_customize_the_uid()
    {
        $this->nowcal->uid($uid = 'abcd-1234');
        $this->assertStringContainsString('UID:' . $uid, $this->nowcal->plain);
    }

    public function test_it_can_set_a_sequence()
    {
        $this->nowcal->sequence($sequence = 1);

        $this->assertEquals($sequence, $this->nowcal->sequence);
    }

    public function test_it_can_set_a_method()
    {
        $this->nowcal->method($method = 'request');

        $this->assertEquals($method, $this->nowcal->method);
    }

    public function test_it_can_set_a_reminder_with_simple_string()
    {
        $this->nowcal->reminder('15m');

        $this->assertStringContainsString('BEGIN:VALARM', $this->nowcal->plain);
        $this->assertStringContainsString('TRIGGER:-PT15M', $this->nowcal->plain);
        $this->assertStringContainsString('ACTION:DISPLAY', $this->nowcal->plain);
        $this->assertStringContainsString('DESCRIPTION:Reminder', $this->nowcal->plain);
        $this->assertStringContainsString('END:VALARM', $this->nowcal->plain);

        $actual = NowCal::plain([
            'reminder' => '30m',
        ]);

        $this->assertStringContainsString('BEGIN:VALARM', $actual);
        $this->assertStringContainsString('TRIGGER:-PT30M', $actual);
        $this->assertStringContainsString('ACTION:DISPLAY', $actual);
        $this->assertStringContainsString('DESCRIPTION:Reminder', $actual);
        $this->assertStringContainsString('END:VALARM', $actual);
    }

    public function test_it_can_set_a_reminder_with_iso8601_string()
    {
        $this->nowcal->reminder( 'PT30M');

        $this->assertStringContainsString('BEGIN:VALARM', $this->nowcal->plain);
        $this->assertStringContainsString('TRIGGER:-PT30M', $this->nowcal->plain);
        $this->assertStringContainsString('ACTION:DISPLAY', $this->nowcal->plain);
        $this->assertStringContainsString('DESCRIPTION:Reminder', $this->nowcal->plain);
        $this->assertStringContainsString('END:VALARM', $this->nowcal->plain);
    }
    public function test_it_can_set_a_reminder_with_DateInterval()
    {
        $this->nowcal->reminder(new DateInterval('PT1H'));

        $this->assertStringContainsString('BEGIN:VALARM', $this->nowcal->plain);
        $this->assertStringContainsString('TRIGGER:-PT1H', $this->nowcal->plain);
        $this->assertStringContainsString('ACTION:DISPLAY', $this->nowcal->plain);
        $this->assertStringContainsString('DESCRIPTION:Reminder', $this->nowcal->plain);
        $this->assertStringContainsString('END:VALARM', $this->nowcal->plain);
    }

    public function test_it_includes_description_before_reminder()
    {
        $description = 'This is a test description';
        $this->nowcal->description($description);
        $this->nowcal->reminder( 'PT30M');

        $expectedDescriptionProperty = 'DESCRIPTION:'.$description;
        $this->assertStringContainsString($expectedDescriptionProperty, $this->nowcal->plain);
        $this->assertStringContainsString('BEGIN:VALARM', $this->nowcal->plain);

        $descriptionIndex = strpos($this->nowcal->plain, $expectedDescriptionProperty);
        $reminderStartIndex = strpos($this->nowcal->plain, 'BEGIN:VALARM');
        $this->assertTrue($descriptionIndex < $reminderStartIndex);
    }
}

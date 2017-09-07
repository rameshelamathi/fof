<?php
/**
 * @package     FOF
 * @copyright   2010-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Tests\Utils;

class TimezoneWranglerProvider
{
	/**
	 * The following timezones are used for our tests:
	 *
	 * US Eastern ........... America/New_York      DST began Mar 12, 2017 (UTC-4) ** DST ends Nov 5, 2017 (UTC-5)
	 * US Mountain .......... America/Denver        DST began Mar 12, 2017 (UTC-6) ** DST ends Nov 5, 2017 (UTC-7)
	 * US Mountain no DST ... America/Phoenix       UTC-7 all year round
	 * EEST (follows DST) ... Asia/Nicosia          DST began Mar 26, 2017 (UTC+3) ** DST ends Oct 29, 2017 (UTC+2)
	 * Algeria (no DST) ..... Africa/Algiers        UTC+1 all year round
	 * Australia Eastern .... Australia/Sydney      DST ended Apr 02, 2017 (UTC+10) ** DST starts Oct 1, 2017 (UTC+11)
	 * Queensland ........... Australia/Brisbane    UTC+10 all year round
	 * Tokyo, Japan ......... Asia/Tokyo            UTC+9 all year round (East Asia doesn't follow DST)
	 * India ................ Asia/Kolkata          UTC+05:30 all year round
	 */

	public static function getTestGetLocalDateTime()
	{
		return [
			// $sourceTime, $timezone, $expected, $message

			// US Eastern ........... America/New_York      DST began Mar 12, 2017 (UTC-4) ** DST ends Nov 5, 2017 (UTC-5)
			['2017-08-15 00:11:22Z', 'America/New_York', '2017-08-14 20:11:22', "New York, date within DST range"],
			['2017-02-15 00:11:22Z', 'America/New_York', '2017-02-14 19:11:22', "New York, date outside DST range"],
			['2017-03-12 06:59:59Z', 'America/New_York', '2017-03-12 01:59:59', "New York, date before DST start"],
			['2017-03-12 07:00:00Z', 'America/New_York', '2017-03-12 03:00:00', "New York, date right on DST start"],
			['2017-11-05 05:59:59Z', 'America/New_York', '2017-11-05 01:59:59', "New York, date before DST end"],
			['2017-11-05 05:00:00Z', 'America/New_York', '2017-11-05 01:00:00', "New York, an hour before DST end"],
			['2017-11-05 06:00:00Z', 'America/New_York', '2017-11-05 01:00:00', "New York, date right on DST end"],

			// US Mountain .......... America/Denver        DST began Mar 12, 2017 (UTC-6) ** DST ends Nov 5, 2017 (UTC-7)
			['2017-08-15 00:11:22Z', 'America/Denver', '2017-08-14 18:11:22', "Denver, date within DST range"],
			['2017-02-15 00:11:22Z', 'America/Denver', '2017-02-14 17:11:22', "Denver, date outside DST range"],
			['2017-03-12 08:59:59Z', 'America/Denver', '2017-03-12 01:59:59', "Denver, date before DST start"],
			['2017-03-12 09:00:00Z', 'America/Denver', '2017-03-12 03:00:00', "Denver, date right on DST start"],
			['2017-11-05 07:59:59Z', 'America/Denver', '2017-11-05 01:59:59', "Denver, date before DST end"],
			['2017-11-05 07:00:00Z', 'America/Denver', '2017-11-05 01:00:00', "Denver, an hour before DST end"],
			['2017-11-05 08:00:00Z', 'America/Denver', '2017-11-05 01:00:00', "Denver, date right on DST end"],

			// US Mountain no DST ... America/Phoenix       UTC-7 all year round
			['2017-08-15 00:11:22Z', 'America/Phoenix', '2017-08-14 17:11:22', "Phoenix, date within DST range"],
			['2017-02-15 00:11:22Z', 'America/Phoenix', '2017-02-14 17:11:22', "Phoenix, date outside DST range"],
			['2017-03-12 08:59:59Z', 'America/Phoenix', '2017-03-12 01:59:59', "Phoenix, date before DST start"],
			['2017-03-12 09:00:00Z', 'America/Phoenix', '2017-03-12 02:00:00', "Phoenix, date right on DST start"],
			['2017-11-05 07:59:59Z', 'America/Phoenix', '2017-11-05 00:59:59', "Phoenix, date before DST end"],
			['2017-11-05 07:00:00Z', 'America/Phoenix', '2017-11-05 00:00:00', "Phoenix, an hour before DST end"],
			['2017-11-05 08:00:00Z', 'America/Phoenix', '2017-11-05 01:00:00', "Phoenix, date right on DST end"],

			// EEST (follows DST) ... Asia/Nicosia          DST began Mar 26, 2017 (UTC+3) ** DST ends Oct 29, 2017 (UTC+2)
			['2017-08-15 00:11:22Z', 'Asia/Nicosia', '2017-08-15 03:11:22', "EEST, date within DST range"],
			['2017-02-15 00:11:22Z', 'Asia/Nicosia', '2017-02-15 02:11:22', "EEST, date outside DST range"],
			['2017-03-26 00:59:59Z', 'Asia/Nicosia', '2017-03-26 02:59:59', "EEST, date right before DST start"],
			['2017-03-26 01:00:00Z', 'Asia/Nicosia', '2017-03-26 04:00:00', "EEST, date right on DST start"],
			['2017-10-29 00:59:59Z', 'Asia/Nicosia', '2017-10-29 03:59:59', "EEST, date right before DST end"],
			['2017-10-29 00:00:00Z', 'Asia/Nicosia', '2017-10-29 03:00:00', "EEST, date one hour before DST end"],
			['2017-10-29 01:00:00Z', 'Asia/Nicosia', '2017-10-29 03:00:00', "EEST, date right on DST end"],

			// Algeria (no DST) ..... Africa/Algiers        UTC+1 all year round
			['2017-08-15 00:11:22Z', 'Africa/Algiers', '2017-08-15 01:11:22', "Algeria, date within EEST DST range"],
			['2017-02-15 00:11:22Z', 'Africa/Algiers', '2017-02-15 01:11:22', "Algeria, date outside EEST DST range"],
			['2017-03-26 00:59:59Z', 'Africa/Algiers', '2017-03-26 01:59:59', "Algeria, date right before EEST DST start"],
			['2017-03-26 01:00:00Z', 'Africa/Algiers', '2017-03-26 02:00:00', "Algeria, date right on EEST DST start"],
			['2017-10-29 00:59:59Z', 'Africa/Algiers', '2017-10-29 01:59:59', "Algeria, date right before EEST DST end"],
			['2017-10-29 00:00:00Z', 'Africa/Algiers', '2017-10-29 01:00:00', "Algeria, date one hour before EEST DST end"],
			['2017-10-29 01:00:00Z', 'Africa/Algiers', '2017-10-29 02:00:00', "Algeria, date right on EEST DST end"],

			// Australia Eastern .... Australia/Sydney      DST ended Apr 02, 2017 (UTC+10) ** DST starts Oct 1, 2017 (UTC+11)
			['2017-02-15 00:11:22Z', 'Australia/Sydney', '2017-02-15 11:11:22', "Australia Eastern, date within DST range"],
			['2017-08-15 00:11:22Z', 'Australia/Sydney', '2017-08-15 10:11:22', "Australia Eastern, date outside DST range"],
			['2017-09-30 15:59:59Z', 'Australia/Sydney', '2017-10-01 01:59:59', "Australia Eastern, date right before DST start"],
			['2017-09-30 16:00:00Z', 'Australia/Sydney', '2017-10-01 03:00:00', "Australia Eastern, date right on DST start"],
			['2017-04-01 15:59:59Z', 'Australia/Sydney', '2017-04-02 02:59:59', "Australia Eastern, date right before DST end"],
			['2017-04-01 15:00:00Z', 'Australia/Sydney', '2017-04-02 02:00:00', "Australia Eastern, date one hour before DST end"],
			['2017-04-01 16:00:00Z', 'Australia/Sydney', '2017-04-02 02:00:00', "Australia Eastern, date right on DST end"],

			// Queensland ........... Australia/Brisbane    UTC+10 all year round
			['2017-02-15 00:11:22Z', 'Australia/Brisbane', '2017-02-15 10:11:22', "Australia Queensland, date within DST range"],
			['2017-08-15 00:11:22Z', 'Australia/Brisbane', '2017-08-15 10:11:22', "Australia Queensland, date outside DST range"],
			['2017-09-30 15:59:59Z', 'Australia/Brisbane', '2017-10-01 01:59:59', "Australia Queensland, date right before DST start"],
			['2017-09-30 16:00:00Z', 'Australia/Brisbane', '2017-10-01 02:00:00', "Australia Queensland, date right on DST start"],
			['2017-04-01 15:59:59Z', 'Australia/Brisbane', '2017-04-02 01:59:59', "Australia Queensland, date right before DST end"],
			['2017-04-01 15:00:00Z', 'Australia/Brisbane', '2017-04-02 01:00:00', "Australia Queensland, date one hour before DST end"],
			['2017-04-01 16:00:00Z', 'Australia/Brisbane', '2017-04-02 02:00:00', "Australia Queensland, date right on DST end"],

			// Tokyo, Japan ......... Asia/Tokyo            UTC+9 all year round (East Asia doesn't follow DST)
			['2017-02-15 00:11:22Z', 'Asia/Tokyo', '2017-02-15 09:11:22', "Tokyo, date within DST range"],
			['2017-08-15 00:11:22Z', 'Asia/Tokyo', '2017-08-15 09:11:22', "Tokyo, date outside DST range"],
			['2017-09-30 15:59:59Z', 'Asia/Tokyo', '2017-10-01 00:59:59', "Tokyo, date right before DST start"],
			['2017-09-30 16:00:00Z', 'Asia/Tokyo', '2017-10-01 01:00:00', "Tokyo, date right on DST start"],
			['2017-04-01 15:59:59Z', 'Asia/Tokyo', '2017-04-02 00:59:59', "Tokyo, date right before DST end"],
			['2017-04-01 15:00:00Z', 'Asia/Tokyo', '2017-04-02 00:00:00', "Tokyo, date one hour before DST end"],
			['2017-04-01 16:00:00Z', 'Asia/Tokyo', '2017-04-02 01:00:00', "Tokyo, date right on DST end"],

			// India ................ Asia/Kolkata          UTC+05:30 all year round
			['2017-02-15 00:11:22Z', 'Asia/Kolkata', '2017-02-15 05:41:22', "India #1"],
			['2017-02-15 18:30:00Z', 'Asia/Kolkata', '2017-02-16 00:00:00', "India #2"],
			['2017-02-15 23:30:00Z', 'Asia/Kolkata', '2017-02-16 05:00:00', "India #3"],
		];
	}

	public static function getTestGetGMTDateTime()
	{
		return [
			// $localTime, $timezone, $gmtTime, $message, $negativeTest
			/**
			 * Negative tests: when converting to GMT can lead to two different hours due to DST ambiguity, the timezone
			 * conversion in PHP always uses the later (higher) GMT date/time. Therefore converting between GMT and
			 * local is always lossy wherever there's GMT or other reasons for date/time overlap.
			 */

			// US Eastern ........... America/New_York      DST began Mar 12, 2017 (UTC-4) ** DST ends Nov 5, 2017 (UTC-5)
			['2017-08-14 20:11:22', 'America/New_York', '2017-08-15 00:11:22', "New York, date within DST range"],
			['2017-02-14 19:11:22', 'America/New_York', '2017-02-15 00:11:22', "New York, date outside DST range"],
			['2017-03-12 01:59:59', 'America/New_York', '2017-03-12 06:59:59', "New York, date before DST start"],
			['2017-03-12 02:00:00', 'America/New_York', '2017-03-12 07:00:00', "New York, date right on DST start"],
			['2017-03-12 02:15:00', 'America/New_York', '2017-03-12 07:15:00', "New York, INVALID TIME DUE TO DST"],
			['2017-03-12 03:00:00', 'America/New_York', '2017-03-12 07:00:00', "New York, date right on DST start (duplicate time, same second)"],
			['2017-11-05 01:00:00', 'America/New_York', '2017-11-05 05:00:00', "New York, an hour before DST end"],
			['2017-11-05 01:59:59', 'America/New_York', '2017-11-05 05:59:59', "New York, date before DST end"],
			['2017-11-05 02:00:00', 'America/New_York', '2017-11-05 06:00:00', "New York, date right on DST end (invalid conversion)", true],
			['2017-11-05 02:00:00', 'America/New_York', '2017-11-05 07:00:00', "New York, date right on DST end"],

			// US Mountain .......... America/Denver        DST began Mar 12, 2017 (UTC-6) ** DST ends Nov 5, 2017 (UTC-7)
			['2017-08-14 18:11:22', 'America/Denver', '2017-08-15 00:11:22', "Denver, date within DST range"],
			['2017-02-14 17:11:22', 'America/Denver', '2017-02-15 00:11:22', "Denver, date outside DST range"],
			['2017-03-12 01:59:59', 'America/Denver', '2017-03-12 08:59:59', "Denver, date before DST start"],
			['2017-03-12 02:00:00', 'America/Denver', '2017-03-12 09:00:00', "Denver, date right on DST start"],
			['2017-03-12 02:15:00', 'America/Denver', '2017-03-12 09:15:00', "Denver, INVALID TIME DUE TO DST"],
			['2017-03-12 03:00:00', 'America/Denver', '2017-03-12 09:00:00', "Denver, date right on DST start (duplicate time, same second)"],
			['2017-11-05 01:00:00', 'America/Denver', '2017-11-05 07:00:00', "Denver, an hour before DST end"],
			['2017-11-05 01:59:59', 'America/Denver', '2017-11-05 07:59:59', "Denver, date before DST end"],
			['2017-11-05 02:00:00', 'America/Denver', '2017-11-05 08:00:00', "Denver, date right on DST end", true],
			['2017-11-05 02:00:00', 'America/Denver', '2017-11-05 09:00:00', "Denver, date right on DST end"],

			// US Mountain no DST ... America/Phoenix       UTC-7 all year round
			// No negative tests here (no DST!)
			['2017-08-14 17:11:22', 'America/Phoenix', '2017-08-15 00:11:22', "Phoenix, date within DST range"],
			['2017-02-14 17:11:22', 'America/Phoenix', '2017-02-15 00:11:22', "Phoenix, date outside DST range"],
			['2017-03-12 01:59:59', 'America/Phoenix', '2017-03-12 08:59:59', "Phoenix, date before DST start"],
			['2017-03-12 02:00:00', 'America/Phoenix', '2017-03-12 09:00:00', "Phoenix, date right on DST start"],
			['2017-11-05 00:59:59', 'America/Phoenix', '2017-11-05 07:59:59', "Phoenix, date before DST end"],
			['2017-11-05 00:00:00', 'America/Phoenix', '2017-11-05 07:00:00', "Phoenix, an hour before DST end"],
			['2017-11-05 01:00:00', 'America/Phoenix', '2017-11-05 08:00:00', "Phoenix, date right on DST end"],

			// EEST (follows DST) ... Asia/Nicosia          DST began Mar 26, 2017 (UTC+3) ** DST ends Oct 29, 2017 (UTC+2)
			['2017-08-15 03:11:22', 'Asia/Nicosia', '2017-08-15 00:11:22', "EEST, date within DST range"],
			['2017-02-15 02:11:22', 'Asia/Nicosia', '2017-02-15 00:11:22', "EEST, date outside DST range"],
			['2017-03-26 02:59:59', 'Asia/Nicosia', '2017-03-26 00:59:59', "EEST, date right before DST start"],
			['2017-03-26 03:00:00', 'Asia/Nicosia', '2017-03-26 01:00:00', "EEST, date right on DST start"],
			['2017-03-26 03:15:00', 'Asia/Nicosia', '2017-03-26 01:15:00', "EEST, INVALID DATE DUE TO DST"],
			['2017-03-26 04:00:00', 'Asia/Nicosia', '2017-03-26 01:00:00', "EEST, date right on DST start (duplicate time, same second)"],
			// NEGATIVE TEST: the conversion can be satisfied by the GMT time which translates to the same local time without using DST
			['2017-10-29 02:59:59', 'Asia/Nicosia', '2017-10-28 23:59:59', "EEST, date right before DST end"],
			['2017-10-29 03:00:00', 'Asia/Nicosia', '2017-10-29 00:00:00', "EEST, date right on DST end", true],
			['2017-10-29 03:00:00', 'Asia/Nicosia', '2017-10-29 01:00:00', "EEST, date right on DST end"],
			['2017-10-29 04:00:00', 'Asia/Nicosia', '2017-10-29 02:00:00', "EEST, date one hour after DST end"],
		];
	}

	public static function getTestGetApplicableTimezone()
	{
		return [
			// $userID, $forced, $expected, $message
			[null, null, 'America/New_York', "Not logged in user must return the Server Timezone"],
			[null, 'Europe/Athens', 'Europe/Athens', "Not logged in user with forced timezone must return the forced timezone"],
			[1001, null, 'Asia/Nicosia', "User with specific timezone must return their specific timezone"],
			[1001, 'Europe/Athens', 'Europe/Athens', "Forced timezone must override user's timezone"],
			[1002, null, 'America/New_York', "User without a specific timezone must return the Server Timezone"],
			[1001, 'Europe/Athens', 'Europe/Athens', "Forced timezone must override a user without a specific timezone"],
		];
	}
}

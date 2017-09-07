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
	 * Australia Eastern .... Australia/Sydney      DST ended Apr 02, 2017 (UTC+10) ** DST starts Oct 8, 2017 (UTC+11)
	 * Queensland ........... Australia/Brisbane    UTC+10 all year round
	 * Tokyo, Japan ......... Asia/Tokyo            UTC+9 all year round (East Asia doesn't follow DST)
	 * India ................ Asia/Kolkata          UTC+05:30 all year round
	 * Anywhere on Earth .... Etc/GMT+12            UTC-12 all year round (NOT a typo! See http://php.net/manual/en/timezones.others.php; also see https://www.timeanddate.com/time/zones/aoe)
	 */

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

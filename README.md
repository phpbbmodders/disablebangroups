# Disable Ban Groups

Disables the ability to ban users of selected groups; configured in the ACP

## Installation

1. Download the extension
2. Copy the whole archive content to /ext/phpbbmodders/disablebangroups
3. Go to your phpBB board > Administration Control Panel > Customise > Manage extensions > Group Ban: enable

## Update instructions

1. Go to your phpBB board > Administration Control Panel > Customise > Manage extensions > Group Ban: disable
2. Delete all files of the extension from /ext/phpbbmodders/disablebangroups
3. Upload all the new files to the same locations
4. Go to your phpBB board > Administration Control Panel > Customise > Manage extensions > Group Ban: enable
5. Purge the board cache

## Automated testing

We use automated unit tests to prevent regressions. Check out our build below:

master: [![Build Status](https://github.com/phpbbmodders/disablebangroups/workflows/Tests/badge.svg)](https://github.com/phpbbmodders/disablebangroups/actions)

## License

[GNU General Public License v2](license.txt)

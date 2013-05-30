#!/bin/bash
php twitter-fetcher.php >> cron.log
php parse-inserter.php  >> cron.log

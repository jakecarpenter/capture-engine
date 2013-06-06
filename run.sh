#!/bin/bash
php /vhosts/jakecarpenter.com/public/capengine/parse-user-syncer.php >> /vhosts/jakecarpenter.com/public/capengine/cron.log
php /vhosts/jakecarpenter.com/public/capengine/twitter-fetcher.php >> /vhosts/jakecarpenter.com/public/capengine/cron.log
php /vhosts/jakecarpenter.com/public/capengine/parse-inserter.php  >> /vhosts/jakecarpenter.com/public/capengine/cron.log

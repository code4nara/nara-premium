#!/bin/sh

env='/usr/bin/env'
pwd='/usr/bin/pwd'
prefix=`${pwd}`

${env} php ${prefix}/nara-premium-scraping.php
${env} php ${prefix}/nara-premium-parse.php
#${env} php ${prefix}/nara-premium-geo.php

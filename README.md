NFL_DepthCharts
===============

skims depthchart, saves json to disk &amp; uses simple bootstrap page for displaying data

=================

The skimming script will require elevated privileges. It is meant to be ran as a cronjob. Something like this: 
0 8 * * * curl http://localhost/nfl/data/skim.php


It is definitely a work in progress, but it works.

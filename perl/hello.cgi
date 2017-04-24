#!/usr/local/bin/perl -w
use strict;
use CGI;
my $q = CGI->new;
print $q->header,
      $q->start_html,
      $q->h1('Hello World!'),
      $q->end_html;

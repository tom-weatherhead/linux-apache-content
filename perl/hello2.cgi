#!/usr/local/bin/perl

use strict;
use warnings FATAL => qw(all); # Make all warnings fatal.  See page 862.

use CGI;

my $q = CGI->new;
my $foo = $q->param('foo') || 'undefined'; # param() can return a scalar or a list.

print $q->header,
      $q->start_html,
      $q->h1("Hello World! 2; foo is $foo"),
      $q->end_html;

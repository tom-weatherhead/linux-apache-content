#!/usr/bin/perl

use strict;
use warnings FATAL => qw(all); # Make all warnings fatal.  See page 862.

use CGI qw/:standard/; # Load standard CGI routines.

# my $q = CGI->new;
my $foo = param('foo') || 'undefined'; # param() can return a scalar or a list.

print header,
      start_html,
      h1("Hello World! 3; foo is $foo"),
      end_html;

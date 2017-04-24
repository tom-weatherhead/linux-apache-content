#!/usr/bin/perl

# Tic-Tac-Toe as a Perl Web service - June 26, 2014

use strict;
use warnings FATAL => qw(all); # Make all warnings fatal.  See page 862.

use CGI qw/:standard/; # Load standard CGI routines.

use lib "/var/www/html/perl/tictactoe";
use GameEngine;

# my $cgi = CGI->new;
my $boardAsString = param("board") || "EEEEEEEEE"; # param() can return a scalar or a list.
my $ply = param("ply") || 6;
my $gameEngine = GameEngine->new();
my ($httpStatusCode, $msg, $result) = $gameEngine->doWork($boardAsString, $ply);

print header(-status => "$httpStatusCode $gameEngine->{httpStatusMessage}{$httpStatusCode}"),
      # "\n",
      ($httpStatusCode == 200) ? $result : $msg; # $msg is not displayed if the status code is not OK (at least in Firefox).

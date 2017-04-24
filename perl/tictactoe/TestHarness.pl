#!/usr/bin/perl

# Tic-Tac-Toe game engine test harness - June 26, 2014

use strict;
use warnings FATAL => qw(all); # Make all warnings fatal.  See page 862.

use GameEngine;

my $boardAsString = shift || "EEEEEEEEE";
my $ply = shift || 6;

my $gameEngine = GameEngine->new();
my ($httpStatusCode, $msg, $result) = $gameEngine->doWork($boardAsString, $ply);

print "HTTP Status Code: $httpStatusCode\n";
print "HTTP Status Message: $gameEngine->{httpStatusMessage}{$httpStatusCode}\n";
print "Message: $msg\n";
print "Result: $result\n";

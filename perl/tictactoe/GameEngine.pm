# A Tic-Tac-Toe game engine in Perl - June 26, 2014

package GameEngine;

use strict;
use warnings FATAL => qw(all); # Or: use warnings qw/FATAL all/; See page 673.

use constant MINIMUM_PLY => 1; # See page 295.
use constant MAXIMUM_PLY => 16;
use constant VICTORY_VALUE => 100;
use constant DEFEAT_VALUE => -100;

sub new {
	my $invocant = shift;
	my $self = bless({}, ref $invocant || $invocant);
	# $self->{boardDimension} = 0;
	# $self->{boardArea} = 0;
	# $self->{board} = [];
	# $self->{currentPlayer} = "X";
	# $self->{minimumPly} = 1;
	# $self->{maximumPly} = 16;
	# $self->{ply} = 0;
	# $self->{boardPopulation} = 0;
	# $self->{victoryValue} = 100;
	# $self->{defeatValue} = -100;
	$self->{httpStatusMessage}{200} = "OK";
	$self->{httpStatusMessage}{400} = "Bad Request";
	$self->{httpStatusMessage}{500} = "Internal Server Error";
	return $self;
}

sub isVictory {
	my $self = shift;
	my $player = shift;
	my $row = shift;
	my $column = shift;

	# 1) Check the specified row.
	my $victory = 1; # True.

	for (my $column2 = 0; $column2 < $self->{boardDimension}; ++$column2)
	{

		if ($self->{board}[$row * $self->{boardDimension} + $column2] ne $player)
		{
			$victory = 0; # False.
			last;
		}
	}

	return 1 if $victory;

	# 2) Check the specified column.
	$victory = 1;

	for (my $row2 = 0; $row2 < $self->{boardDimension}; ++$row2)
	{

		if ($self->{board}[$row2 * $self->{boardDimension} + $column] ne $player)
		{
			$victory = 0;
			last;
		}
	}

	return 1 if $victory;

	if ($row == $column)
	{
		# 3) Check the primary diagonal.
		$victory = 1;

		for (my $i = 0; $i < $self->{boardDimension}; ++$i)
		{

			if ($self->{board}[$i * $self->{boardDimension} + $i] ne $player)
			{
				$victory = 0;
				last;
			}
		}

		return 1 if $victory;
	}

	if ($row + $column == $self->{boardDimension} - 1)
	{
		# 4) Check the secondary diagonal.
		$victory = 1;

		for (my $i = 0; $i < $self->{boardDimension}; ++$i)
		{

			if ($self->{board}[$i * $self->{boardDimension} + $self->{boardDimension} - 1 - $i] ne $player)
			{
				$victory = 0;
				last;
			}
		}

		return 1 if $victory;
	}

	return 0;
}

sub placePiece { #($player, $row, $column)
	my $self = shift;
	my $player = shift;
	my $row = shift;
	my $column = shift;

	# If player is X or O, the square being written to must be empty just before the move is made.
	# If player is Empty, the square being written to must be non-empty just before the move is made, and displayMove must be false.

	if ($row < 0 || $row >= $self->{boardDimension})
	{
		die "PlacePiece() : row $row is out of range";
	}

	if ($column < 0 || $column >= $self->{boardDimension})
	{
		die "PlacePiece() : column $column is out of range";
	}

	my $oldSquareContent = $self->{board}[$row * $self->{boardDimension} + $column];

	if ($player ne " ")
	{

		if ($oldSquareContent ne " ")
		{
			die "PlacePiece() : Attempted to write an X or an O into a non-empty square";
		}
	}
	else
	{

		if ($oldSquareContent eq " ")
                {
			die "PlacePiece() : Attempted to erase an already-empty square.";
		}
	}

	$self->{board}[$row * $self->{boardDimension} + $column] = $player;

	if ($player eq " ")
	{
		--$self->{boardPopulation};
	}
	else
	{
		++$self->{boardPopulation};
	}

	my $victory = $player ne " " && $self->isVictory($player, $row, $column);

	return $victory; # This can return true for real or speculative moves.
}

sub getNumOpenLines { # ($opponent)
	my $self = shift;
	my $opponent = shift;

	my $numOpenLines = 2 * $self->{boardDimension} + 2;

	# 1) Check all rows.

	for (my $row = 0; $row < $self->{boardDimension}; ++$row)
	{

		for (my $column = 0; $column < $self->{boardDimension}; ++$column)
                {

			if ($self->{board}[$row * $self->{boardDimension} + $column] eq $opponent)
			{
				--$numOpenLines;
				last;
			}
		}
	}

	# 2) Check all columns.

	for (my $column = 0; $column < $self->{boardDimension}; ++$column)
	{

		for (my $row = 0; $row < $self->{boardDimension}; ++$row)
		{

			if ($self->{board}[$row * $self->{boardDimension} + $column] eq $opponent)
			{
				--$numOpenLines;
				last;
			}
		}
	}

	# 3) Check the primary diagonal.

	for (my $row = 0; $row < $self->{boardDimension}; ++$row)
	{

		if ($self->{board}[$row * $self->{boardDimension} + $row] eq $opponent)
		{
			--$numOpenLines;
			last;
		}
	}

	# 4) Check the secondary diagonal.

	for (my $row = 0; $row < $self->{boardDimension}; ++$row)
	{

		if ($self->{board}[$row * $self->{boardDimension} + $self->{boardDimension} - 1 - $row] eq $opponent)
		{
			--$numOpenLines;
			last;
		}
	}

	return $numOpenLines;
}

sub getBoardValue { # ($player, $opponent)
	my $self = shift;
	my $player = shift;
	my $opponent = shift;

	# This (and the PHP original) is backwards:
	# return $self->getNumOpenLines($player) - $self->getNumOpenLines($opponent);
	# It should be:
	return $self->getNumOpenLines($opponent) - $self->getNumOpenLines($player);
}

sub findBestMove {
	my $self = shift;
	my $player = shift;
	my $ply = shift;
	my $bestUncleRecursiveScore = shift; # For alpha-beta pruning.

	my $opponent = ($player eq "X") ? "O" : "X";
	my $bestMoveValue = DEFEAT_VALUE - 1; # Worse than the worst possible move value.
	my $returnBestCoordinates = $ply == 0;
	# $bestMoveList = $returnBestCoordinates ? [] : null;
	my $bestMoveList = [];
	# my $doneSearching = 0; # Use a loop label instead?

	# for (my $row = 0; $row < $self->{boardDimension} && !$doneSearching; ++$row)
	OUTER: for (my $row = 0; $row < $self->{boardDimension}; ++$row)
	{

		for (my $column = 0; $column < $self->{boardDimension}; ++$column)
		{
			my $moveValue = 0;
			my $currentSquareIndex = $row * $self->{boardDimension} + $column;

			if ($self->{board}[$currentSquareIndex] ne " ")
			{
				next;
			}

			if ($self->placePiece($player, $row, $column)) # I.e. if this move results in immediate victory.
			{
				$moveValue = VICTORY_VALUE;
			}
			# Note: The usage of $ply here differs from the PHP version.
			elsif ($self->{boardPopulation} < $self->{boardArea} && $ply < $self->{ply})
			{
				$moveValue = -$self->findBestMove($opponent, $ply + 1, $bestMoveValue);
			}
			else
			{
				$moveValue = $self->getBoardValue($player, $opponent);
			}

			$self->placePiece(" ", $row, $column);

			if ($moveValue == $bestMoveValue && $returnBestCoordinates)
			{
				push @$bestMoveList, $currentSquareIndex;
			}
			elsif ($moveValue > $bestMoveValue)
			{
				$bestMoveValue = $moveValue;

				if ($bestMoveValue > -$bestUncleRecursiveScore) 
				{
					# Alpha-beta pruning.  Because of the initial parameters for the top-level move, this break is never executed for the top-level move.
					# $doneSearching = 1;
					# last; # ie. return.
					# print "Alpha-beta pruning at ply $ply\n";
					last OUTER;
				}
				elsif ($returnBestCoordinates) # ($bestMoveList != null)
				{
					$bestMoveList = [];
					push @$bestMoveList, $currentSquareIndex;
				}
				elsif ($bestMoveValue == VICTORY_VALUE)
				{
					# Prune the search tree, since we are not constructing a list of all of the best moves.
					# $doneSearching = 1;
					# last;
					# print "Victory detected at ply $ply\n";
					last OUTER;
				}
			}
		}
	}

	if ($bestMoveValue < DEFEAT_VALUE || $bestMoveValue > VICTORY_VALUE)
	{
		die "FindBestMove() : bestMoveValue is out of range: $bestMoveValue";
	}
	elsif (!$returnBestCoordinates)
	{
		# $bestMove = -1;
		return $bestMoveValue;
	}
	elsif (@$bestMoveList == 0)
	{
		die "FindBestMove() : The bestMoveList is empty at the end of the method";
	}
	else
	{
		# $bestMove = $$bestMoveList[int(rand(scalar @$bestMoveList))]; # This may need $$ or ${$...}
		return $$bestMoveList[int(rand(scalar @$bestMoveList))];
	}

	# return $bestMoveValue;
}

sub doWork {
	my $self = shift;
	my $boardAsString = shift;
	$self->{ply} = shift;

	$self->{boardArea} = length $boardAsString;

	if ($self->{boardArea} == 9)
	{
		$self->{boardDimension} = 3;
	}
	elsif ($self->{boardArea} == 16)
	{
		$self->{boardDimension} = 4;
	}
	else
	{
		return (400, "The board area is neither 9 nor 16", -1);
	}

	$self->{board} = [];
	my $xPopulation = 0;
	my $oPopulation = 0;

	for (split //, $boardAsString) { # For each character in $boardAsString

		if ($_ eq "X")
		{
			push $self->{board}, "X";
			++$xPopulation;
		}
		elsif ($_ eq "O")
		{
			push $self->{board}, "O";
			++$oPopulation;
		}
		else
		{
			push $self->{board}, " ";
		}
	}

	$self->{boardPopulation} = $xPopulation + $oPopulation;

	if ($self->{boardPopulation} == $self->{boardArea})
	{
		return (400, "The board is already full", -1);
	}

	if ($xPopulation == $oPopulation)
	{
		$self->{currentPlayer} = "X";
	}
	elsif ($xPopulation == $oPopulation + 1)
	{
		$self->{currentPlayer} = "O";
	}
	else
	{
		return (400, "Cannot infer the current player from the board", -1);
	}

	# if ($self->{ply} < $self->{minimumPly})
	if ($self->{ply} < MINIMUM_PLY)
	{
		return (400, "The ply is too small", -1);
	}
	# elsif ($self->{ply} > $self->{maximumPly})
	elsif ($self->{ply} > MAXIMUM_PLY)
	{
		return (400, "The ply is too large", -1);
	}

	# Question: Can we trap exceptions (from die "...") and return them as (500, msg, -1) ?
	# Or can we send a signal instead of dying, and then handle it here (or somewhere)?
	my $bestMove = eval { $self->findBestMove($self->{currentPlayer}, 0, DEFEAT_VALUE - 1); };

	if ($@)
	{
		return (500, $@, -1);
	}

	# Return a list: (HTTPStatusCode, ErrorMessage, BestMove)
	return (200, "Success", $bestMove);
}

1; # The module must return a true value.

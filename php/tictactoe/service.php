<?php
	// A Tic-Tac-Toe game engine as a PHP Web service - February 12, 2014

    class GameEngine
    {
		/* public */ const SquareContentType_EmptySquare = -1;
		const SquareContentType_X = 0;
		const SquareContentType_O = 1;
        public $boardDimension;
        public $boardArea;      // board.Length may make this unnecessary.
        public $board;
        public $currentPlayer;
        const minimumPly = 1;
        const maximumPly = 16;
		public $ply;
        public $boardPopulation;
        const victoryValue = 100;
        const defeatValue = -100;

		public function __construct()
		{

			if (!isset($_GET["board"]))
			{
				throw new Exception("The board parameter is missing from the request");
			}
			elseif (!isset($_GET["ply"]))
			{
				throw new Exception("The ply parameter is missing from the request");
			}

			$boardAsString = $_GET["board"];
			$this->ply = $_GET["ply"];

			$this->boardArea = strlen($boardAsString);

			if ($this->boardArea == 9)
			{
				$this->boardDimension = 3;
			}
			elseif ($this->boardArea == 16)
			{
				$this->boardDimension = 4;
			}
			else
			{
				throw new Exception("The board area is neither 9 nor 16");
			}

			$this->board = [];
			$XPopulation = 0;
			$OPopulation = 0;

			for ($i = 0; $i < $this->boardArea; ++$i)
			{

				if ($boardAsString[$i] == 'X')
				{
					$this->board[] = self::SquareContentType_X;
					++$XPopulation;
				}
				elseif ($boardAsString[$i] == 'O')
				{
					$this->board[] = self::SquareContentType_O;
					++$OPopulation;
				}
				else
				{
					$this->board[] = self::SquareContentType_EmptySquare;
				}
			}

			$this->boardPopulation = $XPopulation + $OPopulation;

			if ($this->boardPopulation == $this->boardArea)
			{
				throw new Exception("The board is already full");
			}

			if ($XPopulation == $OPopulation)
			{
				$this->currentPlayer = self::SquareContentType_X;
			}
			elseif ($XPopulation == $OPopulation + 1)
			{
				$this->currentPlayer = self::SquareContentType_O;
			}
			else
			{
				throw new Exception("Cannot infer the current player from the board");
			}

			if ($this->ply < self::minimumPly)
			{
				throw new Exception("The ply is too small");
			}
			elseif ($this->ply > self::maximumPly)
			{
				throw new Exception("The ply is too large");
			}
		}

        private function IsVictory($player, $row, $column)
        {
            // 1) Check the specified row.
            $victory = true;

            for ($column2 = 0; $column2 < $this->boardDimension; ++$column2)
            {

                if ($this->board[$row * $this->boardDimension + $column2] != $player)
                {
                    $victory = false;
                    break;
                }
            }

            if ($victory)
            {
                return true;
            }

            // 2) Check the specified column.
            $victory = true;

            for ($row2 = 0; $row2 < $this->boardDimension; ++$row2)
            {

                if ($this->board[$row2 * $this->boardDimension + $column] != $player)
                {
                    $victory = false;
                    break;
                }
            }

            if ($victory)
            {
                return true;
            }

            if ($row == $column)
            {
                // 3) Check the primary diagonal.
                $victory = true;

                for ($i = 0; $i < $this->boardDimension; ++$i)
                {

                    if ($this->board[$i * $this->boardDimension + $i] != $player)
                    {
                        $victory = false;
                        break;
                    }
                }

                if ($victory)
                {
                    return true;
                }
            }

            if ($row + $column == $this->boardDimension - 1)
            {
                // 4) Check the secondary diagonal.
                $victory = true;

                for ($i = 0; $i < $this->boardDimension; ++$i)
                {

                    if ($this->board[$i * $this->boardDimension + $this->boardDimension - 1 - $i] != $player)
                    {
                        $victory = false;
                        break;
                    }
                }

                if ($victory)
                {
                    return true;
                }
            }

            return false;
        }

        private function PlacePiece($player, $row, $column)
        {
            // If player is X or O, the square being written to must be empty just before the move is made.
            // If player is Empty, the square being written to must be non-empty just before the move is made, and displayMove must be false.

            if ($row < 0 || $row >= $this->boardDimension)
            {
                throw new Exception("PlacePiece() : row $row is out of range");
            }

            if ($column < 0 || $column >= $this->boardDimension)
            {
                throw new Exception("PlacePiece() : column $column is out of range");
            }

            $oldSquareContent = $this->board[$row * $this->boardDimension + $column];

            if ($player != self::SquareContentType_EmptySquare)
            {

                if ($oldSquareContent != self::SquareContentType_EmptySquare)
                {
                    throw new Exception("PlacePiece() : Attempted to write an X or an O into a non-empty square");
                }
            }
            else
            {

                if ($oldSquareContent == self::SquareContentType_EmptySquare)
                {
                    throw new Exception("PlacePiece() : Attempted to erase an already-empty square.");
                }
            }

            $this->board[$row * $this->boardDimension + $column] = $player;

            if ($player == self::SquareContentType_EmptySquare)
            {
                --$this->boardPopulation;
            }
            else
            {
                ++$this->boardPopulation;
            }

            $victory = $player != self::SquareContentType_EmptySquare && $this->IsVictory($player, $row, $column);

            return $victory; // This can return true for real or speculative moves.
        }

        private function GetNumOpenLines($opponent)
        {
            $numOpenLines = 2 * $this->boardDimension + 2;

            // 1) Check all rows.

            for ($row = 0; $row < $this->boardDimension; ++$row)
            {

                for ($column = 0; $column < $this->boardDimension; ++$column)
                {

                    if ($this->board[$row * $this->boardDimension + $column] == $opponent)
                    {
                        --$numOpenLines;
                        break;
                    }
                }
            }

            // 2) Check all columns.

            for ($column = 0; $column < $this->boardDimension; ++$column)
            {

                for ($row = 0; $row < $this->boardDimension; ++$row)
                {

                    if ($this->board[$row * $this->boardDimension + $column] == $opponent)
                    {
                        --$numOpenLines;
                        break;
                    }
                }
            }

            // 3) Check the primary diagonal.

            for ($row = 0; $row < $this->boardDimension; ++$row)
            {

                if ($this->board[$row * $this->boardDimension + $row] == $opponent)
                {
                    --$numOpenLines;
                    break;
                }
            }

            // 4) Check the secondary diagonal.

            for ($row = 0; $row < $this->boardDimension; ++$row)
            {

                if ($this->board[$row * $this->boardDimension + $this->boardDimension - 1 - $row] == $opponent)
                {
                    --$numOpenLines;
                    break;
                }
            }

            return $numOpenLines;
        }

        private function GetBoardValue($player, $opponent)
        {
			// 2014/06/26 : This is backwards:
            //return $this->GetNumOpenLines($player) - $this->GetNumOpenLines($opponent);
			// This is correct:
            return $this->GetNumOpenLines($opponent) - $this->GetNumOpenLines($player);
        }

        private function FindBestMove($player, $ply,
            $bestUncleRecursiveScore,	// For alpha-beta pruning.
            $returnBestCoordinates,
            &$bestMove)
        {
            $opponent = ($player == self::SquareContentType_X) ? self::SquareContentType_O : self::SquareContentType_X;
            $bestMoveValue = self::defeatValue - 1;     // Worse than the worst possible move value.
            //$bestMoveList = $returnBestCoordinates ? [] : null;
			$bestMoveList = []; // ThAW 2014/02/12 : I suspect that [] == null in PHP.
            $doneSearching = false;

            for ($row = 0; $row < $this->boardDimension && !$doneSearching; ++$row)
            {

                for ($column = 0; $column < $this->boardDimension; ++$column)
                {
                    $moveValue = 0;
                    $currentSquareIndex = $row * $this->boardDimension + $column;

                    if ($this->board[$currentSquareIndex] != self::SquareContentType_EmptySquare)
                    {
                        continue;
                    }

                    if ($this->PlacePiece($player, $row, $column)) // I.e. if this move results in immediate victory.
                    {
                        $moveValue = self::victoryValue;
                    }
                    else if ($this->boardPopulation < $this->boardArea && $ply > 1)
                    {
                        $dummyBestMove = 0;
                        $moveValue = -$this->FindBestMove($opponent, $ply - 1, $bestMoveValue, false, $dummyBestMove);
                    }
                    else
                    {
                        $moveValue = $this->GetBoardValue($player, $opponent);
                    }

                    $this->PlacePiece(self::SquareContentType_EmptySquare, $row, $column);

                    if ($moveValue == $bestMoveValue && $returnBestCoordinates /* && $bestMoveList != null */)
                    {
                        $bestMoveList[] = $currentSquareIndex;
                    }
                    else if ($moveValue > $bestMoveValue)
                    {
                        $bestMoveValue = $moveValue;

                        if ($bestMoveValue > -$bestUncleRecursiveScore) 
                        {
                            // Alpha-beta pruning.  Because of the initial parameters for the top-level move, this break is never executed for the top-level move.
                            $doneSearching = true;
                            break; // ie. return.
                        }
                        else if ($returnBestCoordinates) //($bestMoveList != null)
                        {
                            $bestMoveList = [];
                            $bestMoveList[] = $currentSquareIndex;
                        }
                        else if ($bestMoveValue == self::victoryValue)
                        {
                            // Prune the search tree, since we are not constructing a list of all of the best moves.
                            $doneSearching = true;
                            break;
                        }
                    }
                }
            }

            if ($bestMoveValue < self::defeatValue || $bestMoveValue > self::victoryValue)
            {
                throw new Exception("FindBestMove() : bestMoveValue is out of range: $bestMoveValue");
            }
			elseif (!$returnBestCoordinates)
			{
				$bestMove = -1;
			}
            else if (count($bestMoveList) == 0)
            {
                throw new Exception("FindBestMove() : The bestMoveList is empty at the end of the method");
            }
            else
            {
				$bestMove = $bestMoveList[array_rand($bestMoveList)];
            }

            return $bestMoveValue;
        }

		public function DoWork()
		{
			$bestMove = 0;
			$this->FindBestMove($this->currentPlayer, $this->ply, self::defeatValue - 1, true, $bestMove);
			return $bestMove;
		}
	}

    $statusMessages = array( 
        200 => 'OK', 
        400 => 'Bad Request',   
        500 => 'Internal Server Error');
	$statusCode = 400;
	$body = "";

	try
	{
		$engine = new GameEngine();

		$statusCode = 500;

		$body = $engine->DoWork(); // This will return a single integer.

		$statusCode = 200;
	}
	catch (Exception $ex)
	{
		$body = "$statusCode {$statusMessages[$statusCode]}: {$ex->getMessage()}";
	}

    header("HTTP/1.1 $statusCode {$statusMessages[$statusCode]}");
	echo $body;
?>

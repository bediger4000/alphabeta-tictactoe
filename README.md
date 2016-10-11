# alphabeta-tictactoe
Alpha beta minimaxing tic tac toe in various languages

My first effort at a PHP program, an alpha-beta minimax tic tac toe player.
Every human's move sends an HTTP POST back to the ttt.php module, which
figures out its next move, and gives back HTML with the current board state,
plus HTML buttons for the human to choose the next move.  This works because
tic tac toe has a particularly simple state at any stage of the game.


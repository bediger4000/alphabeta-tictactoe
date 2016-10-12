#PHP alpha-beta minimax tic tac toe

My first effort at a PHP program, an alpha-beta minimax tic tac toe player.
Every human's move sends an HTTP POST back to the ttt.php module, which
figures out its next move, and gives back HTML with the current board state,
plus HTML buttons for the human to choose the next move.  This works because
tic tac toe has a particularly simple state at any stage of the game.

The POST request to the PHP program names tic tac toe cells like this:

    UL  UM  UR
    ML  MM  MR
    LL  LM  LR

This allows you to start a game anywhere you want, with a specially crafted URL:

> `http://whatever/ttt/ttt.php?UL=X&UR=X&LL=X&LR=X`

That particular configuration can never arise in a real game, starting from an
empty board. But still...

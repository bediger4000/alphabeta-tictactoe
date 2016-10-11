#Alpha beta minimaxing tic tac toe in various languages

A set of tic tac toe games in 4 different programming languges, C, Python, bash and PHP.

In every one of these games (except the PHP version, which is point-n-click),
the 9-cell board is labeled like this:

    0   1   2
    3   4   5
    6   7   8

Each game defaults to the human going first, but command line options can
coerce the computer to going first.

* C version: `./ttt -C`
* Bash version; `./ttt.sh -C`
* Python 2.x version: `./ttt.py -c`

In all versions, the algorithm is alpha beta minimaxing. It could potentially
examine the entire game tree on each move, but I don't think it does.

I was untruthful: the C language version is non-recursive, and just plays according
to a very simple set of rules-of-thumb that almost certainly keep it from losing.

Ther's no obvious winner from a software engineering point of view. They all clock
in at between 200 and 300 lines. The Bash version is convoluted by the lack of function
return values, necessitating the use of globally-scoped variables as "return values".
The C version is a lot faster than any of the others. The Python version has a lot
of frills the others don't, like choice of mark characters. The PHP version is point-n-click.

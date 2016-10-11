#include <stdio.h>
#include <stdlib.h>
/* Board is an 8-element array of ints, organize 0-based indexes
 * like this:
 *
 *  0   1   2
 *  3   4   5
 *  6   7   8
 *
 */

int winning_triads[8][3] = {
	{0,1,2}, {3,4,5}, {6,7,8},
	{0,3,6}, {1,4,7}, {2,5,8},
	{0,4,8}, {2,4,6}
};

int  find_winner(int board[]);     /* if computer wins 1, if humn -1, 0 no win */
int  move_remaining(int board[]);  /* 1 if a legal move remains, 0 otherwise */
int  computer_moves(int board[]);  /* determine computer's next move */
int  get_human_move(int board[]);  /* get an int between 0 and 8 inclusive */
void print_board(int board[]);

int
main(int ac, char **av)
{
	int goes_first = -1;
	int board[9];
	int winner = 0;
	int new_move;
	int i;

	if (ac > 1)
	{
		if (av[1][0] == 'h' || av[1][0] == 'H')
			goes_first = -1;
		else if (av[1][0] == 'c' || av[1][0] == 'C')
			goes_first = 1;
	}

	printf("0   1   2\n3   4   5\n6   7   8\n");

	for (i = 0; i < 9; ++i)
		board[i] = 0;

	if (goes_first == -1)
		printf("\nHuman plays first, X, computer plays 2nd, O\n");
	else
		printf("\nComputer plays first, 0, human plays 2nd, X\n");

	/* Duff-like device. */
	switch (goes_first)
	{
	do {

	case -1:
		new_move = get_human_move(board);  /* human's next move */
		board[new_move] = -1; /* Human is minimizer */
		winner = find_winner(board);
		if (winner) break;

	case 1:
		new_move = computer_moves(board);
		board[new_move] = 1; /* Computer maximises */
		winner = find_winner(board);
		if (winner) break;

		print_board(board);

	} while (0 == winner && move_remaining(board));

	}

	switch (winner)
	{
	case -1:  printf("\nHuman wins.\n"); break;
	case 1:   printf("\nComputer wins\n"); break;
	default:  printf("\nCat got the game\n"); break;
	}

	print_board(board);

	return 0;
}

int
move_remaining(int board[])
{
	int i, legal_move_left = 0;
	for (i = 0; i < 9; ++i)
	{
		if (board[i] == 0)
		{
			legal_move_left = 1;
			break;
		}
	}
	return legal_move_left;
}

int
find_winner(int board[])
{
	int i;
	int winner = 0;

	for (i = 0; i < 8; ++i)
	{
		int sum;
		int *triad = winning_triads[i];

		sum = board[triad[0]] + board[triad[1]] + board[triad[2]];

		if (sum == 3)
			winner = 1;
		else if (sum == -3)
			winner = -1;
	}

	return winner;
}

int corners[] = {0, 2, 6, 8};
int sides[] = {1, 3, 5, 7};

int
computer_moves(int board[])
{
	int i;

	/* fill in any wins */
	for (i = 0; i < 8; ++i)
	{
		int sum;
		int *triad = winning_triads[i];

		sum = board[triad[0]] + board[triad[1]] + board[triad[2]];

		if (sum == 2)
		{
			int j;
			for (j = 0; j < 3; ++j)
			{
				if (board[triad[j]] == 0)
					return triad[j];
			}
		}
	}

	/* block any 2-in-a row */
	for (i = 0; i < 8; ++i)
	{
		int sum;
		int *triad = winning_triads[i];

		sum = board[triad[0]] + board[triad[1]] + board[triad[2]];

		if (sum == -2)
		{
			int j;
			for (j = 0; j < 3; ++j)
			{
				if (board[triad[j]] == 0)
					return triad[j];
			}
		}
	}

	/* Move to center if possible */
	if (board[4] == 0)
		return 4;

	/* Move to a corner if possible */
	for (i = 0; i < 3; ++i)
		if (board[corners[i]] == 0)
			return corners[i];

	/* Move to a side if possible */
	for (i = 0; i < 3; ++i)
		if (board[sides[i]] == 0)
			return sides[i];

	return -1;
}

void
print_board(int board[])
{
		printf("%c   %c   %c\n%c   %c   %c\n%c   %c   %c\n",
			(board[0] == -1? 'X': (board[0] == 0? '_': 'O')),
			(board[1] == -1? 'X': (board[1] == 0? '_': 'O')),
			(board[2] == -1? 'X': (board[2] == 0? '_': 'O')),
			(board[3] == -1? 'X': (board[3] == 0? '_': 'O')),
			(board[4] == -1? 'X': (board[4] == 0? '_': 'O')),
			(board[5] == -1? 'X': (board[5] == 0? '_': 'O')),
			(board[6] == -1? 'X': (board[6] == 0? '_': 'O')),
			(board[7] == -1? 'X': (board[7] == 0? '_': 'O')),
			(board[8] == -1? 'X': (board[8] == 0? '_': 'O'))
		);
}

int
get_human_move(int board[])
{
	char buffer[16];
	int new_move = -1;  /* human's next move */

	while (new_move == -1)
	{
		int candidate;
		printf("Your move > ");
		fgets(buffer, sizeof(buffer), stdin);
		printf("\n");
		candidate = strtol(buffer, NULL, 10);
		if (0 <= candidate && 9 > candidate && board[candidate] == 0)
		{
			new_move = candidate;
			break;
		} else {
			if (0 > candidate || 9 <= candidate)
				fprintf(stderr, "Valid move between 0 and 8\n");
			if (board[candidate] != 0)
				fprintf(stderr, "That spot is taken\n");
		}
	}

	return new_move;
}

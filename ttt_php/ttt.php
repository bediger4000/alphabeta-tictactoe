<html>
<head>
<!-- $Id: ttt.php,v 1.3 2011/07/22 01:46:46 bediger Exp $ -->
<script language=javascript" type="text/javascript">
function setChoice(pos) {
    document.ttt.choice.value = pos;
    document.ttt.submit();
}
</script>
</head>
<body>
<h1>Tic-Tac-Toe</h1>
<form name="ttt" method="post" action="ttt.php" >
<input type='hidden' name='choice' value="" />
<?php
/* Tic Tac Toe
 * Human plays 'X' minimizes,   -1
 * Computer plays 'O' maximizes, 1
 */
define("X_token", -1);
define("O_token", 1);
define("Open_token", 0);

$spaces = array('UL', 'UM', 'UR', 'ML', 'MM', 'MR', 'LL', 'LM', 'LR');
$winning_rows = array(
	array('UL', 'UM', 'UR'),
	array('ML', 'MM', 'MR'),
	array('LL', 'LM', 'LR'),

	array('UL', 'ML', 'LL'),
	array('UM', 'MM', 'LM'),
	array('UR', 'MR', 'LR'),

	array('UR', 'MM', 'LL'),
	array('UL', 'MM', 'LR')
);

function find_winner($bd) {
	global $winning_rows;
	foreach ($winning_rows as $winning_row) {
		$triad_sum = $bd[$winning_row[0]] + $bd[$winning_row[1]]
			+ $bd[$winning_row[2]];
		if ($triad_sum == 3 || $triad_sum == -3)
			return $bd[$winning_row[0]];
	}
	return 0;
}

function legal_move_left($brd) {
	global $spaces;
	foreach ($spaces as $move)
		if ($brd[$move] == Open_token)
			return TRUE;
	return FALSE;
}

// Return -1, 0, 1 for O wins, Cat, X wins, respectively.
function board_value($brd, $player, $next_player, $alpha, $beta) {
	global $spaces;

	$winner = find_winner($brd);

	// Static valuation
	if ($winner != Open_token)
		return $winner;
	else if (!legal_move_left($brd))
		return 0;  // Cat game

	// Dynamic valuation
	foreach ($spaces as $move) {
		if ($brd[$move] == Open_token) {
			$brd[$move] = $player;
			$val = board_value($brd, $next_player, $player, $alpha, $beta);
			$brd[$move] = Open_token;

			if ($player == O_token) {
				if ($val > $alpha) $alpha = $val;
				if ($alpha >= $beta) return $beta;
			} else {
				if ($val < $beta) $beta = $val;
				if ($beta <= $alpha) return $alpha;
			}
		}
	}

	if ($player == O_token)
		return $alpha;

	return $beta;
}

function choose_move($brd) {
	global $spaces;
	$best_val = -2;
	$my_moves = null;
	foreach ($spaces as $move) {
		if ($brd[$move] == Open_token) {
			$brd[$move] = O_token;
			$val = board_value($brd, X_token, O_token, -2, 2);
			$brd[$move] = Open_token;
			if ($val > $best_val) {
				$best_val = $val;
				$my_moves = array($move);
			}
			if ($val == $best_val)
				array_push($my_moves, $move);
		}
	}

	if (count($my_moves) > 0)
		return $my_moves[array_rand($my_moves)];

	return null;
}

function fill_board() {
	global $spaces;
	$board = array(
				'UL' => Open_token, 'UM' => Open_token, 'UR' => Open_token,
				'ML' => Open_token, 'MM' => Open_token, 'MR' => Open_token,
				'LL' => Open_token, 'LM' => Open_token, 'LR' => Open_token
	);
	foreach ($spaces as $k) {
		if (isset($_REQUEST[$k])) {
			if ($_REQUEST[$k] == 'X')
				$board[$k] = X_token;
			if ($_REQUEST[$k] == 'O')
				$board[$k] = O_token;
		}
	}
	if (isset($_REQUEST['choice'])) {
		echo "<!-- Human chose: " . htmlspecialchars($_REQUEST['choice']) . " -->\n";
		if (in_array($_REQUEST['choice'], $spaces))
			$board[$_REQUEST['choice']] = X_token;
	}
	return $board;
}

function print_raw_board($board) {
	global $spaces;
	echo "<!-- ";
	foreach ($spaces as $k) {
		switch ($board[$k]) {
		case X_token:    echo 'X'; break;
		case O_token:    echo 'O'; break;
		case Open_token: echo '.'; break;
		}
	}
	echo " -->\n";
}

function empty_square($pos) {
	global $spaces;
	if (in_array($pos, $spaces))
		fill_square("<input type='button' name='btn"
			. htmlspecialchars($pos)
			. "' value='&#x25A2;' onclick='setChoice(\""
			. htmlspecialchars($pos) . "\");' />"
		);
}

function write_square($mark) {
	if ($mark == X_token)
		fill_square("<img src='X.jpg' alt='X' />");
	else if ($mark == O_token)
		fill_square("<img src='O.jpg' alt='O' />");
}

function fill_square($str) {
	echo "\t\t<td align='center' valign='center' height='170px' width='170px'>"
		. $str
		. "</td>\n"
	;
}

function write_board_html($board, $winning_mark) {
	global $spaces;
	echo "\n\n";

	// Put a "hidden" field for each square marked so far.
	foreach ($board as $pos => $token) {
		switch ($token) {
			case X_token: $mark = 'X'; break;
			case O_token: $mark = 'O'; break;
			case Open_token:
			default:
				$mark = FALSE;
				break;
		}
		if ($mark)
			echo "<input type='hidden' name='" . htmlspecialchars($pos)
				. "' value='" . htmlspecialchars($mark) . "'/>\n";
	}

	echo "\n";
	// Print out HTML for the 9-position board, with either an
	// X-image, an O-image, or an appropriately-valued "button"
	// in each position.
	echo "<table border='0'>\n";
	foreach ($spaces as $w) {
		if ($w == 'UL' || $w == 'ML' || $w == 'LL')
			echo "\t<tr>\n";

		if ($board[$w] == Open_token)
			empty_square($w);
		else
			write_square($board[$w]);

		if ($w == 'UL' || $w == 'UM' || $w == 'ML'
			|| $w == 'MM' || $w == 'LL' || $w == 'LM')
			echo "\t\t<td align='center' valign='center' height='170px' width='3px'><img src='vertical170.png' /></td>\n";

		if ($w == 'UR' || $w == 'MR' || $w == 'LR')
			echo "\t</tr>\n";

		if ($w == 'UR' || $w == 'MR')
			echo "\t<tr>\n\t\t<td colspan='5' align='center' valign='center' width='516px' height='3px'><img src='horizontal516.png' /></td></tr>\n";

	}

	if ($winning_mark) {
		// HTML for who won.
		if ($winning_mark == X_token)
			echo "\t<tr>\n\t\t<td colspan='5' align='center'><blink><strong>Human Wins</strong></blink></td>\n\t</tr>\n";
		if ($winning_mark == O_token)
			echo "\t<tr>\n\t\t<td colspan='5' align='center'><strong>Computer Wins</strong></td>\n\t</tr>\n";
	} else {
		// HTML for draws or next move.
		if (!legal_move_left($board))
			echo "\t<tr><td colspan='5' align='center'><strong>Cat Wins</strong></td></tr>\n";
		else
			echo "\t<tr><td colspan='5' align='center'>&nbsp;</td></tr>\n";
	}
	// HTML for next game.
	echo "\t<tr>\n\t\t<td align='center'><a href='ttt.php'>New - Computer First</a></td>\n\t\t<td colspan='3'></td>\n\t\t<td><a href='empty.html'>New - Human First</a></td>\n\t</tr>\n";
	echo "</table>\n";
}

// Main - fill in board from HTTP params, decide on a move for
// the computer, fill in the board, print out HTML for the board.
// This works because Tic Tac Toe has a fairly simple state at
// any point in the game, and the next move really doesn't depend
// on anything other than the state.
	$programs_choice = null;
	$board = fill_board();
	print_raw_board($board);
	$w = find_winner($board);
	if ($w == 0) {
		$programs_choice = choose_move($board);
		if ($programs_choice) {
			echo "<!-- Computer chooses " . htmlspecialchars($programs_choice) . " -->\n";
			$board[$programs_choice] = O_token;
			$w = find_winner($board);
		} else {
			echo "<!-- No move for Computer -->\n";
		}
		print_raw_board($board);
	}
	write_board_html($board, $w);
	print_raw_board($board);
?>
</form>
</body>
</html>

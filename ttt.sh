#!/bin/bash

function print_board {
	#echo ${BOARD:0:3}
	#echo ${BOARD:3:3}
	#echo ${BOARD:6:3}
	echo ${BOARD:0:1}'|'${BOARD:1:1}'|'${BOARD:2:1}
	echo ${BOARD:3:1}'|'${BOARD:4:1}'|'${BOARD:5:1}
	echo ${BOARD:6:1}'|'${BOARD:7:1}'|'${BOARD:8:1}
}

# NEWBOARD is "return value"
function move {
	local BD=$1
	local MOVE=$2
	local SYMBOL=$3
	if [[ $MOVE == 0 ]]
	then
		NEWBOARD=$SYMBOL${BD:1:8}
	elif [[ $MOVE == 8 ]]
	then
		NEWBOARD=${BD:0:8}$SYMBOL
	else
		NEWBOARD=${BD:0:$MOVE}$SYMBOL${BD:$((MOVE + 1)):$((8 - MOVE))}
	fi
}

# alphabeta BOARD ALPHA BETA PLAYER
function alphabeta {
	local BD=$1
	local ALPHA=$2
	local BETA=$3
	local PLAYER=$4
	local max
	local min
	local YS
	local YQ

	winner $BD
	case $WINNER in
	0) VAL=-1; return;;
	X) VAL=1;  return;;
	*) ;;
	esac
	if [[ $BD =~ '_' ]]
	then
		:
	else
		VAL=0
		return
	fi
	case $PLAYER in
	X)
		max=-1
		for YS in 0 1 2 3 4 5 6 7 8
		do
			if [[ ${BD:$YS:1} == '_' ]]
			then
				move $BD $YS X
				alphabeta $NEWBOARD $ALPHA $BETA 0
				if (( $VAL > $max ))
				then
					max=$VAL
				fi
				if (( $max > $ALPHA ))
				then
					ALPHA=$max
				fi
				if (( $BETA <= $ALPHA ))
				then
					VAL=$max
					break
				fi
			fi
		done
		VAL=$max
		return
	;;
	0)
		min=1
		for YQ in 0 1 2 3 4 5 6 7 8
		do
			if [[ ${BD:$YQ:1} == '_' ]]
			then
				move $BD $YQ 0
				alphabeta $NEWBOARD $ALPHA $BETA X
				if (( VAL < min ))
				then
					min=$VAL
				fi
				if (( min < BETA ))
				then
					BETA=$min
				fi
				if (( BETA <= ALPHA ))
				then
					VAL=$min
					break
				fi
			fi
		done
		VAL=$min
		return
	;;
	*) echo Freakout. BOARD=$BD ALPHA=$ALPHA BETA=$BETA PLAYER=$PLAYER
	;;
	esac
	return
}

# global scope variable WINNER holds return value
function winner {
	WINNER=_
	local ZD=$1
	local TRIP
	for TRIP in \
		${ZD:0:1}${ZD:3:1}${ZD:6:1} \
		${ZD:1:1}${ZD:4:1}${ZD:7:1} \
		${ZD:2:1}${ZD:5:1}${ZD:8:1} \
		${ZD:0:1}${ZD:1:1}${ZD:2:1} \
		${ZD:3:1}${ZD:4:1}${ZD:5:1} \
		${ZD:6:1}${ZD:7:1}${ZD:8:1} \
		${ZD:0:1}${ZD:4:1}${ZD:8:1} \
		${ZD:2:1}${ZD:4:1}${ZD:6:1}
	do
		case $TRIP in
		XXX) WINNER=X; break;;
		000) WINNER=0; break;;
		esac
	done
}

BOARD=_________

if [[ $1 == '-C' ]]
then
	HUMAN=no
else
	HUMAN=yes
fi

while :
do
	if [[ $HUMAN == yes ]]
	then
		VALIDITY=N
		while [[ $VALIDITY == N ]]
		do
			echo -n "Your move: "
			read MINMOVE
			if [[ ${BOARD:$MINMOVE:1} == '_' ]]
			then
				VALIDITY=Y
			fi
		done

		move $BOARD $MINMOVE 0
		BOARD=$NEWBOARD

		winner $BOARD
		[[ $WINNER != _ ]] && break

		[[ $BOARD =~ _ ]] || break
	fi 
	HUMAN=yes

	m=-1
	for SP in 0 1 2 3 4 5 6 7 8
	do
		if [[ ${BOARD:$SP:1} == '_' ]]
		then
			move $BOARD $SP X
			alphabeta $NEWBOARD -1 1 0
			if (( $VAL >= $m ))
			then
				if (( $VAL > $m ))
				then
					m=$VAL
					COMPUTER=$SP
				else
					X=$(( $RANDOM % 2))
					if (( $X == 0 ))
					then
						COMPUTER=$SP
					fi
				fi
			fi
		fi
	done

	if (( $COMPUTER > -1 ))
	then
		echo "My move: $COMPUTER ($m)"
		move $BOARD $COMPUTER X
		BOARD=$NEWBOARD
		print_board $BOARD
	fi

	winner $BOARD
	[[ $WINNER != _ ]] && break

	[[ $BOARD =~ _ ]] || break
done

echo
print_board
case $WINNER in
X) echo "Computer won";;
0) echo "Human won";;
_) echo "Cat got the game";;
esac

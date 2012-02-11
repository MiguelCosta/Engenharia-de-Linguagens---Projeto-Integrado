grammar robot;

robot
	: 'ASPIRADOR' '{' corpo '}'
	;
	
corpo
	: 'DEFINICOES' definicoes 'MOVIMENTOS' movimentos
	;
	
definicoes
	: '{' (definicao)+ '}'
	;
	
definicao
	: DIM '=' '(' x=INT ',' y=INT ')' ';'		  
	| POS '=' '(' x=INT ',' y=INT ')' ';'
	;

movimentos
	: movimento (movimento)*
	;
	
movimento
	: LIGAR ';'
	| DESLIGAR ';'
	| NORTE INT ';'
	| SUL INT ';'
	| ESTE INT ';'
	| OESTE INT ';'
	;

DIM 	: ('d'|'D')('i'|'I')('m'|'M');
POS 	: ('p'|'P')('o'|'O')('s'|'S');

LIGAR :	('l'|'L')('i'|'I')('g'|'G')('a'|'A')('r'|'R');
DESLIGAR :	('d'|'D')('e'|'E')('s'|'S')('l'|'L')('i'|'I')('g'|'G')('a'|'A')('r'|'R');

NORTE 	: ('n'|'N')('o'|'O')('r'|'R')('t'|'T')('e'|'E');
SUL 	: ('s'|'S')('u'|'U')('l'|'L');
ESTE 	: ('e'|'E')('s'|'S')('t'|'T')('e'|'E');
OESTE 	: ('o'|'O')('e'|'E')('s'|'S')('t'|'T')('e'|'E');

ID  :	('a'..'z'|'A'..'Z'|'_') ('a'..'z'|'A'..'Z'|'0'..'9'|'_')*
    ;

INT :	'0'..'9'+
    ;

COMMENT
    :   '//' ~('\n'|'\r')* '\r'? '\n' {$channel=HIDDEN;}
    |   '/*' ( options {greedy=false;} : . )* '*/' {$channel=HIDDEN;}
    ;

WS  :   ( ' '
        | '\t'
        | '\r'
        | '\n'
        ) {$channel=HIDDEN;}
    ;


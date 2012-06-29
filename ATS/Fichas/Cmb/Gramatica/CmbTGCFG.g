tree grammar CmbTGCFG;

options{
	tokenVocab=Cmb;
	ASTLabelType = CommonTree;
	output = AST;
	backtrack = true;
}



programa returns [Grafo g_out]
@init {
	Grafo g = new Grafo();
}
@after {
	//System.out.println(g);
}
	: 	^(PROGRAMA (funcao[g]
	{
		g = $funcao.g_out;
	}
	)+
	{
		$programa.g_out = g;
	}
	)
	;

funcao [Grafo g_in] returns [Grafo g_out]
	:  ^(FUNCAO cabecalho corpo_funcao[$funcao.g_in])
	{
		$funcao.g_out = $corpo_funcao.g_out;
	}
	;
	
cabecalho
	:  ^(CAEBECALHO tipo ID argumentos?)
	;

argumentos
	:  ^(ARGUMENTOS declaracao+)
	;

corpo_funcao [Grafo g_in] returns [Grafo g_out]
	: ^(CORPO declaracoes statements[$corpo_funcao.g_in, "CORPO_FUNCAO", 0, -1])
	{
		$corpo_funcao.g_out = $statements.g_out;
	}
	;

declaracoes
	: ^(DECLARACOES declaracao+)
	;
	
declaracao
	:	^(DECLARACAO tipo ID)
	;
	
tipo
	:	TD_INT		
	|	TD_BOOL	
	|	TD_STRING
	|	TD_VAZIO
	;
	
statements [Grafo g_in, String contexto, int nr_ultima_instrucao_in, int nr_ultima_instrucao2_in] returns [Grafo g_out, int nr_ultima_instrucao_out, int nr_ultima_instrucao2_out]
@init {
	int nr_ultima_instrucao = $statements.nr_ultima_instrucao_in;
	int nr_ultima_instrucao2 = $statements.nr_ultima_instrucao2_in;
}
	:	 ^(STATEMENTS (statement[$statements.g_in, nr_ultima_instrucao, nr_ultima_instrucao2]
	{
		$statements.g_in = $statement.g_out;
		nr_ultima_instrucao = $statement.nr_ultima_instrucao_out;
		nr_ultima_instrucao2 = $statement.nr_ultima_instrucao2_out;
	}
	)+
	{
		if ($statements.contexto.equals("CORPO_FUNCAO")){
		//TODO No final da execucao do statements em que o contexto (fazer passar isto como atributo) seja "corpo_funcao", ligar o nr_instrucao da ultima instrucao ao noso EXIT
		}
		$statements.g_out = $statement.g_out;
		$statements.nr_ultima_instrucao_out = nr_ultima_instrucao;
		$statements.nr_ultima_instrucao2_out = nr_ultima_instrucao2;
	}
	)
	;
	

statement [Grafo g_in, int nr_ultima_instrucao_in, int nr_ultima_instrucao2_in] returns [Grafo g_out, int nr_ultima_instrucao_out, int nr_ultima_instrucao2_out]
@init {
	Grafo g = $statement.g_in;
}
	:	atribuicao[g] 
		{
			g = $atribuicao.g_out;
			// Se a ultima instrucao tiver sido computada
			if ($statement.nr_ultima_instrucao_in != -1) {
				// liga a instrucao anterior com a nova instrucao
				g.putCaminho($statement.nr_ultima_instrucao_in, $atribuicao.nr_ultima_instrucao_out);
				// Se a ultima instrucao foi um if e tinha um bloco else. Liga o bloco else ao a nova instrucao
				if ($statement.nr_ultima_instrucao2_in != -1) {
					// liga a instrucao anterior ( ultima isntrucao do bloco else) com a nova instrucao
					g.putCaminho($statement.nr_ultima_instrucao2_in, $atribuicao.nr_ultima_instrucao_out);
				}
			}
			
			$statement.g_out = g;
			$statement.nr_ultima_instrucao_out = $atribuicao.nr_ultima_instrucao_out;
			$statement.nr_ultima_instrucao2_out = $atribuicao.nr_ultima_instrucao2_out;
		}
	|	read[g]
		{
			g = $read.g_out;
			// Se a ultima instrucao tiver sido computada
			if ($statement.nr_ultima_instrucao_in != -1) {
				// liga a instrucao anterior com a nova instrucao
				g.putCaminho($statement.nr_ultima_instrucao_in, $read.nr_ultima_instrucao_out);
				// Se a ultima instrucao foi um if e tinha um bloco else. Liga o bloco else ao a nova instrucao
				if ($statement.nr_ultima_instrucao2_in != -1) {
					// liga a instrucao anterior ( ultima isntrucao do bloco else) com a nova instrucao
					g.putCaminho($statement.nr_ultima_instrucao2_in, $read.nr_ultima_instrucao_out);
				}
			}
			
			$statement.g_out = g;
			$statement.nr_ultima_instrucao_out = $read.nr_ultima_instrucao_out;
			$statement.nr_ultima_instrucao2_out = $read.nr_ultima_instrucao2_out;
		}
	|	write[g]
		{
			g = $write.g_out;
			// Se a ultima instrucao tiver sido computada
			if ($statement.nr_ultima_instrucao_in != -1) {
				// liga a instrucao anterior com a nova instrucao
				g.putCaminho($statement.nr_ultima_instrucao_in, $write.nr_ultima_instrucao_out);
				// Se a ultima instrucao foi um if e tinha um bloco else. Liga o bloco else ao a nova instrucao
				if ($statement.nr_ultima_instrucao2_in != -1) {
					// liga a instrucao anterior ( ultima isntrucao do bloco else) com a nova instrucao
					g.putCaminho($statement.nr_ultima_instrucao2_in, $write.nr_ultima_instrucao_out);
				}
			}
			
			$statement.g_out = g;
			$statement.nr_ultima_instrucao_out = $write.nr_ultima_instrucao_out;
			$statement.nr_ultima_instrucao2_out = $write.nr_ultima_instrucao2_out;
		}
	|	ifs[g, $statement.nr_ultima_instrucao_in, $statement.nr_ultima_instrucao2_in]
		{
			g = $ifs.g_out;
			
			$statement.g_out = g;
			$statement.nr_ultima_instrucao_out = $ifs.nr_ultima_instrucao_out;
			$statement.nr_ultima_instrucao2_out = $ifs.nr_ultima_instrucao2_out;
		}
	|	whiles[g, $statement.nr_ultima_instrucao_in, $statement.nr_ultima_instrucao2_in]
		{
			g = $whiles.g_out;
			
			$statement.g_out = g;
			$statement.nr_ultima_instrucao_out = $whiles.nr_ultima_instrucao_out;
			$statement.nr_ultima_instrucao2_out = $whiles.nr_ultima_instrucao2_out;
		}
	|	invocacao
	|	retorna
	;
	
retorna returns [String instrucao]
	:  ^(RETURN expr)
	{
		$retorna.instrucao = $RETURN.text + $expr.instrucao;
	}
	;

invocacao returns [String instrucao]
	:  ^(INVOCACAO ID args?)
	{
		$invocacao.instrucao = $ID.text + "(" + $args.ags + ")";
	}
	;

args returns [String ags]
@init{
	String a = "";
}
	:  ^(ARGS (expr
	{
		a += $expr.instrucao + ", ";
	}
	)+
	{$args.ags = a;}
	)
	;

atribuicao [Grafo g_in] returns [Grafo g_out, int nr_ultima_instrucao_out, int nr_ultima_instrucao2_out]
@init {
	Grafo g = $atribuicao.g_in;
}
	:	 ^('=' ID expr)
	{
		// cria nodo no grafo e guarda o nr da instrucao
		$atribuicao.nr_ultima_instrucao_out = g.putNodo(new Instrucao($ID.text + " = " + $expr.instrucao, null, null));
		$atribuicao.nr_ultima_instrucao2_out = -1;
		$atribuicao.g_out = g;
	}
	;

write [Grafo g_in] returns [Grafo g_out, int nr_ultima_instrucao_out, int nr_ultima_instrucao2_out]
@init {
	Grafo g = $write.g_in;
}
	: ^(WRITE expr)
	{
		// cria nodo no grafo e guarda o nr da instrucao
		$write.nr_ultima_instrucao_out = g.putNodo(new Instrucao($WRITE.text + "(" + $expr.instrucao + ")", null, null));
		$write.nr_ultima_instrucao2_out = -1;
		$write.g_out = g;
	}
	;
	
read [Grafo g_in] returns [Grafo g_out, int nr_ultima_instrucao_out, int nr_ultima_instrucao2_out]
@init {
	Grafo g = $read.g_in;
}
	: ^(READ ID)
	{
		// cria nodo no grafo e guarda o nr da instrucao
		$read.nr_ultima_instrucao_out = g.putNodo(new Instrucao($READ.text + "(" + $ID.text + ")", null, null));
		$read.nr_ultima_instrucao2_out = -1;
		$read.g_out = g;
	}
	;
	
	
ifs [Grafo g_in, int nr_ultima_instrucao_in, int nr_ultima_instrucao2_in] returns [Grafo g_out, int nr_ultima_instrucao_out, int nr_ultima_instrucao2_out]
@init {
	Grafo g = $ifs.g_in;
	int nr_ultima_instrucao2 = -1;
	int nr_ult_inst_exp = -1;
	// TODO o nr_ultima_instrucao pode ser mais que dois devido a if's aninhados. ponderar uso de array de nr_ultimo_isntrucao
}
	:	^(IF expr 
			{
				// cria nodo no grafo e guarda o nr da instrucao
				nr_ult_inst_exp = g.putNodo(new Instrucao($IF.text + "(" + $expr.instrucao + ")", null, null));
				
				// Se a ultima instrucao tiver sido computada
				if ($ifs.nr_ultima_instrucao_in != -1) {
					// liga a instrucao anterior com a nova instrucao
					g.putCaminho($ifs.nr_ultima_instrucao_in, nr_ult_inst_exp);
					// Se a ultima instrucao foi um if e tinha um bloco else. Liga o bloco else ao a nova instrucao
					if ($ifs.nr_ultima_instrucao2_in != -1) {
						// liga a instrucao anterior ( ultima isntrucao do bloco else) com a nova instrucao
						g.putCaminho($ifs.nr_ultima_instrucao2_in, nr_ult_inst_exp);
					}
				}
			}
			a=bloco[g, nr_ult_inst_exp, -1] 
			{
				g = $a.g_out; 
			} 
			(b=bloco[g, nr_ult_inst_exp, -1] 
			{ 
				g = $b.g_out; 
				nr_ultima_instrucao2 = $b.nr_ultima_instrucao_out; 
			} )?
		)
		{
			$ifs.nr_ultima_instrucao_out = $a.nr_ultima_instrucao_out;
			$ifs.nr_ultima_instrucao2_out = nr_ultima_instrucao2;
			$ifs.g_out = g;
		}
	;
	
whiles [Grafo g_in, int nr_ultima_instrucao_in, int nr_ultima_instrucao2_in] returns [Grafo g_out, int nr_ultima_instrucao_out, int nr_ultima_instrucao2_out]
@init {
	Grafo g = $whiles.g_in;
	int nr_ult_inst_exp = -1;
	// TODO o nr_ultima_instrucao pode ser mais que dois devido a if's aninhados. ponderar uso de array de nr_ultimo_isntrucao
}
	:	 ^(WHILE expr
			{
				// cria nodo no grafo e guarda o nr da instrucao
				nr_ult_inst_exp = g.putNodo(new Instrucao($WHILE.text + "(" + $expr.instrucao + ")", null, null));
				
				// Se a ultima instrucao tiver sido computada
				if ($whiles.nr_ultima_instrucao_in != -1) {
					// liga a instrucao anterior com a nova instrucao
					g.putCaminho($whiles.nr_ultima_instrucao_in, nr_ult_inst_exp);
					// Se a ultima instrucao foi um if e tinha um bloco else. Liga o bloco else ao a nova instrucao
					if ($whiles.nr_ultima_instrucao2_in != -1) {
						// liga a instrucao anterior ( ultima isntrucao do bloco else) com a nova instrucao
						g.putCaminho($whiles.nr_ultima_instrucao2_in, nr_ult_inst_exp);
					}
				}
			}
	 		bloco[g, nr_ult_inst_exp, -1] { g = $bloco.g_out; })
		 	{
				// Se a ultima instrucao do bloco tiver sido computada
				if ($bloco.nr_ultima_instrucao_out != -1) {
					// liga a ultima instrucao do bloco com a intrucao inicial do while, ou seja a expressao
					g.putCaminho($bloco.nr_ultima_instrucao_out, nr_ult_inst_exp);
					// Se a ultima instrucao do bloco foi um if e tinha um bloco else. Liga o bloco else a intrucao inicial do while, ou seja a expressao
					if ($bloco.nr_ultima_instrucao2_out != -1) {
						// liga a instrucao anterior ( ultima isntrucao do bloco else) com a intrucao inicial do while, ou seja a expressao
						g.putCaminho($bloco.nr_ultima_instrucao2_out, nr_ult_inst_exp);
					}
				}		 	
		 	
		 		// é passado o nr da intrucao inicial do while, ou seja a expressao, para que  proximo statement se ligue a este
				$whiles.nr_ultima_instrucao_out = nr_ult_inst_exp;
				$whiles.nr_ultima_instrucao2_out = -2;
				$whiles.g_out = g;
			}
	;

bloco [Grafo g_in, int nr_ultima_instrucao_in, int nr_ultima_instrucao2_in] returns [Grafo g_out, int nr_ultima_instrucao_out, int nr_ultima_instrucao2_out]
	:	statements[$bloco.g_in, "BLOCO", $bloco.nr_ultima_instrucao_in, $bloco.nr_ultima_instrucao2_in]
	{
		$bloco.g_out = $statements.g_out;
		$bloco.nr_ultima_instrucao_out = $statements.nr_ultima_instrucao_out;
		$bloco.nr_ultima_instrucao2_out = $statements.nr_ultima_instrucao2_out;
	}
//	|	^(STATEMENTS statement)
	;
	
expr returns [String instrucao]
	:	^('||' a=expr b=expr) 	{$expr.instrucao = $a.instrucao + "||" 	+ $b.instrucao;}
	|	^('&&' a=expr b=expr) 	{$expr.instrucao = $a.instrucao + "&&" 	+ $b.instrucao;}
	|	^('+' a=expr b=expr) 	{$expr.instrucao = $a.instrucao + "+" 	+ $b.instrucao;}
	|	^('-' a=expr b=expr) 	{$expr.instrucao = $a.instrucao + "-" 	+ $b.instrucao;}
	|	^('*' a=expr b=expr) 	{$expr.instrucao = $a.instrucao + "*" 	+ $b.instrucao;}
	|	^('/' a=expr b=expr) 	{$expr.instrucao = $a.instrucao + "/" 	+ $b.instrucao;}
	//|	^('%' a=expr b=expr) 	{$expr.instrucao = $a.instrucao + "%" 	+ $b.instrucao;}
	|	^('>' a=expr b=expr) 	{$expr.instrucao = $a.instrucao + ">" 	+ $b.instrucao;}
	|	^('<' a=expr b=expr) 	{$expr.instrucao = $a.instrucao + "<" 	+ $b.instrucao;}
	|	^('>=' a=expr b=expr) 	{$expr.instrucao = $a.instrucao + ">=" 	+ $b.instrucao;}
	| 	^('<=' a=expr b=expr) 	{$expr.instrucao = $a.instrucao + "<=" 	+ $b.instrucao;}
	|	^('==' a=expr b=expr) 	{$expr.instrucao = $a.instrucao + "==" 	+ $b.instrucao;}
	|	^('!=' a=expr b=expr) 	{$expr.instrucao = $a.instrucao + "!=" 	+ $b.instrucao;}
	|	^('!' a=expr) 			{$expr.instrucao = "!" + $a.instrucao;}
	|	factor 					{$expr.instrucao = $factor.instrucao;}
	;
	
factor returns [String instrucao]
	:	ID 		{$factor.instrucao = $ID.text;}
	| constante	{$factor.instrucao = $constante.valor;}
	| invocacao	{$factor.instrucao = $invocacao.instrucao;}
	;
	
constante returns [String valor]
	:	STRING	{$constante.valor = $STRING.text;}
	|	INT		{$constante.valor = $INT.text;}
	|	TRUE	{$constante.valor = $TRUE.text;}
	|	FALSE	{$constante.valor = $FALSE.text;}
	;
	
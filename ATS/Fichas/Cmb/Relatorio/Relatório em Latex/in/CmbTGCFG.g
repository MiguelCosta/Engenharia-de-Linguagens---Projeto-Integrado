tree grammar CmbTGCFG;

options{
	tokenVocab=Cmb;
	ASTLabelType = CommonTree;
	output = AST;
	backtrack = true;
}

@header{
	import java.util.TreeSet;
	import java.util.TreeMap;
}

programa returns [TreeMap<String, Grafo> grafos_out]
@init {
	TreeMap<String, Grafo> grafos = new TreeMap<String, Grafo>();
}
	: 	^(PROGRAMA (funcao[new Grafo()]
	{
		grafos.put($funcao.func_id, $funcao.g_out);
	}
	)+
	{
		$programa.grafos_out = grafos;
	}
	)
	;

funcao [Grafo g_in] returns [Grafo g_out, String func_id]
	:  ^(FUNCAO cabecalho corpo_funcao[$funcao.g_in, $cabecalho.id])
	{
		$funcao.g_out = $corpo_funcao.g_out;
		$funcao.func_id = $cabecalho.id;
	}
	;
	
cabecalho returns [String id]
	:  ^(CAEBECALHO tipo ID argumentos?)
	{
		$cabecalho.id = $ID.text;	
	}
	;

argumentos
	:  ^(ARGUMENTOS declaracao+)
	;

corpo_funcao [Grafo g_in, String id_funcao] returns [Grafo g_out]
@init{
	Grafo g = $corpo_funcao.g_in;
	TreeSet<Integer> nrs = new TreeSet<Integer>();
	// 0 <=> Nodo START. E passado como parametro para que o nodo START se ligue a primeira instrucao
	int nr = g.putNodo(0, new Instrucao("START", null, null));
	nrs.add(nr);
}
	: ^(CORPO declaracoes statements[$corpo_funcao.g_in, "CORPO_FUNCAO", nrs, ""])
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
	
statements [Grafo g_in, String contexto, TreeSet<Integer> nrs_ultima_instrucao_in, String label_in] returns [Grafo g_out, TreeSet<Integer> nrs_ultima_instrucao_out, String label_out]
@init {
	Grafo g = $statements.g_in;
	TreeSet<Integer> nrs_ultima_instrucao = $statements.nrs_ultima_instrucao_in;
}
	:	 ^(STATEMENTS (statement[g, nrs_ultima_instrucao, $statements.label_in]
	{
		g = $statement.g_out;
		nrs_ultima_instrucao = $statement.nrs_ultima_instrucao_out;
		$statements.label_in = $statement.label_out;
	}
	)+
	{
		// Apos todos os statements do corpo de uma funcao tiverem sido executados, os ultimos nodos sao ligados ao nodo EXIT
		if ($statements.contexto.equals("CORPO_FUNCAO")){
			// cria nodo EXIT
			int nodo_exit = g.putNodo(new Instrucao("EXIT", null, null));
			// verifica se existem instrucoes anteriormente executadas e conecta essas instrucoes ao nodo EXIT
			g.checkAndPutCaminho(nrs_ultima_instrucao, new ParNrInstrucaoLabel(nodo_exit, $statement.label_out));
		}
		
		$statements.g_out = g;
		$statements.nrs_ultima_instrucao_out = nrs_ultima_instrucao;
		$statements.label_out = $statement.label_out;
	}
	)
	;
	

statement [Grafo g_in, TreeSet<Integer> nrs_ultima_instrucao_in, String label_in] returns [Grafo g_out, TreeSet<Integer> nrs_ultima_instrucao_out, String label_out]
@init {
	Grafo g = $statement.g_in;
}
	:	atribuicao[g, $statement.label_in] 
		{
			g = $atribuicao.g_out;

			// verifica se existem instrucoes anteriormente executadas e conecta essas instrucoes a nova instrucao
			g.checkAndPutCaminho($statement.nrs_ultima_instrucao_in, new ParNrInstrucaoLabel($atribuicao.nrs_ultima_instrucao_out.first(), $statement.label_in));
			
			$statement.g_out = g;
			$statement.nrs_ultima_instrucao_out = $atribuicao.nrs_ultima_instrucao_out;
			$statement.label_out = $atribuicao.label_out;
		}
	|	read[g, $statement.label_in]
		{
			g = $read.g_out;
			
			// verifica se existem instrucoes anteriormente executadas e conecta essas instrucoes a nova instrucao
			g.checkAndPutCaminho($statement.nrs_ultima_instrucao_in, new ParNrInstrucaoLabel($read.nrs_ultima_instrucao_out.first(), $statement.label_in));
			
			$statement.g_out = g;
			$statement.nrs_ultima_instrucao_out = $read.nrs_ultima_instrucao_out;
			$statement.label_out = $read.label_out;
		}
	|	write[g, $statement.label_in]
		{
			g = $write.g_out;
			
			// verifica se existem instrucoes anteriormente executadas e conecta essas instrucoes a nova instrucao
			g.checkAndPutCaminho($statement.nrs_ultima_instrucao_in, new ParNrInstrucaoLabel($write.nrs_ultima_instrucao_out.first(), $statement.label_in));
			
			$statement.g_out = g;
			$statement.nrs_ultima_instrucao_out = $write.nrs_ultima_instrucao_out;
			$statement.label_out = $write.label_out;
		}
	|	ifs[g, $statement.nrs_ultima_instrucao_in, $statement.label_in]
		{
			g = $ifs.g_out;
			
			$statement.g_out = g;
			$statement.nrs_ultima_instrucao_out = $ifs.nrs_ultima_instrucao_out;
			$statement.label_out = $ifs.label_out;
		}
	|	whiles[g, $statement.nrs_ultima_instrucao_in, $statement.label_in]
		{
			g = $whiles.g_out;
			
			$statement.g_out = g;
			$statement.nrs_ultima_instrucao_out = $whiles.nrs_ultima_instrucao_out;
			$statement.label_out = $whiles.label_out;
		}
	|	invocacao[g, "STATEMENT", $statement.label_in]
		{
			g = $invocacao.g_out;
			
			// verifica se existem instrucoes anteriormente executadas e conecta essas instrucoes a nova instrucao
			g.checkAndPutCaminho($statement.nrs_ultima_instrucao_in, new ParNrInstrucaoLabel($invocacao.nrs_ultima_instrucao_out.first(), $statement.label_in));

			$statement.g_out = g;
			$statement.nrs_ultima_instrucao_out = $invocacao.nrs_ultima_instrucao_out;
			$statement.label_out = $invocacao.label_out;
		}
	|	retorna[g, $statement.label_in]
		{
			g = $retorna.g_out;
			
			// verifica se existem instrucoes anteriormente executadas e conecta essas instrucoes a nova instrucao
			g.checkAndPutCaminho($statement.nrs_ultima_instrucao_in, new ParNrInstrucaoLabel($retorna.nrs_ultima_instrucao_out.first(), $statement.label_in));
			
			$statement.g_out = g;
			$statement.nrs_ultima_instrucao_out = $retorna.nrs_ultima_instrucao_out;
			$statement.label_out = $retorna.label_out;
		}
	;
	
retorna [Grafo g_in, String label_in] returns [Grafo g_out, TreeSet<Integer> nrs_ultima_instrucao_out, String label_out]
@init {
	Grafo g = $retorna.g_in;
}
	:  ^(RETURN expr)
	{
		TreeSet<Integer> nrs = new TreeSet<Integer>();
		// cria nodo no grafo e guarda o nr da instrucao
		nrs.add(g.putNodo(new Instrucao($RETURN.text + " " + $expr.instrucao, null, null)));
		
		$retorna.nrs_ultima_instrucao_out = nrs;
		$retorna.g_out = g;
		$retorna.label_out = "";
	}
	;

invocacao [Grafo g_in, String contexto, String label_in] returns [Grafo g_out, TreeSet<Integer> nrs_ultima_instrucao_out, String instrucao, String label_out]
@init {
	Grafo g = $invocacao.g_in;
}
	:  ^(INVOCACAO ID args?)
	{
		if ($invocacao.contexto.equals("FACTOR")) {
			$invocacao.instrucao = $ID.text + "(" + $args.ags + ")";
		}
		else {
			TreeSet<Integer> nrs = new TreeSet<Integer>();
			// cria nodo no grafo e guarda o nr da instrucao
			nrs.add(g.putNodo(new Instrucao($ID.text + "(" + $args.ags + ")", null, null)));
			
			$invocacao.nrs_ultima_instrucao_out = nrs;
			$invocacao.g_out = g;
			$invocacao.label_out = "";
		}
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
	{
		// limpa os caracteres finais ", "
		$args.ags = a.substring(0,a.length()-2);
	}
	)
	;

atribuicao [Grafo g_in, String label_in] returns [Grafo g_out, TreeSet<Integer> nrs_ultima_instrucao_out, String label_out]
@init {
	Grafo g = $atribuicao.g_in;
}
	:	 ^('=' ID expr)
	{
		TreeSet<Integer> nrs = new TreeSet<Integer>();
		// cria nodo no grafo e guarda o nr da instrucao
		nrs.add(g.putNodo(new Instrucao($ID.text + " = " + $expr.instrucao, null, null)));
		
		$atribuicao.nrs_ultima_instrucao_out = nrs;
		$atribuicao.g_out = g;
		$atribuicao.label_out = "";
	}
	;

write [Grafo g_in, String label_in] returns [Grafo g_out, TreeSet<Integer> nrs_ultima_instrucao_out, String label_out]
@init {
	Grafo g = $write.g_in;
}
	: ^(WRITE expr)
	{
		TreeSet<Integer> nrs = new TreeSet<Integer>();
		// cria nodo no grafo e guarda o nr da instrucao
		nrs.add(g.putNodo(new Instrucao($WRITE.text + "(" + $expr.instrucao + ")", null, null)));
		
		$write.nrs_ultima_instrucao_out = nrs;
		$write.g_out = g;
		$write.label_out = "";
	}
	;
	
read [Grafo g_in, String label_in] returns [Grafo g_out, TreeSet<Integer> nrs_ultima_instrucao_out, String label_out]
@init {
	Grafo g = $read.g_in;
}
	: ^(READ ID)
	{
		TreeSet<Integer> nrs = new TreeSet<Integer>();
		// cria nodo no grafo e guarda o nr da instrucao
		nrs.add(g.putNodo(new Instrucao($READ.text + "(" + $ID.text + ")", null, null)));
		$read.nrs_ultima_instrucao_out = nrs;
		$read.g_out = g;
		$read.label_out = "";
	}
	;
	
	
ifs [Grafo g_in, TreeSet<Integer> nrs_ultima_instrucao_in, String label_in] returns [Grafo g_out, TreeSet<Integer> nrs_ultima_instrucao_out, String label_out]
@init {
	Grafo g = $ifs.g_in;
	int nr_ult_inst_exp = -1;
	TreeSet<Integer> nrs_exp = new TreeSet<Integer>();
	TreeSet<Integer> nrs_out = new TreeSet<Integer>();
//	boolean entrou_else = false;
}
	:	^(IF expr 
			{
				// cria nodo no grafo e guarda o nr da instrucao
				nr_ult_inst_exp = g.putNodo(new Instrucao($IF.text + "(" + $expr.instrucao + ")", null, null));
				
				// verifica se existem instrucoes anteriormente executadas e conecta essas instrucoes a nova instrucao (expressao)
				g.checkAndPutCaminho($ifs.nrs_ultima_instrucao_in, new ParNrInstrucaoLabel(nr_ult_inst_exp, $ifs.label_in));
				
				// variavel que sera passada aos blocos para indicar o nodo que sera ligado a primeira instrucao de cada bloco
				nrs_exp.add(nr_ult_inst_exp);
				
				// adiciona provisoriamente o nr da expressao. caso exista o bloco else, este E removido ja que a instrucao seguinte, ligar-se-á a última instrucao dos blocos then e else
				// caso contrario, ligar-se-á a expressão e ao bloco then.
				nrs_out.add(nr_ult_inst_exp);
			}
			a=bloco[g, nrs_exp, "T"] 
			{
				g = $a.g_out; 
				// adiciona os nrs das utlimas instrucoes deste bloco
				nrs_out.addAll($a.nrs_ultima_instrucao_out);
			} 
			(b=bloco[g, nrs_exp, "F"] 
			{ 
				g = $b.g_out; 
				// remove o nr da expressao adicionado provisoriamente
				nrs_out.remove(nr_ult_inst_exp);
				// adiciona os nrs das utlimas instrucoes deste bloco
				nrs_out.addAll($b.nrs_ultima_instrucao_out); 
			//	entrou_else = true;
			} )?
		)
		{
			$ifs.nrs_ultima_instrucao_out = nrs_out;
			$ifs.g_out = g;
			//if (entrou_else) 
			$ifs.label_out = "";
		//	else $ifs.label_out = "F";
			
		}
	;
	
whiles [Grafo g_in, TreeSet<Integer> nrs_ultima_instrucao_in, String label_in] returns [Grafo g_out, TreeSet<Integer> nrs_ultima_instrucao_out, String label_out]
@init {
	Grafo g = $whiles.g_in;
	int nr_ult_inst_exp = -1;
	TreeSet<Integer> nrs_exp = new TreeSet<Integer>();
}
	:	 ^(WHILE expr
		{
			// cria nodo no grafo e guarda o nr da instrucao
			nr_ult_inst_exp = g.putNodo(new Instrucao($WHILE.text + "(" + $expr.instrucao + ")", null, null));
			
			// verifica se existem instrucoes anteriormente executadas e conecta essas instrucoes a nova instrucao (expressao)
			g.checkAndPutCaminho($whiles.nrs_ultima_instrucao_in, new ParNrInstrucaoLabel(nr_ult_inst_exp, $whiles.label_in));
			
			// variavel que sera passada ao bloco para indicar o nodo que sera ligado a primeira instrucao do bloco
			nrs_exp.add(nr_ult_inst_exp);
		}
	 	bloco[g, nrs_exp, "T"] { g = $bloco.g_out; })
		{
			// verifica se existem instrucoes anteriormente executadas no bloco e conecta essas instrucoes a instrucao (expressao)
			g.checkAndPutCaminho($bloco.nrs_ultima_instrucao_out, new ParNrInstrucaoLabel(nr_ult_inst_exp, $bloco.label_out));
			
			// E passado o nr da instrucao inicial do while, ou seja a expressao, para que  proximo statement se ligue a este
			$whiles.nrs_ultima_instrucao_out = nrs_exp;
			$whiles.g_out = g;
			// devolve "F" porque a proxima instrucao so acontece quando a expressao do while der falso
			$whiles.label_out = "F";
		}
	;

bloco [Grafo g_in, TreeSet<Integer> nrs_ultima_instrucao_in, String label_in] returns [Grafo g_out, TreeSet<Integer> nrs_ultima_instrucao_out, String label_out]
	:	statements[$bloco.g_in, "BLOCO", $bloco.nrs_ultima_instrucao_in, $bloco.label_in]
	{
		$bloco.g_out = $statements.g_out;
		$bloco.nrs_ultima_instrucao_out = $statements.nrs_ultima_instrucao_out;
		$bloco.label_out = $statements.label_out;
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
	|	^(\'%' a=expr b=expr) 	{$expr.instrucao = $a.instrucao + "\%" 	+ $b.instrucao;}
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
	:	ID 						{$factor.instrucao = $ID.text;}
	| constante					{$factor.instrucao = $constante.valor;}
	| invocacao[null, "FACTOR", ""]	{$factor.instrucao = $invocacao.instrucao;}
	;
	
constante returns [String valor]
	:	STRING	{$constante.valor = $STRING.text;}
	|	INT		{$constante.valor = $INT.text;}
	|	TRUE	{$constante.valor = $TRUE.text;}
	|	FALSE	{$constante.valor = $FALSE.text;}
	;
	

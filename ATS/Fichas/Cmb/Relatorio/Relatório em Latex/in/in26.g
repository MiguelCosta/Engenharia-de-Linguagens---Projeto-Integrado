atribuicao [GrafoTGSSA g_in, String label_in, String contexto_in] returns [GrafoTGSSA g_out, TreeSet<Integer> nrs_ultima_instrucao_out, String label_out]
@init {
	GrafoTGSSA g = $atribuicao.g_in;
}
	:	 ^('=' ID expr[g, $atribuicao.contexto_in])
	{
		TreeSet<Integer> nrs = new TreeSet<Integer>();
		// cria nodo no grafo e guarda o nr da instrucao
		Integer i_id = $atribuicao.g_in.getVersaoVariavelNext($ID.text);
		nrs.add(g.putNodo(new Instrucao($ID.text + " = " + $expr.instrucao, null, null, "", "", $ID.text + i_id + " = " + $expr.instrucaoVersao)));
		
		//como ha uma atribuicao, a versao da variavel vai ter de ser incrementada
		Integer i = g.incrementaVariavel($ID.text);
		
		//vai adicionar a variavel e dizer qual e o contexto onde se encontra
		g.adicionaVariavelContexto($ID.text + i, $atribuicao.contexto_in);
		//System.out.println(g_in.getContextoVariaveis());
		
		...
	}
	;



read [GrafoTGSSA g_in, String label_in, String contexto_in] returns [GrafoTGSSA g_out, TreeSet<Integer> nrs_ultima_instrucao_out, String label_out]
@init {
	GrafoTGSSA g = $read.g_in;
}
	: ^(READ ID)
	{
		TreeSet<Integer> nrs = new TreeSet<Integer>();
		// cria nodo no grafo e guarda o nr da instrucao
		nrs.add(g.putNodo(new Instrucao($READ.text + "(" + $ID.text + ")", null, null)));
		
		//como ha uma atribuicao, a versao da variavel vai ter de ser incrementada
		Integer i = g.incrementaVariavel($ID.text);
		
		// vai adicionar a variavel e dizer qual e o contexto onde se encontra
		g.adicionaVariavelContexto($ID.text + i, $read.contexto_in);
		...
	}
	;
	

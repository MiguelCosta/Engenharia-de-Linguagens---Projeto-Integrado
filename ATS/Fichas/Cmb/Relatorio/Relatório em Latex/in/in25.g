//  caso exista vai adicionar uma invocacao a uma funcao
if($expr.cFuncao_out != null){
	$expr.cFuncao_out.setNrInstrucao(nr_ult_inst_exp);
	g.putChamadaFuncao(nr_ult_inst_exp, $expr.cFuncao_out);
}

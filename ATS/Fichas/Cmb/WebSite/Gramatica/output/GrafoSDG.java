import java.util.TreeMap;
import java.util.TreeSet;

public class GrafoSDG extends GrafoPDG {
	
	// número da instrucao, informacao das chamadas de funcao, vai dizer quais sao os parametros aqui e
	// como sabemos qual é a instrucao, vamos ver qual a variavel que vai ficar com o valor de retorno
	private TreeMap<Integer, ChamadasFuncao> chamadasFuncao;
	// aqui vai conter todos os cabecalhos das funcoes, todos os parametros que revebe
	// e o tipo de valor que retorna
	private TreeMap<String, CabecalhoFuncao> funcoes;
	
	

	public GrafoSDG() {
		super();
		this.chamadasFuncao = new TreeMap<Integer, ChamadasFuncao>();
		this.funcoes = new TreeMap<String, CabecalhoFuncao>();
	}
	
	public GrafoSDG(Grafo grafo) {
		super(grafo);
		this.chamadasFuncao = new TreeMap<Integer, ChamadasFuncao>();
		this.funcoes = new TreeMap<String, CabecalhoFuncao>();
	}
	
	/**
	 * @param dependencias_dados
	 */
	public GrafoSDG(TreeMap<Integer, TreeSet<Integer>> dependencias_dados) {
		super();
		this.chamadasFuncao = new TreeMap<Integer, ChamadasFuncao>();
		this.funcoes = new TreeMap<String, CabecalhoFuncao>();
	}

	public TreeMap<Integer, ChamadasFuncao> getChamadasFuncao() {
		return chamadasFuncao;
	}

	public void setChamadasFuncao(TreeMap<Integer, ChamadasFuncao> chamadasFuncao) {
		this.chamadasFuncao = chamadasFuncao;
	}

	public TreeMap<String, CabecalhoFuncao> getFuncoes() {
		return funcoes;
	}

	public void setFuncoes(TreeMap<String, CabecalhoFuncao> funcoes) {
		this.funcoes = funcoes;
	}
	
	public void putChamadaFuncao(Integer i, ChamadasFuncao cf) {
		chamadasFuncao.put(i, cf);
	}
	
	public void putCabecalhoFuncao(CabecalhoFuncao cf) {
		funcoes.put(cf.getNomeFuncao(), cf);
	}
	
	@Override
	public String toString() {
		String c = "caminhos={";
		
		for (int nr : super.getCaminhos().keySet()) {
			c += "\n\t"+nr+"="+super.getCaminhos().get(nr)+",";
		}
		c += "\n}";
		
		return "Grafo [\n\t" +
				"nodos=" + super.getNodos() + ",\n\n" +
				c + "\n\n" +
//				"dependencias_dados=" + dependencias_dados + "\n" +
				"cabecalhos=" + funcoes + "\n" +
				"chamadasFuncoes=" + chamadasFuncao + "\n" +
			"]";
	}
	
	
}

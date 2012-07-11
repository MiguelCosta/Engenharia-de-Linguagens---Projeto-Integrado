import java.util.TreeMap;
import java.util.TreeSet;
import java.io.*;

public class GrafoTGSSA extends Grafo {

	// variável => versão
	private TreeMap<String, Integer> versoesVariaveis;
	// onde está guardado o contexto onde as versoes das variaveis foram encontradas
	// a1 => If_THEN; a2 => IF_ELSE
	private TreeMap<String, String>  contextoVariaveis;

	public GrafoTGSSA() {
		super();
		this.versoesVariaveis = new TreeMap<String, Integer>();
		this.contextoVariaveis	= new TreeMap<String, String>();
	}
	
	public GrafoTGSSA(Grafo grafo) {
		super(grafo);
		this.versoesVariaveis = new TreeMap<String, Integer>();
		this.contextoVariaveis	= new TreeMap<String, String>();
	}
	
	/**
	 * @param versoesVariaveis
	 */
	public GrafoTGSSA(TreeMap<String, Integer> versoesVariaveis, TreeMap<String, String> contextoVariaveis) {
		super();
		this.versoesVariaveis = versoesVariaveis;
		this.contextoVariaveis = contextoVariaveis;
	}


	public Integer getVersaoVariavel(String v){
		if (versoesVariaveis.containsKey(v)) {
			return versoesVariaveis.get(v);
		} 
		else {
			versoesVariaveis.put(v, 0);
			return 0;
		}
	}
	
	public Integer getVersaoVariavelNext(String v){
		if (versoesVariaveis.containsKey(v)) {
			return versoesVariaveis.get(v) + 1;
		} 
		else {
			return 0;
		}
	}
	
	public Integer incrementaVariavel(String v){
		if (versoesVariaveis.containsKey(v)) {
			//System.out.println("Valor antes de incrementar "+ v +": " + versoesVariaveis.get(v));
			putVariavel(v, versoesVariaveis.get(v) + 1);
		} 
		else {
			//System.out.println("Valor antes de incrementar "+ v +": sem valor");
			putVariavel(v);
		}
		//System.out.println("Valor Depois de incrementar "+ v + ": " +versoesVariaveis.get(v));
		return versoesVariaveis.get(v);
	}
	
	public void putVariavel(String v){
		versoesVariaveis.put(v, 0);
	}
	
	public void putVariavel(String v, Integer i){
		versoesVariaveis.put(v, i);
	}
 
 
	public void adicionaVariavelContexto(String v, String contexto){
		contextoVariaveis.put(v, contexto);
		//System.out.println("Foi adicionado: " + v + " | " +contexto);
	}
	

	public String getVersaoVariavelDependentes(String v){
		String res = "";
		
		if (versoesVariaveis.containsKey(v)) {
			
			Integer i = versoesVariaveis.get(v);
			
			String vTmp = v+i;
			
			String cont = contextoVariaveis.get(vTmp);
			
			while(cont !=null && !cont.equals("") && i >= 0){
				vTmp = v+i;
				
				// para algo no ELSE não depender do THEN
				if(!(cont.equals("IF_ELSE") && contextoVariaveis.get(vTmp).equals("IF_THEN"))){
					
					res += vTmp + ", ";
				}
				
				cont = contextoVariaveis.get(vTmp);
				
				i--;
			}
			
			if (res.length()-2 > 0){
				return "F(" + res.substring(0, res.length()-2) + ")";
			} else {
				return v + versoesVariaveis.get(v);
			}
			
		} 
		else {
			return v+"0";
		}
	}



	/**
	 * @return the dependencias_dados
	 */
	public TreeMap<String, Integer> getVersoesVariaveis() {
		return versoesVariaveis;
	}
	
	public TreeMap<String, String> getContextoVariaveis() {
		return contextoVariaveis;
	}
 
	/**
	 * @param dependencias_dados the dependencias_dados to set
	 */
	public void setVersoesVariaveis(
			TreeMap<String, Integer> versoesVariaveis) {
		this.versoesVariaveis = versoesVariaveis;
	}
	

	@Override
	public String toString() {
		this.toDot();
		
		return "Grafo [\n\t" +
				"nodos=" + super.getNodos() + ",\n\n" +
				"caminhos=" + super.getCaminhos() + "\n\n" +
				"versoesVariaveis=" + versoesVariaveis + "\n" +
			"]";
			//return versoesVariaveis + "\n";
	}
	
	public void toDot(){
		/*String s = "digraph mainmapSSA {\n     \n";
		// graph [bgcolor=transparent];
		TreeMap<Integer, TreeSet<Integer>> caminhos = super.getCaminhos();
		TreeMap<Integer, Instrucao> nodos = super.getNodos();
		
		for(Integer n : nodos.keySet()){
			s+= '"' + nodos.get(n).getInstrucaoDot() + '"' + "[label=" + '"' +nodos.get(n).getInstrucaoVersaoDot() + '"' +"];\n";
		}
		
		for(Integer o : caminhos.keySet()){
			for(Integer d : caminhos.get(o)){
				
				s+= '"' + nodos.get(o).getInstrucaoDot() + '"' + " -> " + '"' + nodos.get(d).getInstrucaoDot() + '"' +  ";\n";
			}
		}
		
		s+= "}";
		try{
		FileWriter fstream = new FileWriter("ssa.gv");
		BufferedWriter out = new BufferedWriter(fstream);
		out.write(s);
		out.close();
		}catch (Exception e){//Catch exception if any
			  System.err.println("Error: " + e.getMessage());
		 }*/
		
	}
	
	
	
}

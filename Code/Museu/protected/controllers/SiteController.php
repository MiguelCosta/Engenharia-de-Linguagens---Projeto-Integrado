<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
				// captcha action renders the CAPTCHA image displayed on the contact page
				'captcha'=>array(
						'class'=>'CCaptchaAction',
						'backColor'=>0xFFFFFF,
				),
				// page action renders "static" pages stored under 'protected/views/site/pages'
				// They can be accessed via: index.php?r=site/page&view=FileName
				'page'=>array(
						'class'=>'CViewAction',
				),
				'sala'=>array(
						'class'=>'CViewAction',
				),
				'search'=>array(
						'class'=>'CViewAction',
				),
		);
	}

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
				'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
				array('allow', // allow admin user to perform 'admin' and 'delete' actions
						'actions'=>array('novaSala'),
						'users'=>array('@', 'admin', 'museu'),
				),
				array('allow',  // deny all users
						'users'=>array('*'),
				),
		);
	}


	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	public function actionSearch()
	{
		$this->render('search');
	}

	/**
	 * Cria uma nova sala de uma exposicao
	 */
	public function actionNovaSala()
	{
		$session=new CHttpSession;
		$session->open();

		$model = new NovaSalaForm;

		if(isset($_POST['NovaSalaForm']))
		{
			$model->attributes=$_POST['NovaSalaForm'];
			$model->image=CUploadedFile::getInstance($model,'image');

			// se a sala for valida segundo o schema e o conteudo (nao testa os argumentos)
			if($model->validate()) {
				// obtem o ultimo id da tabela Exhibitions
				$last_id_room = Yii::app()->db->createCommand()
				->select('max(id_room) as max')
				->from('Rooms')
				->queryScalar();


				// se uma imagem tiver sido carregada entao a imagem é guardada no repositorio e algumas variaveis sao preenchidas
				if ($model->image != NULL){
					// nome do ficheiro passa a ser o id da exibicao a ser criada
					$model->image_path = ($last_id_room+1).".jpg";

					// variavel de sessao que guarda a informacao do image_path e que será acedida no create
					$_SESSION['image_path'] = $model->image_path;

					// guarda imagem no repositorio
					// Yii::app()->basePath : pasta protected
					$model->image->saveAs(Yii::app()->basePath."/../myFiles/Imagens/rooms/$model->image_path");
				}
				else $_SESSION['image_path'] = null;


				//Converte o documento XML que contem os conceitos (templates) num objecto
				$conceitos = simplexml_load_file("protected/components/conceitos.xml");
					
				//Converte a sala num objecto
				$sala_xml = simplexml_load_string($model->sala);

				if($model->template == 0){
					$sala_php = $this->template0($model, $sala_xml, $last_id_room, $conceitos);
				}

				if($model->template == 1){
					$sala_php = $this->template1($model, $sala_xml, $last_id_room, $conceitos);
				}

				if($model->template == 2){
					$sala_php = $this->template2($model, $sala_xml, $last_id_room, $conceitos);
				}

				// guarda a sala na diretoria das salas geradas
				$fh = fopen($_SESSION['room_path'], 'w') or die("can't open file");
				fwrite($fh, $sala_php);
				fclose($fh);
					
				// redirect to another page
				$this->redirect(array('/Exhibitions/view','id'=>$_SESSION['id_exhib']));
			}
		}
		$this->render('novaSala',array('model'=>$model));
	}

	/**
	 * Devolve a parte da sala correspondente ao cabecalho
	 * @param unknown_type $sala_xml
	 * @param unknown_type $last_id_room
	 */
	private function cabecalho($sala_xml, $last_id_room) {
		return "<?php
		\$NOME = '$sala_xml->nome';
		\$this->pageTitle=Yii::app()->name . ' - Salas';
		\$this->breadcrumbs=array(
		'Exposições'=>array('/Exhibitions/index'),
		\$NOME,
		);
			
		\$model = Rooms::model()->findByAttributes(array('id_room'=>".($last_id_room+1)."));
			
		\$this->widget('zii.widgets.CMenu', array(
		'items'=>array(
		//array('label'=>'Visualizar Exposições','url'=>array('/Exhibitions/index')),
		//array('label'=>'Criar Exposição','url'=>array('/Exhibitions/create')),
		//array('label'=>'Criar Sala','url'=>array('/site/novaSala')),
		//array('label'=>'Eliminar sala', 'url'=>'#', 'linkOptions'=>array( 'submit'=>array( '/rooms/delete', 'id'=>".($last_id_room+1)."), 'confirm'=>'Tem a certeza que pretende remover esta sala?' )),
		),
		)
		);
		?>

		<table style=\"margin-bottom: 0em\">
		<tr>
		<td width=\"60%\">
		<h1>
		<?php echo \$NOME ?>
		</h1>
		</td>
		<td width=\"40%\" style=\"text-align: right;\"><?php
		// caminho para a imagem desta exibição
		\$image_path = \"/../myFiles/Imagens/rooms/\$model->image_path\";
			
		// se a imagem existir exibe-a
		if(!empty(\$model->image_path) && file_exists(Yii::app()->basePath.\$image_path)){
		// Gera uma image tag
		\$img = CHtml::image(\"..\".\$image_path, \$model->name, array('class'=>'image', 'width'=>200, 'title'=>\$model->name));
		echo \$img;
	}
	?>
	</td>
	</tr>
	</table>

	<h3><?php echo \"Descrição:\" ?></h3>
	&nbsp;&nbsp;&nbsp;<?php echo \$model->description; ?>";
	}


	/**
	 * Sistema de navegação para uma sala
	 * Cria link para sala anterior e posterior segundo o número de ordenação estabelecido para cada sala
	 * @param STRING $sala_php
	 * @param Integer $id_exhib
	 * @param Integer $id_room
	 */
	private function roomNavigationSystem($sala_php, $id_exhib, $id_room) {
		$sala_php .= "\n<!-- Sistema de navegação --><br/><br/><hr/>
		<?php
		\$prev_room = Rooms::model()->findAllBySql('SELECT R.*
		FROM Rooms R
		INNER JOIN Exhibitions_Rooms ER
		ON ER.Roomsid_room = R.id_room
		WHERE ER.Exhibitionsid_exhibition=".$id_exhib."
		and ER.ord_nr + 1 = (SELECT ord_nr
		FROM Exhibitions_Rooms
		WHERE Exhibitionsid_exhibition=".$id_exhib." and Roomsid_room=".$id_room.")'
		);
			
		\$next_room = Rooms::model()->findAllBySql('SELECT R.*
		FROM Rooms R
		INNER JOIN Exhibitions_Rooms ER
		ON ER.Roomsid_room = R.id_room
		WHERE ER.Exhibitionsid_exhibition=".$id_exhib."
		and ER.ord_nr - 1 = (SELECT ord_nr
		FROM Exhibitions_Rooms
		WHERE Exhibitionsid_exhibition=".$id_exhib." and Roomsid_room=".$id_room.")'
		);
			
		if (!empty(\$prev_room)) {
		\$matches = array();
		\$subject = \$prev_room[0]->path;
		\$pattern = '@(?:[^/]+/)(\w+)(\.php)$@'; // extrai apenas o nome do ficheiro da sala
		preg_match(\$pattern, \$subject, \$matches);
		if (isset(\$matches[1])){
		\$link = Yii::app()->baseUrl . '/index.php/site/sala?view=' . \$matches[1];
		?>
		<div class=\"salaPrevious\" onclick=\"location.href='<? echo \$link;?>';\" style=\"cursor: pointer;\">
		<?php
		// Estabelece o link entre o nome da sala e a localizacao da sala
		echo CHtml::encode(\"Anterior - \".\$prev_room[0]->name);
		?>
		</div>
		<?php
	}
	else echo CHtml::encode(\$prev_room[0]->name);
	}
	?>
	<?php
		if (!empty(\$next_room)) {
			\$matches = array();
			\$subject = \$next_room[0]->path;
			\$pattern = '@(?:[^/]+/)(\w+)(\.php)$@'; // extrai apenas o nome do ficheiro da sala
			preg_match(\$pattern, \$subject, \$matches);
			if (isset(\$matches[1])){
				\$link = Yii::app()->baseUrl . '/index.php/site/sala?view=' . \$matches[1];
	?>
				<div class=\"salaNext\" onclick=\"location.href='<? echo \$link;?>';\" style=\"cursor: pointer;\">
	<?php
				// Estabelece o link entre o nome da sala e a localizacao da sala
				echo CHtml::encode(\"Seguinte - \".\$next_room[0]->name);
	?>
				</div>
	<?php
			}
			else echo CHtml::encode(\$next_room[0]->name);
		}
	?>";

		return $sala_php;
	}

	private function toArrayOfStrings(array $exhibitions){
		$result = array();
		foreach ($exhibitions as $ex) {
			array_push($result, strval($ex));
		}
			
		return $result;
	}

	private function normalize_str($str)
	{
		$invalid = array('Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z',
				'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A',
				'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E',
				'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
				'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y',
				'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a',
				'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e',  'ë'=>'e', 'ì'=>'i', 'í'=>'i',
				'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
				'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y',  'ý'=>'y', 'þ'=>'b',
				'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r', "`" => "'", "´" => "'", "„" => ",", "`" => "'",
				"´" => "'", "“" => "\"", "”" => "\"", "´" => "'", "&acirc;€™" => "'", "{" => "",
				"~" => "", "–" => "-", "’" => "'");

		$str = str_replace(array_keys($invalid), array_values($invalid), $str);

		return $str;
	}

	/**
	 * O gajo que fez esta função não sabe fazer comentários --'
	 * @param unknown_type $model
	 * @param unknown_type $sala_xml
	 * @param unknown_type $last_id_room
	 * @param unknown_type $conceitos
	 */
	private function template0($model, $sala_xml, $last_id_room, $conceitos){

		// Cabecalho de uma sala php (titulo, descricao e imagem)
		$sala_php = $this->cabecalho($sala_xml, $last_id_room);

		// Anexa os templates php à sala php já com o conteúdo introduzido no formulário
		$sala_php .= '<table width="100%"><tr>';
		$num_obj = 0;
		foreach($sala_xml->xpath('//objecto') as $obj) {
			if($num_obj < 2){

				$sala_php .= '<td width="50%">';
					
				// tipo do objecto
				$tipo = $obj->tipo;

				// obtem o template PHP do tipo extraido
				$template_php = $conceitos->xpath("//value[contains(../key,'$tipo')]");

				$nr_itens_encontrado = false;

				// substitui a variável de um argumento no template_php pelo valor real correspondente
				foreach($obj->argumentos->children() as $arg) {
					// substitui a tag (valor variável) em $template_php[0] que seja igual ao atributo id do argumento iterado
					// pelo valor do argumento
					$tag = "%".$arg['id']."%";
					// variavel de controlo (utilizada depois do ciclo)
					if ($tag=="%NrItens%")
						$nr_itens_encontrado = true;
					// $tag: The value being searched for
					// $arg: The replacement value that replaces found $tag values
					// $template_php[0]: The string or array being searched and replaced on
					$template_php[0] = str_replace($tag, $arg, $template_php[0]);
				}
				// se o argumento NrItens não for definido, um valor por defeito é utilizado
				if (!$nr_itens_encontrado)
					$template_php[0] = str_replace("%NrItens%", 10, $template_php[0]);

				// concatena o template PHP à sala PHP
				$sala_php .= $template_php[0];
					
				$sala_php .= '</td>';
			}
			$num_obj++;
		}

		$sala_php .= '</tr></table>';

		// elimina espaços do nome da sala. servirá como nome do ficheiro
		$nome_ficheiro = str_replace(" ", "", $sala_xml->nome);
		// substitui caracteres especiais por caracteres normais
		$nome_ficheiro = $this->normalize_str($nome_ficheiro);

		// variaveis de sessao que guardam a informacao necessaria de uma sala para armazenar na BD
		$_SESSION['room_name'] = strval($sala_xml->nome);
		$_SESSION['room_description'] = strval($sala_xml->descricao);
		$_SESSION['room_file_name'] = $nome_ficheiro;
		$_SESSION['exhibition'] = strval($sala_xml->exposicao);
		$_SESSION['tipo_ordenacao'] = $model->tipo_ordenacao;
		$_SESSION['ord_nr'] = $model->ord_nr;

		// armazena a informacao da sala gerada na base de dados utilizando a accao create do controller rooms
		$this->forward('/rooms/create',false);

		// adiciona à sala o sistema de navegação
		$sala_php = $this->roomNavigationSystem($sala_php, $_SESSION['id_exhib'], $_SESSION['id_nova_sala']);

		return $sala_php;
	}

	private function template1($model, $sala_xml, $last_id_room, $conceitos){

		// Cabecalho de uma sala php (titulo, descricao e imagem)
		$sala_php = $this->cabecalho($sala_xml, $last_id_room);

		// Anexa os templates php à sala php já com o conteúdo introduzido no formulário
		$sala_php .= '<table width="100%">';
		$ja_criou_linha_para_coluna = false;
		$num_obj = 0;

		foreach($sala_xml->xpath('//objecto') as $obj) {
			if($num_obj < 2){
				if(!$ja_criou_linha_para_coluna){
					$sala_php .='<tr>';
					$ja_criou_linha_para_coluna = true;
				}
				$sala_php .= '<td width="50%">';
			}

			// tipo do objecto
			$tipo = $obj->tipo;

			// obtem o template PHP do tipo extraido
			$template_php = $conceitos->xpath("//value[contains(../key,'$tipo')]");

			$nr_itens_encontrado = false;

			// substitui a variável de um argumento no template_php pelo valor real correspondente
			foreach($obj->argumentos->children() as $arg) {
				// substitui a tag (valor variável) em $template_php[0] que seja igual ao atributo id do argumento iterado
				// pelo valor do argumento
				$tag = "%".$arg['id']."%";
				// variavel de controlo (utilizada depois do ciclo)
				if ($tag=="%NrItens%")
					$nr_itens_encontrado = true;
				// $tag: The value being searched for
				// $arg: The replacement value that replaces found $tag values
				// $template_php[0]: The string or array being searched and replaced on
				$template_php[0] = str_replace($tag, $arg, $template_php[0]);
			}
			// se o argumento NrItens não for definido, um valor por defeito é utilizado
			if (!$nr_itens_encontrado)
				$template_php[0] = str_replace("%NrItens%", 10, $template_php[0]);

			// concatena o template PHP à sala PHP
			$sala_php .= $template_php[0];

			if($num_obj < 2){
				$sala_php .= '</td>';
				if($num_obj == 1){
					$sala_php .= '</tr></table>';
				}
			}

			$num_obj++;
		}

		// elimina espaços do nome da sala. servirá como nome do ficheiro
		$nome_ficheiro = str_replace(" ", "", $sala_xml->nome);
		// substitui caracteres especiais por caracteres normais
		$nome_ficheiro = $this->normalize_str($nome_ficheiro);

		// variaveis de sessao que guardam a informacao necessaria de uma sala para armazenar na BD
		$_SESSION['room_name'] = strval($sala_xml->nome);
		$_SESSION['room_description'] = strval($sala_xml->descricao);
		$_SESSION['room_file_name'] = $nome_ficheiro;
		$_SESSION['exhibition'] = strval($sala_xml->exposicao);
		$_SESSION['tipo_ordenacao'] = $model->tipo_ordenacao;
		$_SESSION['ord_nr'] = $model->ord_nr;

		// armazena a informacao da sala gerada na base de dados utilizando a accao create do controller rooms
		$this->forward('/rooms/create',false);

		// adiciona à sala o sistema de navegação
		$sala_php = $this->roomNavigationSystem($sala_php, $_SESSION['id_exhib'], $_SESSION['id_nova_sala']);

		return $sala_php;
	}

	private function template2($model, $sala_xml, $last_id_room, $conceitos){

		// Cabecalho de uma sala php (titulo, descricao e imagem)
		$sala_php = $this->cabecalho($sala_xml, $last_id_room);

		// Anexa os templates php à sala php já com o conteúdo introduzido no formulário
		foreach($sala_xml->xpath('//objecto') as $obj) {
			$sala_php .= '<td width="50%">';

			// tipo do objecto
			$tipo = $obj->tipo;

			// obtem o template PHP do tipo extraido
			$template_php = $conceitos->xpath("//value[contains(../key,'$tipo')]");

			$nr_itens_encontrado = false;

			// substitui a variável de um argumento no template_php pelo valor real correspondente
			foreach($obj->argumentos->children() as $arg) {
				// substitui a tag (valor variável) em $template_php[0] que seja igual ao atributo id do argumento iterado
				// pelo valor do argumento
				$tag = "%".$arg['id']."%";
				// variavel de controlo (utilizada depois do ciclo)
				if ($tag=="%NrItens%")
					$nr_itens_encontrado = true;
				// $tag: The value being searched for
				// $arg: The replacement value that replaces found $tag values
				// $template_php[0]: The string or array being searched and replaced on
				$template_php[0] = str_replace($tag, $arg, $template_php[0]);
			}
			// se o argumento NrItens não for definido, um valor por defeito é utilizado
			if (!$nr_itens_encontrado)
				$template_php[0] = str_replace("%NrItens%", 10, $template_php[0]);

			// concatena o template PHP à sala PHP
			$sala_php .= $template_php[0];

			$sala_php .= '</td>';
		}

		$sala_php .= '</tr></table>';

		// elimina espaços do nome da sala. servirá como nome do ficheiro
		$nome_ficheiro = str_replace(" ", "", $sala_xml->nome);
		// substitui caracteres especiais por caracteres normais
		$nome_ficheiro = $this->normalize_str($nome_ficheiro);

		// variaveis de sessao que guardam a informacao necessaria de uma sala para armazenar na BD
		$_SESSION['room_name'] = strval($sala_xml->nome);
		$_SESSION['room_description'] = strval($sala_xml->descricao);
		$_SESSION['room_file_name'] = $nome_ficheiro;
		$_SESSION['exhibition'] = strval($sala_xml->exposicao);
		$_SESSION['tipo_ordenacao'] = $model->tipo_ordenacao;
		$_SESSION['ord_nr'] = $model->ord_nr;

		// armazena a informacao da sala gerada na base de dados utilizando a accao create do controller rooms
		$this->forward('/rooms/create',false);

		// adiciona à sala o sistema de navegação
		$sala_php = $this->roomNavigationSystem($sala_php, $_SESSION['id_exhib'], $_SESSION['id_nova_sala']);

		return $sala_php;
	}





}
?>

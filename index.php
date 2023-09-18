<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Hello, world!</title>
</head>
<body>


    <div class="container">
        <?php
        include 'config.php';

        $conexao = Conexao::getInstance();

        $databases = Conexao::getDatabase();


        ?>
        <legend>Gerador 2.0 Laravel</legend>
        <div class="row border">

            <form action="#" method="post" id="frmDatabase" name="frmDatabase">


                <div class="col p-2">
                    <label>Base de dados</label>:
                    <select name="selDatabases" id="selDatabases" class="form form-control">
                        <option>Selecione a Base de Dados</option>

                        <?php foreach ($databases as $key => $database): ?>
                            <option><?=$database['Database']?></option>
                        <?php endforeach ?>

                    </select>
                    <br>
                    <input type="submit" value="Busca" class="btn btn-primary">

                </div>


            </form>
        </div>

        <?php

        $database = isset($_POST['selDatabases']) ? $_POST['selDatabases'] :"";

        $tabelas = Conexao::getTabelas($database);

        ?>

        <legend>Base Selecionada : <?=$database?></legend>


        <form action="#" method="post" id="frmTabela" name="frmTabela">

            <label>Escolha a Tabela</label>:
            <select name="selTabelas" id="selTabelas" class="form form-control">
                <option>Selecione a Tabela</option>
                <?php foreach ($tabelas as $key => $tabela): ?>
                    <option><?=$tabela['TABLE_NAME']?></option>
                <?php endforeach ?>

            </select>

            <input type="hidden" name="h_db" value="<?=$database?>">

            <br>
            <input type="submit" value="Gerar" name="btnGerar" class="btn btn-primary">

        </form>


        <?php



        $btnGerar = isset($_POST['btnGerar']) ? $_POST['btnGerar'] : "";


        $tabela = isset($_POST['selTabelas']) ? $_POST['selTabelas'] : "";

        $db = isset($_POST['h_db']) ? $_POST['h_db'] : "";

        if($btnGerar) {

            $colunas = Conexao::getColunas($tabela, $db);

            $linha = [];

            foreach ($colunas as $coluna) {

                $linhas[] = [
                    'nome_tabela'=> $coluna->TABLE_NAME,
                    'nome_campo' => $coluna->COLUMN_NAME,
                    'tipo_campo' => $coluna->DATA_TYPE,
                    'max_length' => $coluna->CHARACTER_MAXIMUM_LENGTH,
                    'chave_prim' => $coluna->COLUMN_KEY,
                    'permit_nul' => $coluna->IS_NULLABLE,
                    'comentario' => $coluna->COLUMN_COMMENT,
                ];
            }

            $debug = isset($_GET['debug']) ? $_GET['debug'] : null;

            if( !is_null($debug) ) {

                print "<legend>Informações da tabela </legend>";

                var_dump($colunas[0]);

                print '#############################################################';
                print "<legend>Informações dos Campos </legend>";
                var_dump($linhas);
            }


            foreach( $linhas as $key=>$linha ) {


                /* CAMPOS TEXT SIMPLES VARCHAR */
                if($linha['tipo_campo'] == 'varchar') {

                    $required = "";
                    $required_validator = "";

                    if($linha['permit_nul'] == 'NO'){
                        $required = ", 'required'";
                        $required_validator = "required|";
                    }

            # parte 1 do campo (inicio)
                    $campo_parte1 = htmlspecialchars("<div class='col-md-6 p-2'>").
                    "<br>".
                    htmlspecialchars("{{ Form::label('".$linha['nome_campo']."', '".$linha['comentario']."') }}:").
                    "<br>";


            # parte 2 do campo input sem o valor default ( Meio )
                    $campo_create = htmlspecialchars("{{ Form::text('".$linha['nome_campo']."', '', ['class'=>'form form-control', 'maxlength'=>".$linha['max_length'].", 'id'=>'".$linha['nome_campo']."' ".$required."]) }}").
                    "<br>";


            # parte 2 do campo input com o valor default para edição (Meio)
                    $campo_edit = htmlspecialchars("{{ Form::text('".$linha['nome_campo']."', $".$linhas[0]['nome_tabela']."->".$linha['nome_campo'].", ['class'=>'form form-control', 'maxlength'=>".$linha['max_length'].", 'id'=>'".$linha['nome_campo']."' ".$required."]) }}").
                    "<br>";

            #parte 3 do campo (Final)
                    $campo_parte3 = htmlspecialchars("</div>").
                    "<br>".
                    "<br>";


                    $campos_create[] = $campo_parte1.$campo_create.$campo_parte3;
                    $campos_edit[]   = $campo_parte1.$campo_edit.$campo_parte3;
                    
                    #### request/ controller
                    $request_campos[] = htmlspecialchars("$".$linhas[0]['nome_tabela']."->".$linha['nome_campo']." = \$request->".$linha['nome_campo']).";<br>";

                    #### validator
                    $request_validators[] = "\"".$linha['nome_campo']."\" => \"".$required_validator."max:".$linha['max_length']."\",";

                    #### validator messagem
                    $request_validators_messages[] = "\"".$linha['nome_campo'].".max\" => \"O Campo :attribute deve ter no máximo ".$linha['max_length']." Caracteres !\",";
                    
                    if($required) {
                        $request_validators_messages[] = "\"".$linha['nome_campo'].".required\" => \"O Campo :attribute é obrigatório !\",";
                    }


                    /* CAMPOS varchar numero  */
                }elseif( ($linha['tipo_campo'] == 'varchar') && (substr($linha['nome_campo'], 0, 3) == 'nr_') ){ 



                }/* CAMPOS TINYINT(1) data  */
                elseif( ($linha['tipo_campo'] == 'timestamp') && (substr($linha['nome_campo'], 0, 3) == 'dt_') ){ 

                }
                /* CAMPOS TINYINT(1) radio button  */
                elseif( ($linha['tipo_campo'] == 'tinyint') && (substr($linha['nome_campo'], 0, 3) == 'rb_') ){ 

                    $campo_parte1 = htmlspecialchars("<div class='form-check'>")."<br>".
                            htmlspecialchars("{{ Form::label('".$linha['nome_campo']."', '".$linha['comentario']."') }}:")."<br>";
                    
                    $campo_create = htmlspecialchars("{{ Form::radio('".$linha['nome_campo']."', 0, true) }}")."<br>"

                            .htmlspecialchars("<label class='form-check-label' for='".$linha['nome_campo']."'>")."<br>"
                                                ."Não"."<br>"
                            .htmlspecialchars("</label>")."<br>"
                            .htmlspecialchars("</div>")."<br>"
                            .htmlspecialchars("    <div class='form-check'>")."<br>"
                            .htmlspecialchars("        {{ Form::radio('".$linha['nome_campo']."', 1, false) }}")."<br>"
                            .htmlspecialchars("        <label class='form-check-label' for='".$linha['nome_campo']."'>")."<br>"
                            .htmlspecialchars("           Sim")."<br>"
                            .htmlspecialchars("        </label>")."<br>"
                            .htmlspecialchars("    </div>")."<br>"
                            .htmlspecialchars("    <br>");


                    $campo_edit = htmlspecialchars("{{ Form::radio('".$linha['nome_campo']."', 0, $".$linhas[0]['nome_tabela']."->".$linha['nome_campo']." == 0 ? true : false) }}")."<br>"

                            .htmlspecialchars("<label class='form-check-label' for='".$linha['nome_campo']."'>")."<br>"
                                                ."Não"."<br>"
                            .htmlspecialchars("</label>")."<br>"
                            .htmlspecialchars("</div>")."<br>"
                            .htmlspecialchars("    <div class='form-check'>")."<br>"
                            .htmlspecialchars("        {{ Form::radio('".$linha['nome_campo']."', 1, $".$linhas[0]['nome_tabela']."->".$linha['nome_campo']." == 1 ? true : false) }}")."<br>"
                            .htmlspecialchars("        <label class='form-check-label' for='".$linha['nome_campo']."'>")."<br>"
                            .htmlspecialchars("           Sim")."<br>"
                            .htmlspecialchars("        </label>")."<br>"
                            .htmlspecialchars("    </div>")."<br>"
                            .htmlspecialchars("    <br>");



                    $request_campos[] = htmlspecialchars("$".$linhas[0]['nome_tabela']."->".$linha['nome_campo']." = \$request->".$linha['nome_campo']).";<br>";

                    #### validator
                    $request_validators[] = "\"".$linha['nome_campo']."\" => \"boolean\",";

                    #### validator messagem
                    $request_validators_messages[] = "\"".$linha['nome_campo'].".boolean\" => \"O Campo :attribute deve ser \"Sim\" ou \"Não\" !\",";


                    $campos_create[] = $campo_parte1.$campo_create.$campo_parte3;
                    $campos_edit[]   = $campo_parte1.$campo_edit.$campo_parte3;



                    
                }/* CAMPOS TINYINT(1) checkbox  */
                elseif( ($linha['tipo_campo'] == 'tinyint') && (substr($linha['nome_campo'], 0, 3) == 'ck_') ){ 


                    //$request_campos[] = $vistoria->ck_esgo_sant_canalizado = isset($request->ck_esgo_sant_canalizado) ? $request->ck_esgo_sant_canalizado : 0;

                }

            }

            ?>
            <div class="row">
                <div class="col">
                    <?php
                    /*###########################  VIEW CREATE ############################## */
                    print "<code><div style='background:gray; color:#FFFFFF' class='p-3'>";
                    print "<p class='text-center'><legend> CREATE</legend></p><br>";
                    print htmlspecialchars("{{ Form::open(['url' => '".$linhas[0]['nome_tabela']."/store']) }}")."<br>";
                    print htmlspecialchars("{{ Form::token() }}")."<br>";

                    print htmlspecialchars("<div class='row p-2'>")."<br>";

                    print htmlspecialchars("<div class='col border rounded'>")."<br>";
                    print htmlspecialchars("<div class='row'>")."<br>";
                    $ct = 1;
                    foreach($campos_create as $key=>$campo_create) {

                        print $campo_create;
                        $ct++;
                        if( ( $ct == 3 ) &&  ( $key != array_key_last($campos_create) ) ){
                            print htmlspecialchars("</div>")."<br>";
                            print htmlspecialchars("</div>")."<br>";
                            print htmlspecialchars("</div>")."<br>";
                            print htmlspecialchars("<div class='row p-2'>")."<br>";
                            print htmlspecialchars("<div class='col border rounded'>")."<br>";
                            print htmlspecialchars("<div class='row'>")."<br>";
                            $ct =1;
                        }
                    } 

                    //print htmlspecialchars("</div>")."<br>";
                    print htmlspecialchars("</div>")."<br>";
                    print htmlspecialchars("</div>")."<br>";
                    print htmlspecialchars("<div class='row'>")."<br>";
                    print htmlspecialchars("<div class='col-md-12 p-2'>")."<br>";
                    print htmlspecialchars("{{ Form::submit('Salvar', ['class' => 'btn btn-primary']) }}")."<br>";
                    print htmlspecialchars("{{ Form::close() }}")."<br>";
                    print htmlspecialchars("</div>")."<br>";

                    print htmlspecialchars("</div>");

                    /* FINAL CREATE */
                    print "</div>";


                    print "<br>";

                    ?>
                </div>
            </div>

            <div class="row">

            </div>
            <?php
            /*###########################  CONTROLLER  ############################## */

            print "<div style='background:#A4A4A4; color:#FFFFFF' class='p-3'>";
            print "<legend> CONTROLLER STORE </legend><br>";

            # create

            #store

            print htmlspecialchars("\$request->validate(")."<br>";
            print htmlspecialchars("[")."<br>";

            foreach($request_validators as $request_validator) {
                print htmlspecialchars($request_validator)."<br>";
            }

            print "<br>";

            foreach($request_validators_messages as $request_validators_message) {
                print htmlspecialchars($request_validators_message)."<br>";
            }

            print htmlspecialchars("]);");

            print "<br>";
            print "<br>";

            print htmlspecialchars("$".$linhas[0]['nome_tabela']." = new ".ucwords($linhas[0]['nome_tabela'])."();")."<br>";

            foreach($request_campos as $request_campo) {
                print $request_campo;
            }

            print htmlspecialchars("$".$linhas[0]['nome_tabela']."->save();")."<br>";

            print htmlspecialchars("return redirect()->back();");

            print "</div>";
            print "<br>";
            print "<br>";

            ?>

        
        <div class="row">

        </div>

        <?php



        /*###########################  EDIT  ############################## */
        print "<div style='background:#A4A4A4; color:#FFFFFF' class='p-3'>";
        print "<legend> EDIT </legend><br>";
        print htmlspecialchars("{{ Form::open(['url' => '".$linhas[0]['nome_tabela']."/edit/'.$".$linhas[0]['nome_tabela']."->id]) }}")."<br>";
        print htmlspecialchars("{{ Form::token() }}")."<br>";

        print htmlspecialchars("<div class='row p-2'>")."<br>";

        print htmlspecialchars("<div class='col border rounded'>")."<br>";
        print htmlspecialchars("<div class='row'>")."<br>";
        $ct = 1;
        foreach($campos_edit as $key=>$campo_edit) {

            print $campo_edit;
            $ct++;
            if( ( $ct == 3 ) &&  ( $key != array_key_last($campos_create) ) ){
                print htmlspecialchars("</div>")."<br>";
                print htmlspecialchars("</div>")."<br>";
                print htmlspecialchars("</div>")."<br>";
                print htmlspecialchars("<div class='row p-2'>")."<br>";
                print htmlspecialchars("<div class='col border rounded'>")."<br>";
                print htmlspecialchars("<div class='row'>")."<br>";
                $ct =1;
            }
        } 

        //print htmlspecialchars("</div>")."<br>";
        print htmlspecialchars("</div>")."<br>";
        print htmlspecialchars("</div>")."<br>";
        print htmlspecialchars("<div class='row'>")."<br>";
        print htmlspecialchars("<div class='col-md-12 p-2'>")."<br>";
        print htmlspecialchars("{{ Form::submit('Salvar', ['class' => 'btn btn-primary']) }}")."<br>";
        print htmlspecialchars("{{ Form::close() }}")."<br>";
        print htmlspecialchars("</div>")."<br>";

        print htmlspecialchars("</div>");

        /* FINAL EDIT */
        print "</div>";


        print "<br>";
        print "<br>";


        /*###########################  CONTROLLER  ############################## */

            print "<div style='background:#A4A4A4; color:#FFFFFF' class='p-3'>";
            print "<legend> CONTROLLER UPDATE </legend><br>";

            # create

            #store

            print htmlspecialchars("\$request->validate(")."<br>";
            print htmlspecialchars("[")."<br>";

            foreach($request_validators as $request_validator) {
                print htmlspecialchars($request_validator)."<br>";
            }

            print "<br>";

            foreach($request_validators_messages as $request_validators_message) {
                print htmlspecialchars($request_validators_message)."<br>";
            }

            print htmlspecialchars("]);");

            print "<br>";
            print "<br>";

            print htmlspecialchars("$".$linhas[0]['nome_tabela']." = new ".ucwords($linhas[0]['nome_tabela'])."();")."<br>";

            foreach($request_campos as $request_campo) {
                print $request_campo;
            }

            print htmlspecialchars("$".$linhas[0]['nome_tabela']."->save();")."<br>";

            print htmlspecialchars("return redirect()->back();");

            print "</div>";
            print "<br>";
            print "<br>";



        /*###########################  SHOW  ############################## */
        print "<div style='background:#A4A4A4; color:#FFFFFF'>";
        print "<legend> SHOW </legend><br>";


        print htmlspecialchars("<div class='row'>")."<br>";

        foreach($linhas as $linha) {

            print htmlspecialchars("<div class='col-md-4>")."<br>";
            print $linha['comentario'];
            print htmlspecialchars("</div>")."<br>";

            print htmlspecialchars("<div class='col-md-8>")."<br>";
            print '$'.$linha['nome_tabela'].'->'.$linha['nome_campo'];
            print htmlspecialchars("</div>")."<br>";

        } 



        /* FINAL EDIT */
        print htmlspecialchars("</div>");


    }
    ?>


</div>




!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
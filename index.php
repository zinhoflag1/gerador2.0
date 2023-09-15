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

$conexao = new PDO('mysql:host=localhost;port=3306;dbname=gerador', 'root', '12345678', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));



    $sql = "SELECT *
FROM information_schema.columns
WHERE table_name='cadastro'";

        $result = $conexao->query($sql);
        $result->execute();

        $colunas = $result->fetchAll(PDO::FETCH_OBJ);


    
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


    // <div class="row">
    //                             <div class='col'>
    //                                 {{ Form::label('prefeito', 'Nome do Prefeito') }}:
    //                                 {{ Form::text('prefeito', $compdec->municipio->prefeito, ['class' => 'form form-control', 'maxlength' => 70]) }}
    //                                 <br>
    //                             </div>


    foreach( $linhas as $key=>$linha ) {


        /* CAMPOS TEXT SIMPLES VARCHAR */
        if($linha['tipo_campo'] == 'varchar') {

            $required = ($linha['permit_nul'] == 'NO') ? ", 'required'": "";

            # parte 1 do campo (inicio)
            $campo_parte1 = htmlspecialchars("<div class='col-md-6 p-2'>").
            "<br>".
            htmlspecialchars("{{ Form::label('".$linha['nome_campo']."', '".$linha['comentario']."') }}:").
            "<br>";

            
            # parte 2 do campo input sem o valor default ( Meio )
            $campo_create = htmlspecialchars("{{ Form::text('".$linha['nome_campo']."', '', ['class'=>'form form-control', 'maxlength'=>".$linha['max_length'].", 'id'=>'".$linha['nome_campo']."' ".$required."]) }}").
            "<br>";


            # parte 2 do campo input com o valor default para edição (Meio)
            $campo_edit = htmlspecialchars("{{ Form::text('".$linha['nome_campo']."', '$".$linhas[0]['nome_tabela']."->".$linha['nome_campo']."', ['class'=>'form form-control', 'maxlength'=>".$linha['max_length'].", 'id'=>'".$linha['nome_campo']."' ".$required."]) }}").
            "<br>";

            #parte 3 do campo (Final)
            $campo_parte3 = htmlspecialchars("</div>").
            "<br>".
            "<br>";


            $campos_create[] = $campo_parte1.$campo_create.$campo_parte3;
            $campos_edit[]   = $campo_parte1.$campo_edit.$campo_parte3;
        
        /* CAMPOS varchar numero  */
        }elseif( ($linha['tipo_campo'] == 'varchar') && (substr($linha['nome_campo'], 0, 3) == 'nr_') ){ 
         

        
        }/* CAMPOS TINYINT(1) data  */
        elseif( ($linha['tipo_campo'] == 'timestamp') && (substr($linha['nome_campo'], 0, 3) == 'dt_') ){ 
            
        }
        /* CAMPOS TINYINT(1) radio button  */
        elseif( ($linha['tipo_campo'] == 'tinyint') && (substr($linha['nome_campo'], 0, 3) == 'rb_') ){ 
            
        }/* CAMPOS TINYINT(1) checkbox  */
        elseif( ($linha['tipo_campo'] == 'tinyint') && (substr($linha['nome_campo'], 0, 3) == 'ck_') ){ 
            
        }

    }

    ?>
    <div class="row">
        <div class="col">
    <?php
/*###########################  CREATE ############################## */
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
    
    print htmlspecialchars("</div>")."<br>";
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
    print "<br>";

?>
</div>
</div>

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
    
    print htmlspecialchars("</div>")."<br>";
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
?>
</div>


!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>
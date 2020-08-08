<?php
session_start();
//cria uma sessão entre os arquivos dentro do diretorio
require './Bibliotecas/phpmailer/Exception.php';
require './Bibliotecas/phpmailer/OAuth.php';
require './Bibliotecas/phpmailer/PHPMailer.php';
require './Bibliotecas/phpmailer/POP3.php';
require './Bibliotecas/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mensagem {

    //classe que armazena objetos que são mensagens recebidas do front-end
    private $destino = null;
    private $assunto = null;
    private $mensagem = null;
    public $status = array('codigo_status' => null, 'descricao_status' => '');
    private $anexo = null;

    public function __get($atributo) {
        return $this->$atributo;
    }

    public function __set($atributo, $valor) {
        $this->$atributo = $valor;
    }

    public function validaAtributo() {
        if (empty($this->assunto) || empty($this->destino) || empty($this->mensagem)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

}

$mensagem = new Mensagem();
$mensagem->__set('destino', $_POST['destino']);
$mensagem->__set('assunto', $_POST['assunto']);
$mensagem->__set('mensagem', $_POST['mensagem']);
$mensagem->__set('anexo',$_POST['anexo']);

if (!$mensagem->validaAtributo()) {
    echo 'atributo invalido';
    header('location:index.php');
}
$mail = new PHPMailer(true);
try {
    //Server settings
    $mail->SMTPDebug = FALSE;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'meu email';                 // SMTP username
    $mail->Password = 'minha senha';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to
    //Recipients
    $mail->setFrom('email do remetente', 'nome do remetente');
    $mail->addAddress($mensagem->__get('destino'));     // Add a recipient
    //$mail->addAddress('ellen@example.com');               // Name is optional
    //$mail->addReplyTo('info@example.com', 'Information');
    // $mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');
    //Attachments
    $mail->addAttachment($mensagem->__get('anexo'));         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $mensagem->__get('assunto');
    $mail->Body = $mensagem->__get('mensagem');
    $mail->AltBody = $mensagem->__get('mensagem');

    $mail->send();
    $mensagem->status['codigo_status'] = 1;
    $mensagem->status['descricao_status'] = 'E-mail enviado com sucesso';
} catch (Exception $e) {
    $mensagem->status['codigo_status'] = 2;
    $mensagem->status['descricao_status'] = 'Não foi possível enviar este e-mail! Por favor tente novamente mais tarde. Detalhes do erro: ' . $mail->ErrorInfo;

    //alguma lógica que armazene o erro para posterior análise por parte do programador
}
?>

<html>
    <head>
        <meta charset="utf-8" />
        <title>App Mail ++</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    </head>

    <body>

        <div class="container">
            <div class="py-3 text-center">
                <img class="d-block mx-auto mb-2" src="logo.png" alt="" width="72" height="72">
                <h2>Send Mail</h2>
                <p class="lead">Seu app de envio de e-mails particular!</p>
            </div>

            <div class="row">
                <div class="col-md-12">

                    <?php if($mensagem->status['codigo_status'] == 1) { ?>

                    <div class="container">
                        <h1 class="display-4 text-success">Sucesso</h1>
                        <p><?= $mensagem->status['descricao_status'] ?></p>
                        <a href="index.php" class="btn btn-success btn-lg mt-5 text-white">Voltar</a>
                    </div>

                    <?php } ?>

                    <?php if($mensagem->status['codigo_status'] == 2) { ?>

                    <div class="container">
                        <h1 class="display-4 text-danger">Ops!</h1>
                        <p><?= $mensagem->status['descricao_status'] ?></p>
                        <a href="index.php" class="btn btn-success btn-lg mt-5 text-white">Voltar</a>
                    </div>

                    <?php } ?>

                </div>
            </div>
        </div>

    </body>
</html>

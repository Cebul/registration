<?php
session_start();

if(isset($_POST['email']))

	{
		//Udana walidacja? Załóżmy ,że tak
		$wszystko_OK = true;

		//Sprawdź poprawność nickname'a
		$nick = $_POST['nick'];

		//Sprawdzenie długości nicka
		if((strlen($nick)<3) || (strlen($nick)>20))
		{
			$wszystko_OK=false;
			$_SESSION['e_nick']="Nick musi posiadać od 3 do 20 znaków!";
		}
		//Sprawdzanie czy wszystkie znaki w nicku sa alfanumeryczne
		if(ctype_alnum($nick)==false)
		{
			$wszystko_OK=false;
			$_SESSION['e_nick']="Nick musi składać się tylko z liter i cyfr (bez polskich znaków)";
		}

		//Sprawdź poprawność adresu email
		$email = $_POST['email'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);

		if((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false) || ($emailB!=$email))
		{
			$wszystko_OK = false;
			$_SESSION['e_email'] = "Podaj poprawny adres email!";
		}

		//Sprawdź poprawnosć hasła
		$haslo1 = $_POST['haslo1'];
		$haslo2 = $_POST['haslo2'];

		if((strlen($haslo1)<8) || (strlen($haslo1)>20) )
		{
			$wszystko_OK = false;
			$_SESSION['e_haslo'] = "Hasło musi posiadać od 8 do 20 znaków!";
		}

		if($haslo1!=$haslo2)
		{
			$wszystko_OK = false;
			$_SESSION['e_haslo'] = "Podane hasła nie są identyczne!";
		}

		$haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT);

		//Sprawdz czy zaakceptowano regulamin
		if(!isset($_POST['regulamin']))
		{
			$wszystko_OK = false;
			$_SESSION['e_regulamin'] = "Potwierdź akceptacje regulaminu!";
		}

		//Bot or not? O to jest pytanie :)

		$sekret = "6LdYBhQTAAAAAAZo-S_8B_JdZ29vCilaR-HT6dvx";

		$sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$sekret.'&response='.
			$_POST['g-recaptcha-response']);

		$odpowiedz = json_decode($sprawdz);

		if($odpowiedz->success==false)
		{
			$wszystko_OK = false;
			$_SESSION['e_bot'] = "Potwierdź ,że nie jesteś botem!";

		}

		require_once "connect.php";

		try
		{

			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);

		}
		catch(Exception $e)
		{
			echo '<span class="error"> Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestracje 
			w innym terminie!"</span>';
			echo '<br />Informacja developerska: '.$e;
		}

		if($wszystko_OK==true)
		{
			//Wszystkie testy zaliczone dodajemy gracza do bazy
			echo "Udana walidacja!"; exit();
		}

	}

?>


<!DOCTYPE HTML>
<html>
<head>
	<meta charset = "utf-8" />
	<meta http-equiv = "X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Osadnicy - załóż darmowe konto</title>
	<script src='https://www.google.com/recaptcha/api.js'></script>

	<style>
		.error
		{
			color:red;
			margin-top: 10px;
			margin-bottom: 10px;
		}


	</style>

	</head>
	<body>
		<form method="post">

			Nickname:<br /> <input type="text" name="nick" /><br />

			<?php

				if(isset($_SESSION['e_nick']))
				{
					echo '<div class="error">'.$_SESSION['e_nick'].'</div>';
					unset($_SESSION['e_nick']);
				}


			?>

			E-mail:<br /> <input type="text" name="email" /><br />

			<?php

				if(isset($_SESSION['e_email']))
				{
					echo '<div class="error">'.$_SESSION['e_email'].'</div>';
					unset($_SESSION['e_email']);
				}

			?>

			Twoje hasło:<br /> <input type="password" name="haslo1" /><br />

			<?php

				if(isset($_SESSION['e_haslo']))
				{
					echo '<div class="error">'.$_SESSION['e_haslo'].'</div>';
					unset($_SESSION['e_haslo']);
				}

			?>

			Powtórz hasło:<br /> <input type="password" name="haslo2" /><br />

		<label>
			<input type="checkbox" name="regulamin" /> Akceptuje regulamin
		</label>

		<?php

			if(isset($_SESSION['e_regulamin']))
			{
				echo '<div class="error">'.$_SESSION['e_regulamin'].'</div>';
				unset($_SESSION['e_regulamin']);
			}

		?>

		<br />

		<div class="g-recaptcha" data-sitekey="6LdYBhQTAAAAAAP_AbiKBvC7CreFubSkTRmglxU6"></div>

			<?php

			if(isset($_SESSION['e_bot']))
			{
				echo '<div class="error">'.$_SESSION['e_bot'].'</div>';
				unset($_SESSION['e_bot']);
			}

		?>

		<input type="submit" value="Zarejestruj się!" />


		</form>


	</body>
</html>
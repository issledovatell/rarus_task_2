<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

include 'elems/init.php';

$name = $_REQUEST['name'] ?? '';
$telefon = $_REQUEST['telefon'] ?? '';
$email = $_REQUEST['email'] ?? '';
$comment = $_REQUEST['comment'] ?? '';

// если форма отправлена
if (isset($_REQUEST['submit'])) {
  // есть имейл или нет
  if ($name && $telefon && $email) {
    // проверка емейла на валидность
    if (validateEmail($email)) {
      // проверка на отсутствие домена gmail
      if (checkDomenOnGmail($email)) {
        // заполнены обязательные поля или нет
        if ($name && $telefon) {
          $query = createQuery($name, $telefon, $email, $comment);
        } else {
          $_SESSION['message'] = "Поля 'name' и 'telefon' обязательны к заполнению";
          $_SESSION['error'] = true;
        }
      } else {
        $_SESSION['message'] = "Емайл не должен содержать домен 'gmail'";
        $_SESSION['error'] = true;
      }
    } else {
      $_SESSION['message'] = "Емайл не валиден";
      $_SESSION['error'] = true;
    }
  } else {
    $_SESSION['message'] = "Заполните и отправьте форму!";
  }

  if ($name && $telefon && !$email) {
    $query = createQuery($name, $telefon, $email, $comment);
  }

  if (isset($query)) {
    insertToTable($link, $query);
    $_SESSION['message'] = 'Данные успешно добавлены в таблицу!';
  }
}

// создание SQL запроса вставки данных в таблицу
function createQuery($name, $telefon, $email, $comment)
{
  return "INSERT INTO users (name, email, telefon, comment) VALUES ('$name', '$email', '$telefon', '$comment')";
}

// ф-ия вставки данных пользователя
function insertToTable($link, $query)
{
  mysqli_query($link, $query) or die(mysqli_error($link));
}

// валидация эл. адреса
function validateEmail($email)
{
  return preg_match('#^[0-9a-zA-Z-.]+@([a-z]+)\.[a-z]{2,3}$#', $email);
}

// проверка на отсутствие доменного имении gmail в эл. адресе
function checkDomenOnGmail($email)
{
  // Можно проверить емейл на отсутствие домена 'gmail' таким способом:
  // $reg = '#^[0-9a-zA-Z-.]+@[^g][a-z]+\.[a-z]{2,3}$#';

  preg_match('#^[0-9a-zA-Z-.]+@([a-z]+)\.[a-z]{2,3}$#', $email, $matches);
  $domenName = $matches[1];
  $checkDomen = 'gmail';

  return $domenName != $checkDomen;
}

include 'layout.php';

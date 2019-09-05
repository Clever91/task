<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <title>Task</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>
<body>
  <div class="container">
    <h1>Task</h1>
    <form action = "<?php $_PHP_SELF ?>" method="POST">
      <div class="form-group">
        <label for="name">File name</label>
        <input type="text" name="name" class="form-control" id="name" aria-describedby="nameHelp" placeholder="Enter file name is here" required>
        <small id="nameHelp" class="form-text text-muted">We need enter file which must calculate and without file format (.txt)</small>
      </div>
      <div class="form-group">
        <label for="operation">Operation</label>
        <input type="text" name="operation" class="form-control" id="oparation" aria-describedby="operationHelp" placeholder="Enter oparation is here" maxlength="1" required>
        <small id="operationHelp" class="form-text text-muted">EXM: + , - , * , /</small>
      </div>
      <button type="submit" class="btn btn-primary">Calculate</button>
    </form>
  </div>
</body>
</html>


<?php

// ~~~~~~~~~~~~~~~ Global Functions ~~~~~~~~~~~~~~~

function getValueWithTrim($str)
{
  return !empty(trim($str)) ? trim($str) : null;
}

function isTypeOfOperation($operation)
{
  $operations = ["+", "-", "*", "/"];

  return in_array($operation, $operations);
}

function isFileReadable($filename)
{
  return is_readable($filename);
}

function isFileWriteable($filename)
{
  return is_writable($filename);
}

function isFileExists($filename)
{
  return file_exists($filename);
}

function getFileWithPath($filename)
{
  return getCurrentPath() . $filename . ".txt";
}

function getCurrentPath()
{
  return getcwd() . "/";
}

// ~~~~~~~~~~~~~~~ Logic Functions ~~~~~~~~~~~~~~~

function splitUpBySpace($str)
{
  return explode(" ", $str);
}

function convertToNumber($number)
{
  return floatval($number);
}

function saveResultToFile($filename, $str)
{
  appendToFile($filename, $str);
}

function appendToFile($filename, $txt)
{
  // file_put_contents($filename, $txt.PHP_EOL, FILE_APPEND | LOCK_EX);
  $file = fopen($filename, "a") or die("Unable to open file!");
  fwrite($file, $txt . "\n");
  fclose($file);
}

function readFileByLine($filename, $operation)
{
  $positive_filename = getFileWithPath("positive_result");
  $negative_filename = getFileWithPath("negative_result");

  if (isFileExists($positive_filename))
    unlink($positive_filename);

  if (isFileExists($negative_filename))
    unlink($negative_filename);

  $fn = fopen($filename, "r");

  while (! feof($fn) ) {
	   $line = fgets($fn);
     $arr = splitUpBySpace($line);
     $one = convertToNumber($arr[0]);
     $two = convertToNumber($arr[1]);

     $result = eval("return (float)$one $operation $two;");

     if ($result > 0) {
       saveResultToFile($positive_filename, $result);
     } elseif ($result < 0) {
       saveResultToFile($negative_filename, $result);
     }
  }

  fclose($fn);
}


// ~~~~~~~~~~~~~~~ Main ~~~~~~~~~~~~~~~


if (!empty($_POST) && isset($_POST["name"]) && isset($_POST["operation"]))
{

  $name = getValueWithTrim($_POST["name"]);
  $operation = getValueWithTrim($_POST["operation"]);

  if (is_null($name) || is_null($operation))
    die ("Fields must not be empty");

  if (!isTypeOfOperation($operation))
    die ("Operation format is incorrect");

  $filename = getFileWithPath($name);
  if (!isFileReadable($filename))
    die ("This file is not found");

  readFileByLine($filename, $operation);

  echo "Task is finished";

}

?>

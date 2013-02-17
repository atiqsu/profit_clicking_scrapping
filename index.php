<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
  </head>
  <body>
    <?php
    
    print '<pre>';
    require_once 'class/profit_clicking_parser.class.php';

    $profit = new ProfitClicking('http://www.profitclicking.com/media/index.php');


    $resultsArray = $profit->getResponseArray();

    // Combined results link and date
    print_r($resultsArray);

    // Extracted links
    $links = $profit->getLinks();
    print_r($links);

    // Extracted dates
    $dates = $profit->getDates();
    print_r($dates);
    // put your code here
    // Write your own code for saving the database
    ?>
  </body>
</html>

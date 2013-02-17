<?php

/**
 * Source code for parsing the profit clicking
 * @package : profit
 * @filename : profit_clicking_parser.class.php 
 * @version : 0.1a
 * @author : Samundra Shrestha
 * @date : February 17, 2013
 */
class ProfitClicking {

  private static $url;
  private static $dates;
  private static $links;
  private static $contents;
  private static $response;

  /**
   * Constructor for the profit clicking
   * @param string $url 
   */
  public function __construct($url = 'http://www.profitclicking.com/announcements/index.php') {
    self::$url = $url;
    //$this->pages = array();
    // Prepare the contents from which the contents will be extracted
    //$this->contents = $this->getResponsePage($this->url);
//    self::$contents = file_get_contents('index.php.txt');
    self::$contents = file_get_contents(self::$url);

    $this->_bootstrap();
  }

  /**
   * The contents grabbed from the url supplied 
   * @return string 
   */
  public static function getContents() {
    return self::$contents;
  }

  /**
   * Returns the dates array
   * @return array dates array 
   */
  public static function getDates() {
    return self::$dates;
  }

  /**
   * Returns the Links array
   * @return array links array 
   */
  public static function getLinks() {
    return self::$links;
  }

  /**
   * Bootstrap the class so everything is loaded before we use them, like dates,links  
   */
  protected function _bootstrap() {
    $divs = $this->_extractContents('div', self::$contents);
    self::$response = $divs;
  }

  /**
   * Returns the reponse array
   * @return array response array 
   */
  public static function getResponseArray() {
    return self::$response;
  }

  /**
   * Static function which actually gets the page from the supplied url 
   * @param string Link to the website from which contents is to be pulled.
   * @return string Returns the contents grabbed
   */
  public static function getResponsePage() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, self::$url);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $result = curl_exec($ch);
    curl_close($ch);

    // Save the response page
    self::$response = $result;

    return $result;
  }

  /**
   * Function to extract the required parameters from the response page
   * @param string $tag Tag eg. 'div','span' etc
   * @param boolean $html Whether this will be loaded as html or xml
   * @param integer $strict int variable to set the strict type
   * @return array final response array
   */
  private function _extractContents($tag, $html, $strict = 0) {
    $dom = new domDocument;

    if ($strict == 1) {
      @$dom->loadXML($html);
    } else {
      @$dom->loadHTML($html);
    }

    /*     * * discard white space ** */
    $dom->preserveWhiteSpace = false;

    /** the tag by its tag name ** */
    $content = $dom->getElementsByTagname($tag);

    /*     * * the array to return ** */
    $out = array();

    // Stores the dates only
    $dateArr = array();

    // Stores the links only
    $linkArr = array();

    foreach ($content as $item) {
      $node = $item->getAttributeNode('class');

      if (isset($node) && is_object($node)) {

        // Select the class
        if ($node->value == 'sidebarmedialeftblocks') {
          $p = $item->getElementsByTagName('div');

          // Gets the dates
          $content = array();
          $count = 0;
          foreach ($p as $pi) {

            if ($pi->hasAttribute('class')) {
              $node = $pi->getAttributeNode('class');

              if ($node->value == 'dailytext' || $node->value == 'dailytitle') {
                $ownerElement = $node->ownerElement;
                $rawText = $ownerElement->textContent;
                if ($node->value == 'dailytext') {
                  //$cNode = $node->cloneNode();
                  $end = strpos($rawText, '...');
                  $contents = substr($rawText, 0, $end);
                  $content[$count] = $contents;
                }

                if ($node->value == 'dailytitle') {
                  if (strpos($rawText, '~') > 0) {
                    $dateArr[] = $rawText;
                  }
                }
              }
            }

            //print $pi->nodeValue;
            if ($pi->hasChildNodes()) {
              $childs = $pi->childNodes;

              foreach ($childs as $ch) {

                if ($ch instanceof DOMElement) {
//                  print $ch->nodeValue;

                  if ($ch->tagName == 'div') {
//                    print $ch->nodeValue;
                  }

                  if ($ch->tagName == 'span') {
                    $spans = $ch->getElementsByTagName('a');
                    foreach ($spans as $idx => $span) {
                      $linkArr[] = array(
                          'contents' => $content[$count],
                          'href' => $span->getAttribute('href'),
                          'text' => $span->nodeValue
                      );
                    }
                  }
                }
              }
            }
            $count++;
          }
          // Gets the links only
          $out['date'] = $dateArr;
          $out['link'] = $linkArr;
        }
      }
    }

    foreach ($out['link'] as $idx => $val) {
      $out['link'][$idx]['date'] = $out['date'][$idx];
    }

    self::$dates = $out['date'];
    self::$links = $out['link'];

    /*     * * return the results ** */
    return $out;
  }

}

?>

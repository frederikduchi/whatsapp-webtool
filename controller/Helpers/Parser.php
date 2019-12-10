<?php
    //header("Content-Type: application/json; charset=utf-8");
    require_once __DIR__ . '/emoji/Emoji.php';

    class Parser {

        public function parseFile($path){
            $parsedLines = [];
            $errorLines = [];

            $file = fopen($path, 'r');
            while(!feof($file)){
                $line = fgets($file);
                $result = $this->parseLine($line);
                if(is_array($result)){
                    // line is parsed correctly: add to array
                    $parsedLines[] = $result;
                }else if($result !== ''){
                    // line is a text that belongs to previous element
                    $index = count($parsedLines) -1;
                    $parsedLines[$index]['text'] .= ' ' . $result;
                    $errorLines[] = $line;
                }
            }
            fclose($file);

            $parsedLines = $this->setDateFormat($parsedLines);

            $content = array('parsed_lines' => $parsedLines, 'error_lines' => $errorLines);
            //return json_encode($content);
            return $content;
        }

        private function setDateFormat($parsedLines){
            // loop over all the items and check if the day or month is first
            // day is set first by default
            $dayFirst = true;
            foreach($parsedLines as $line){
                $split = explode('/',$line['date']);
                if($split[1] > 12){
                    $dayFirst = false;
                }
            }

            // loop over all items and set the correct date and time format in a new filed
            for ($i=0; $i<count($parsedLines); $i++){
                $split = explode('/',$parsedLines[$i]['date']);
                $year = $split[2];
                if(strlen($year) == 2){
                    $year = '20' . $year;
                }
                
                $day = $split[0];
                $month = $split[1];
                if(!$dayFirst){
                    $day = $split[1];
                    $month = $split[0];       
                }
                $parsedLines[$i]['formatdate'] =  $year . '-' . $month . '-' . $day . ' ' . $parsedLines[$i]['time'];
            }
            
            return $parsedLines;
        }

        private function getDateTime($line){
            // check first character to determine iOs or Android
            $clean_line = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $line);
            $first_char = substr($clean_line,0,1);
            if($first_char === '['){
                return ltrim(explode(']',$clean_line)[0],'[');
            }else{
                return explode(' - ', $clean_line)[0];
            }       
        }

        private function getRemainingPart($line){
            // check first character to determine iOs or Android
            $clean_line = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $line);
            $first_char = substr($clean_line,0,1);
            if($first_char === '['){
                return explode('] ',$line)[1];
            }else{
                return explode(' - ', $line)[1];
            }
        }

        private function getFileName($text){
            $clean_text = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $text);
            $first_char = substr($clean_text,0,1);
            if($first_char === '<'){
                return rtrim(explode(': ',$text)[1],'>');
            }else{
                return explode(' (',$text)[0];
            }
        }

        private function parseLine($line){
            $result = array('author' => '', 'date' => '', 'time' => '', 'text' => '', 'emojis' => array(), 'media' => array());    
            $line = str_replace(array("\n", "\t", "\r"), '', $line);
        
            // PARSING DATE AND TIME
            // split the date and time part
            $dateTimePart = $this->getDateTime($line); 
            $dateTime = explode(' ',$dateTimePart);

            // check if the date and time are found.  Return the line if not
            if(count($dateTime) !== 2 || count(explode('/',$dateTime[0])) !== 3 || count(explode(':',$dateTime[1])) <= 1){
                return $line;
            }

            // remove a comma from the date if required
            $date = str_replace(',','', $dateTime[0]);
            $result['date'] = $date;

            // check if the time includes the seconds
            $time = $dateTime[1];
            if(count(explode(':',$time)) === 2){
                $time = $time . ':00';
            }
            $result['time'] = $time;

        
            // PARSING AUTHOR AND TEXT
            $remainingPart = $this->getRemainingPart($line);
            
            $authorTextPart = explode(': ', $remainingPart);
            if(count($authorTextPart) <2){
                // probably an information line, dump it
                $result['author'] = 'Whatsapp status';
                $result['text'] = $authorTextPart[0];
                return $result;
            }

            // line look fine, get the author
            $author = array_shift($authorTextPart);
            $result['author'] = $author;

            $textPart = implode(': ',$authorTextPart);
     
            // check if it contains media
            if(strpos($textPart,'(bestand bijgevoegd)') !== false || strpos($textPart,'(file attached)') !== false || strpos($textPart,'<attached:') !== false|| strpos($textPart,'<bijgevoegd:') !== false){
                $file = $this->getFileName($textPart);
                $extension = explode('.',$file)[1];
                if($extension === 'jpg'){
                    $result['media'] = array('type' => 'image', 'path' => $file);
                }else if($extension === 'mp4'){
                    $result['media'] = array('type' => 'video', 'path' => $file);
                }else{
                    // TODO: wat indien er spaties in de bestandsnaam staan?  urlencode kan een oplossing zijn, maar neemt dan de ganse lijn
                    $result['media'] = array('type' => 'other', 'path' => $file);
                }
                return $result;
            }

            // check if it contains deleted media
            $not_found_messages = array('<Media weggelaten>','<Media omitted>','afbeelding weggelaten', 'video weggelaten','image omitted', 'video omitted');
            if(in_array($textPart,$not_found_messages)){
            $result['media'] = array('type' => 'missing', 'path' => 'default.jpg');
                return $result;         
            }

            // check if text contains emoji
            $emojis = Emoji\detect_emoji($textPart);
            if(count($emojis) > 0){
                foreach($emojis as $emoji){
                    $result['emojis'][] = $emoji['emoji'];
                }
            }
        
            $result['text'] = $textPart;
            
            return $result;
        }
    }

  


    
 

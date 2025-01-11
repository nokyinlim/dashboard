<?php

session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: auth.php');
    exit;
}

include 'database.php';

class Period {
    public $id;
    public $day_number;
    public $week_number;
    public $period_number;
    public $title;
    public $location;
    public $professor;
    public $class;

    public function __construct($day_number, $week_number, $period_number, $title, $location, $professor, $class) {
        $this->day_number = $day_number;
        $this->week_number = $week_number;
        $this->period_number = $period_number;
        $this->title = $title;
        $this->location = $location;
        $this->professor = $professor;
        $this->class = $class;
    }

    public function saveToDatabase($db) {
        // Set default values if any fields are empty
        $day = !empty($this->day_number) ? $this->day_number : 0;
        $week = !empty($this->week_number) ? $this->week_number : 0;
        $period = !empty($this->period_number) ? $this->period_number : 0;

        $title = !empty($this->title) ? $this->title : '';
        $location = !empty($this->location) ? $this->location : '';
        $professor = !empty($this->professor) ? $this->professor : '';
        $class = !empty($this->class) ? $this->class : '';

        return $db->savePeriod($day, $week, $period, $title, $location, $professor, $class);
    }
}
class TimetableParser {

    public function parseTimetable($text) {
        // global $period_times;

        $period_times = [
            '08:00',
            '08:15', 
            '09:15', 
            '10:35', 
            '11:35', 
            '12:35', 
            '13:35', 
            '14:35', 
            '15:35',
            '16:35'
        ];

        $lines = preg_split('/\r\n|\r|\n/', $text);
        foreach ($lines as $line) {
            echo $line . "<br>";
        }
        $periods = [];
        $week_number = 1; // Week A is 1, Week B is 2
        $day_number = 1; // Start with Monday
        $period_number = 1;

        $expecting_period = 1;
        $line_should_be_time = false;

        $expect_title = false;
        $expect_class = false;
        $expect_professor = false;
        $expect_location = false;

        $current_title = $current_class = $current_professor = $current_location = "";
        $continue = true;

        $add_to_current = false;
        $add_value = "";

        foreach ($lines as $line) {
            if ($line == "SCA") {
                continue;
            } 

            if ($line == "REG") {
                error_log("Parser has detected: REG\nCurrent Week: $week_number");
                $continue = false;
            } else if ($line == "" || $continue) {
                continue;
            } 

            // Check for week headers
            if (strpos($line, 'Week A') !== false) {
                $week_number = 1;
                continue;
            } elseif (strpos($line, 'Week B') !== false) {
                $week_number = 2;
                continue;
            }

            if ($line == "P$expecting_period" || $line == "After School CCA") {
                $line_should_be_time = true;
                $period_number = $expecting_period;

                if ($period_number == 9) {
                    $expecting_period = 1;
                } else {
                    $expecting_period++;
                }
            } else if ($line_should_be_time) {
                $line_should_be_time = false;
                $period_times_index = $period_number;
                if ($period_times_index >= 0 && $period_times_index < count($period_times)) {
                    $period_time = $period_times[$period_times_index];
                } else {
                    // Handle the error - e.g., log it or set a default value
                    $period_time = "Unknown Time"; // or some default value
                }
                if (strval($period_time) == strval($line)) {
                    // this is correct!
                    error_log("A correct time has been guessed for the below query:");
                    
                }
                error_log("Period Number: $period_number, Expecting Period: $expecting_period, Index: $period_times_index, Guessed Time: $period_time, Actual Time: $line");
                error_log("Server has stopped ExpectPeriod. Now expecting Title.");
                $expect_title = true;
            } else if ($expect_title) {
                $current_title = $line;
                $expect_class = true;
                $expect_title = false;
                error_log("[Period] Recieved Title Data: $line");
            } else if ($expect_class) {
                $current_class = $line;
                $expect_professor = true;
                $expect_class = false;
                error_log("[Period] Recieved Class Data: $line");
            } else if ($expect_professor) {
                $current_professor = $line;
                $expect_location = true;
                $expect_professor = false;
                error_log("[Period] Recieved Professor Data: $line");
            } else if ($expect_location) {
                $current_location = $line;
                $expect_location = false;
                error_log("[Period] Recieved Location Data: $line");
                // this marks the end of one calendar item, so this can be added to the periods list
                $s = "======================================";
                error_log("A new period has been created with the following attributes:\nDay Number: $day_number\nWeek Number: $week_number\nPeriod: $period_number\nMore Details:\n$s\n  PERIOD $period_number\n  WEEK $week_number ON DAY $day_number\n    Item Title: $current_title\n    Item Location: $current_location\n    Item Professor: $current_professor\n    Item Class: $current_class");
                $periods[] = new Period(
                    $day_number, $week_number, $period_number, $current_title, $current_location, $current_professor, $current_class
                );

                if ($day_number < 5) {
                    $expect_title = true;
                }

                $day_number++;

                if ($day_number == 6) {
                    $day_number = 1;
                }
            }

            
        }

        return $periods;
    }
}

// Example usage
$parser = new TimetableParser();

$text = "MY SCHOOL TIMETABLE
Displayed below is your school timetable for the academic year 2024 - 2025. The timetable was last updated by LHG on 24-Dec-2024 and is on version 7.99.
Print My Timetable
Student Timetable	Mr N Lim
Week A [Week A]	Timetable Week 1
P	Monday	Tuesday	Wednesday	Thursday	Friday
REG
08:00					
P1
08:15	
Tutorial
10 TUT LFE
Ms Eastaff
W509C
Games
10 and 11 Rugby
Mr Kinloch
Astr3
English
10 ENG T
Ms Eastaff
W509C
Chemistry
10 CHE U
Mr Carter
W410S
Physics
10 PHY U
Mr Oakes
W420S
P2
09:15	
Biology
10 BIO U
Ms McCrohan
W403S
Games
10 and 11 Rugby
Mr Kinloch
Astr3
English
10 ENG T
Ms Eastaff
W509C
Spanish
10 SPA B
Mr Valdueza Garcia
W619C
Chemistry
10 CHE U
Mr Carter
W409S
P3
10:35	
Chemistry
10 CHE U
Mr Carter
W409S
Mathematics
10 MAT Z
Mr Sze
W526C
Chinese Second Language
10 CHI NN3
Ms Gu
W605O
Mathematics
10 MAT Z
Mr Sze
W526C
Computer Science
10 CSC A
Ms Darvesh
W345S - Computer Suite
P4
11:35	
Mathematics
10 MAT Z
Mr Sze
W526C
English
10 ENG T
Ms Eastaff
W509C
Physics
10 PHY U
Mr Oakes
W420S
Physics
10 PHY U
Mr Oakes
W420S
Drama
10 DRA D
Mr McInnes
W236S - Cheng Conservatory
P5
12:35					
P6
13:35	
Chinese Second Language
10 CHI NN3
Ms Gu
W605O
Computer Science
10 CSC A
Ms Darvesh
W345S - Computer Suite
Spanish
10 SPA B
Mr Valdueza Garcia
W619C
Drama
10 DRA D
Mr McInnes
WG08M - Shakespeare Studio
Mathematics
10 MAT Z
Mr Sze
W526C
P7
14:35	
English
10 ENG T
Ms Eastaff
W509C
Biology
10 BIO U
Ms McCrohan
W403S
Computer Science
10 CSC A
Ms Darvesh
W345S - Computer Suite
English
10 ENG T
Ms Eastaff
W509C
Biology
10 BIO U
Ms McCrohan
W403S
P8
15:35	
Prep Time
10Pre3
Zhou
W332C
US SCA
Bronze DofE Tue SS SCA CMD
Ms Doherty
W527C
Facing Challenges
10 FCH Ch
Ms Eastaff
W509C
US SCA
Challe Math Senior Thur SS SCA
Mr Yip
W518C
Chinese Second Language
10 CHI NN3
Ms Gu
W605O
After School CCA
16:35					
Week B [Week B]	Timetable Week 2
P	Monday	Tuesday	Wednesday	Thursday	Friday
REG
08:00					
P1
08:15	
Tutorial
10 TUT LFE
Ms Eastaff
W509C
Games
10 and 11 Rugby
Mr Kinloch
Astr3
Drama
10 DRA D
Mr McInnes
WG08M - Shakespeare Studio
Chemistry
10 CHE U
Mr Carter
W410S
Physics
10 PHY U
Mr Oakes
W420S
P2
09:15	
Biology
10 BIO U
Ms McCrohan
W403S
Games
10 and 11 Rugby
Mr Kinloch
Astr3
English
10 ENG T
Ms Eastaff
W509C
Spanish
10 SPA B
Mr Valdueza Garcia
W619C
Chemistry
10 CHE U
Mr Carter
W409S
P3
10:35	
Chemistry
10 CHE U
Mr Carter
W409S
Mathematics
10 MAT Z
Mr Sze
W526C
Chinese Second Language
10 CHI NN3
Ms Gu
W605O
Mathematics
10 MAT Z
Mr Sze
W526C
Spanish
10 SPA B
Mr Valdueza Garcia
W619C
P4
11:35	
Mathematics
10 MAT Z
Mr Sze
W526C
English
10 ENG T
Ms Eastaff
W509C
Physics
10 PHY U
Mr Oakes
W420S
Physics
10 PHY U
Mr Oakes
W420S
Drama
10 DRA D
Mr McInnes
W252S - Playfair Studio
P5
12:35					
P6
13:35	
Chinese Second Language
10 CHI NN3
Ms Gu
W605O
Computer Science
10 CSC A
Ms Darvesh
W345S - Computer Suite
Spanish
10 SPA B
Mr Valdueza Garcia
W619C
Drama
10 DRA D
Mr McInnes
WG08M - Shakespeare Studio
Mathematics
10 MAT Z
Mr Sze
W526C
P7
14:35	
English
10 ENG T
Ms Eastaff
W509C
Biology
10 BIO U
Ms McCrohan
W403S
Computer Science
10 CSC A
Ms Darvesh
W345S - Computer Suite
English
10 ENG T
Ms Eastaff
W509C
Biology
10 BIO U
Ms McCrohan
W403S
P8
15:35	
Prep Time
10Pre3
Zhou
W332C
US SCA
Bronze DofE Tue SS SCA CMD
Ms Doherty
W527C
Facing Challenges
10 FCH Ch
Ms Eastaff
W509C
US SCA
Challe Math Senior Thur SS SCA
Mr Yip
W518C
Tutorial
10 TUT LFE
Ms Eastaff
W509C
After School CCA
16:35";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $periods = $parser->parseTimetable($_POST['text']);

    $db = new Database();
    // Output the parsed periods
    foreach ($periods as $period) {
        // echo "Day: {$period->day_number}, Week: {$period->week_number}, Period: {$period->period_number}, Title: {$period->title}, Location: {$period->location}, Professor: {$period->professor}, Class: {$period->class}\n";
        error_log("[Database] Trying to save Period...");
        if ($period->saveToDatabase($db)) {
            error_log("[Database] Period has been successfully saved");
        } else {
            error_log("[Database] Failed to save period (1 or more invalid arguments)");
        }
    }
} else {
    $periods = $parser->parseTimetable($text);
    $db = new Database();
    // Output the parsed periods
    foreach ($periods as $period) {
        // echo "Day: {$period->day_number}, Week: {$period->week_number}, Period: {$period->period_number}, Title: {$period->title}, Location: {$period->location}, Professor: {$period->professor}, Class: {$period->class}\n";
        error_log("[Database] Trying to save Period...");
        if ($period->saveToDatabase($db)) {
            error_log("[Database] Period has been successfully saved");
        } else {
            error_log("[Database] Failed to save period (1 or more invalid arguments)");
        }
    }
}   

header('Location: calendar/index.php');
exit();

?>


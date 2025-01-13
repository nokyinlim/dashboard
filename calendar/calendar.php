<?php



// calendar.php
class Calendar {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getCurrentPeriod(int $timezone = 8) {
        $dateTime = new DateTime("now", new DateTimeZone("GMT+$timezone"));
        $current_time = $dateTime->format('H:i');
        $current_day = (int)$dateTime->format('N');
        $current_week = (int)((date('W', $dateTime->getTimestamp()) % 2) + 1);

        foreach (Config::PERIODS as $period_number => $period) {
            if ($current_time >= $period['start'] && $current_time <= $period['end']) {
                return [
                    'period' => $period_number,
                    'info' => $this->db->getPeriod($current_day, $current_week, $period_number)
                ];
            }
        }
        return null;
    }

    public function getNextPeriod(int $timezone = 8) {
        $dateTime = new DateTime("now", new DateTimeZone("GMT+$timezone"));
        $current_time = $dateTime->format('H:i');
        $current_day = (int)$dateTime->format('N');
        $current_week = (int)((date('W', $dateTime->getTimestamp()) % 2) + 1);

        $next_period = null;
        foreach (Config::PERIODS as $period_number => $period) {
            if ($current_time < $period['start']) {
                // skip lunch period if it is next
                if ($period_number == 5) {
                    $next_period = 6;
                } else {
                    $next_period = $period_number;
                }
                break;
            }
        }

        if ($next_period) {
            return [
                'period' => $next_period,
                'info' => $this->db->getPeriod($current_day, $current_week, $next_period)
            ];
        }
        return null;
    }

    public function getDaySchedule($day = null, $week = null) {
        if ($day === null) $day = date('N');
        if ($week === null) $week = (int)((date('W') % 2) + 1);

        $schedule = [];
        foreach (Config::PERIODS as $period_number => $period) {
            $schedule[$period_number] = [
                'time' => $period,
                'info' => $this->db->getPeriod($day, $week, $period_number)
            ];
        }
        return $schedule;
    }

    // for call where caller == index.php, run only as DMtrue, otherwise abort
    public function generateTodayView($week = null, $dark_mode = true, int $timezone = 8) {
        if ($week === null) $week = (int)((date('W') % 2) + 1);

        $day_name = "Today's Classes";

        $dateTime = new DateTime("now", new DateTimeZone("GMT+$timezone"));
        $current_time = $dateTime->format('H:i');
        $current_day = (int)$dateTime->format('N');

        $dark_mode_class = $dark_mode ? 'dark-mode' : '';

        // Table start
        $table = '<div class="' . $dark_mode_class . '">';
        $table .= '<table class="schedule-table">';

        // Header row
        $table .= '<tr>';
        $table .= "<th>$day_name</th>";
        $table .= '</tr>';

        $has_been_current = false;
        $has_been_next = false;

        // Periods rows
        foreach (Config::PERIODS as $period_number => $period_time) {
            $table .= '<tr>';

            $p_info = "";
            if ($period_number == 5) {
                $p_info = " (Lunch)";
            } else if ($period_number == 9) {
                $p_info = " (CCA)";
            }
            
            // Days
            for ($day = 1; $day <= 5; $day++) {
                $period_info = $this->db->getPeriod($day, $week, $period_number);
                // check period

                // tracks whether the iterating over current day
                $is_day = $day == $current_day;
                if (!$is_day) {
                    continue;
                }
                

                $is_current = ($day == $current_day && 
                             $current_time >= $period_time['start'] && 
                             $current_time <= $period_time['end']);
                

                // basically, if it is current period then it sets has-been-current to true.
                // this detects that and sets the current period to the next period                
                $is_next = ($day == $current_day && $has_been_current && !$has_been_next);
                if ($is_next) {
                    if ($period_info) {
                        $has_been_next = true;
                    }
                    error_log("Detected next as day $current_day, week $week with period $period_number");
                } else if ($is_current) {
                    $has_been_current = true;
                }


                $current_class = $is_current ? 'current-period' : ($is_next ? 'next-period' : ($is_day ? 'current-day' : ''));
                $table .= "<td class='$current_class'>";
                $message = '';
                if ($period_info) {
                    $table .= "<div class='period-info'>";
                    $table .= "<strong>{$period_info['title']}$message</strong><br>";
                    $table .= "<strong>{$period_info['class']}</strong><br>";
                    $table .= "{$period_info['location']}<br>";
                    $table .= "{$period_info['professor']}";
                    $table .= "</div>";
                } else {
                    $table .= "<span class='no-class'>No class scheduled</span>";
                }
                $table .= "</td>";
            }
            
            $table .= '</tr>';
        }
        
        $table .= '</table>';
        $table .= '</div>';
        return $table;
    }

    public function generateTableView($week = null, $dark_mode = false, int $timezone = 8) {
        if ($week === null) $week = (int)((date('W') % 2) + 1);
    
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $dateTime = new DateTime("now", new DateTimeZone("GMT+$timezone"));
        $current_time = $dateTime->format('H:i');
        $current_day = (int)$dateTime->format('N');
        // $current_week = (int)((date('W', $dateTime->getTimestamp()) % 2) + 1);
        
        $dark_mode_class = $dark_mode ? 'dark-mode' : '';
        
        // Table start
        $table = '<div class="' . $dark_mode_class . '">';
        $table .= '<table class="schedule-table">';
        
        // Header row
        $table .= '<tr>';
        $table .= '<th>Period</th>';
        $table .= '<th>Time</th>';
        foreach ($days as $day) {
            $table .= "<th>$day</th>";
        }
        $table .= '</tr>';

        $has_been_current = false;
        $has_been_next = false;
        
        // Periods rows
        foreach (Config::PERIODS as $period_number => $period_time) {
            $table .= '<tr>';

            $p_info = "";
            if ($period_number == 5) {
                $p_info = " (Lunch)";
            } else if ($period_number == 9) {
                $p_info = " (CCA)";
            }
            
            // Period number and time
            $table .= "<td>P$period_number$p_info</td>";
            $table .= "<td>{$period_time['start']}-{$period_time['end']}</td>";
            
            // Days
            for ($day = 1; $day <= 5; $day++) {
                $period_info = $this->db->getPeriod($day, $week, $period_number);
                // check period
                $is_day = $day == $current_day;

                

                $is_current = ($day == $current_day && 
                             $current_time >= $period_time['start'] && 
                             $current_time <= $period_time['end']);
                

                // basically, if it is current period then it sets has-been-current to true.
                // this detects that and sets the current period to the next period                
                $is_next = ($day == $current_day && $has_been_current && !$has_been_next);
                if ($is_next) {
                    if ($period_info) {
                        $has_been_next = true;
                    }
                    error_log("detect next as day $current_day, week $week and period $period_number");
                } else if ($is_current) {
                    $has_been_current = true;
                }


                $current_class = $is_current ? 'current-period' : ($is_next ? 'next-period' : ($is_day ? 'current-day' : ''));
                $table .= "<td class='$current_class'>";
                $message = '';
                if ($period_info) {
                    $table .= "<div class='period-info'>";
                    $table .= "<strong>{$period_info['title']}$message</strong><br>";
                    $table .= "<strong>{$period_info['class']}</strong><br>";
                    $table .= "{$period_info['location']}<br>";
                    $table .= "{$period_info['professor']}";
                    $table .= "</div>";
                } else {
                    $table .= "<span class='no-class'>No class scheduled</span>";
                }
                $table .= "</td>";
            }
            
            $table .= '</tr>';
        }
        
        $table .= '</table>';
        $table .= '</div>';
        return $table;
    }
}
<?php
// session_start();

$type = $_GET['type'] ?? '';

// This file generates the navbar which should be used by all pages
// Other files using their own navbar should be changed soon
// The navbar may be generated based on the type of page it is on
// The type of page is determined by the type GET parameter
// The type of page can be one of the following:
    // home: the default navbar for non-logged in users
        // features: the features page; a submenu when selecting 'Features' section within home

function generateLinks($links) {
    foreach ($links as $link) {
        $anchor = '<a href="' . htmlspecialchars($link['url']) . '"';
        if (isset($link['onclick'])) {
            $anchor .= ' onclick="' . htmlspecialchars($link['onclick']) . '"';
        }
        if (isset($link['class'])) {
            $anchor .= ' class="' . ($link['class']) . '"';
        }
        $anchor .= '>' . $link['text'] . '</a>';

        echo $anchor;
    }
}

if ($type == 'home') {
    generateLinks([
        ['url' => '#about', 'text' => 'About Us'],
        ['url' => '#contact', 'text' => 'Contact'],
        ['url' => '#help', 'text' => 'Help'],
    ]);
} elseif ($type == 'features') {
    // echo '<div class="nav-left" id="nav-left">';
    // echo '<p class="white-text-paragraph">I am alive!</p>';
    generateLinks([
        ['url' => '#about', 'text' => 'About Us'],
        ['url' => '#', 'text' => 'Go Back  <img src="images/chevron-left-2.png" height="10" width="10">', 'onclick' => 'slideOut(); return false;'],
        ['url' => '/todo', 'text' => 'To Do List', 'class' => 'navbar-link-from-left'],
        ['url' => '/calendar', 'text' => 'Calendar', 'class' => 'navbar-link-from-left'],
        ['url' => '#feature3', 'text' => 'Homework Tracking', 'class' => 'navbar-link-from-left'],
    ]);
    // echo '<a href="#" onclick="fetchLinks(\'features\'); return false;">Go Back</a>';
    // echo "<script>console.log('Correct if/else statement');</script>";
    // echo '</div>';
} else {
    // echo '<div class="nav-left" id="nav-left">';
    // echo "<script>console.log('Went into the Else statement somehow.');</script>";
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        generateLinks([
            ['url' => '/', 'text' => 'About Us'],
            ['url' => '#', 'text' => 'Features  <img src="images/chevron-right-2.png" height="10" width="10">', 'onclick' => 'fetchLinks(\'features\'); return false;'],
            ['url' => '/', 'text' => 'Contact Us', 'class' => 'navbar-link-from-left'],
            ['url' => '/', 'text' => 'Help / Support', 'class' => 'navbar-link-from-left'],
        ]);
    }

    
    
    // echo '</div>';
}

// navbar html stuff

function renderNavbar() {
    ?>
    <div class="navbar" onmouseover="resetTimer()" onmouseleave="startTimer()">
        <div class="nav-left" id="nav-left">
            <?php if (!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]): ?>
                <a href="/">About Us</a>
                <a href="#" onclick="fetchLinks('features'); return false;">Features <img class="navbar-link-from-left" src="images/chevron-right-2.png" height="10" width="10"></a>
                <a href="/">Contact Us</a>
                <a href="/">Help / Support</a>
            <?php else: ?>
                <a href="/todo">To Do List</a>
                <a href="/calendar">Calendar</a>
                <a href="#">Homework Tracking</a>
            <?php endif; ?>
        </div>
        <div class="nav-right" id="nav-right">
            <?php if (!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]): ?>
                <a href="/sign-up.php">Don't have an account? Sign Up</a>
            <?php else: ?>
                <a href="#settings">Settings</a>
                <a href="/logout.php">Logout</a>
            <?php endif; ?>
        </div>
    </div>
    <?php
}


?>
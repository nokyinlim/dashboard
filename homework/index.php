<?php


include 'homework_form.php';
include 'date_formatter.php';

if (!isset($_SESSION)) {
    session_start();
    header('Location: localhost:8000');
    exit();
}


// Settings initialization
if (!isset($_SESSION['use_homework_exact_date_form'])) {
    $_SESSION['use_homework_exact_date_form'] = false;
    $exactForm = &$_SESSION['use_homework_exact_date_form'];
} else {
    $exactForm = &$_SESSION['use_homework_exact_date_form'];
}

// Initialize homework array in session if not already set
if (!isset($_SESSION['homework'])) {
    $_SESSION['homework'] = [];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_index'])) {
        // Remove homework entry
        $indexToDelete = (int)$_POST['delete_index'];
        if (isset($_SESSION['homework'][$indexToDelete])) {
            unset($_SESSION['homework'][$indexToDelete]);
            $_SESSION['homework'] = array_values($_SESSION['homework']); // Re-index the array
        }

        header('Location: ' . $_SERVER['PHP_SELF']); // Redirect to the same page
        exit(); // Ensure no further code is executed after the redirect
    } else {
        // Add new homework entry
        $title = trim($_POST['title']);
        $due_date = $_POST['due_date'] ?: null;
        $notes = $_POST['notes'] ?: null;
        $links = $_POST['links'] ?: null;
        $subject = $_POST['subject'] ?: null;

        $_SESSION['homework'][] = [
            'title' => $title,
            'due_date' => $due_date,
            'notes' => $notes,
            'links' => $links,
            'subject' => $subject,
            'created_at' => time()
        ];

        header('Location: ' . $_SERVER['PHP_SELF']); // Redirect to the same page
        exit(); // Ensure no further code is executed after the redirect
    }
}

// Sort homework if sort criteria is set
if (isset($_GET['sort_by'])) {
    $sortBy = $_GET['sort_by'];
    usort($_SESSION['homework'], function($a, $b) use ($sortBy) {
        if ($sortBy === 'title') {
            return strcmp($a['title'], $b['title']);
        } elseif ($sortBy === 'due_date') {
            // Extract the date part for sorting
            $dateA = explode(' - ', $a['due_date'])[0];
            $dateB = explode(' - ', $b['due_date'])[0];
            return strcmp($dateA, $dateB);
        } elseif ($sortBy === 'subject') {
            return strcmp((isset($a['subject']) ? $a['subject'] : "zzzzzzzzzzzzz"), (isset($b['subject']) ? $b['subject'] : "zzzzzzzzzzzzz"));
        }
        return 0; // No sorting
    });

    header('Location: ' . $_SERVER['PHP_SELF']); // Redirect to the same page
    exit(); // Ensure no further code is executed after the redirect
}

$homeworks = $_SESSION['homework'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homework Tracker</title>
    <link rel="stylesheet" href="../style.css">
    <script src="../navbar.js"></script>
</head>
<body>
    <div class="navbar" onmouseover="resetTimer()" onmouseleave="startTimer()">
        <div class="nav-left" id="nav-left">
            <?php if (!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]): ?>
                <a href="/">About Us</a>
                <a href="#" onclick="updateAndFetch('features'); return false;">Features <img class="navbar-link-from-left" src="images/chevron-right-2.png" height="10" width="10"></a>
                <a href="/">Contact Us</a>
                <a href="/">Help / Support</a>
            <?php else: ?>
                <a href="/todo">To Do List</a>
                <a href="/reminders">Reminders</a>
                <a href="#">Homework Tracking</a>
            <?php endif; ?>
            
        </div>
        <div class="nav-right" id="nav-right">
            

            <?php if (!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]): ?>
                <a href="/">Don't have an account? Sign Up</a>
            <?php else: ?>
                <a href="#settings">Settings</a>
                <a href="/logout.php">Logout</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="horizontal-container">
        <div class="left-side">
            <div class="content-body">
                <?php echo renderCalendar(); ?>
            </div>
        </div>

        <div class="separator"></div>

        <div class="right-side" style="flex-direction: column;">
                <!-- <div>
                    <label for="sort">Sort by:</label>
                    <select id="sort" onchange="sortHomework()">
                        <option value="">Select</option>
                        <option value="title">Name</option>
                        <option value="due_date">Due Date</option>
                        <option value="subject">Subject</option>
                    </select>
                </div> -->

            <div class="content-body">
<!----------------------------------------------------------------------->
<div class="container-body">
    <div class="container" style="grid-template-columns: repeat(1, 1fr);">
        <?php if (empty($homeworks)): ?>
            <div class="box" style="width: 600px; height:120px;">
                <div class="box-content">
                    <h2>This is your Homework List!</h2>
                    <p>The Homework List automatically tracks, and sorts, all your upcoming assignments.</p>
                    <p>Start by adding your first assignment using the menu on the left of the screen.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($homeworks as $index => $homework): ?>
                <div class="box" style="width: 600px; height:120px;">
                    <div class="box-content" style="flex-direction:unset">

                        <div class="box-subcontent-left">
                            <h2>
                                <?php 
                                $date = htmlspecialchars(explode(' - ', $homework['due_date'])[($exactForm ? 0 : 1)]);
                                echo htmlspecialchars($homework['title']);
                                echo " - ";
                                echo $date; ?>
                            </h2>
                            <?php if (isset($homework['notes'])): ?>
                                <?= "<p>" . htmlspecialchars($homework['notes']) . "</p>"?>
                            <?php endif; ?>
                            <br>
                            <?php if (isset($homework['links'])): ?>
                                <a href=<?=htmlspecialchars($homework['links'])?>><?= htmlspecialchars($homework['links']) ?></a>
                            <?php endif; ?>
                            <br>
                            <?php if (isset($homework['subject'])): ?>
                                <?= "<p class='caption'>[" . htmlspecialchars($homework['subject']) . "]</p>" ?>
                            <?php endif; ?>
                        </div>
                        <div class="box-clickable box-subcontent-right">
                            <button class="button-large">Edit</button>
                            <button class="remove-button-large" onClick="removeHomework(<?php echo "$index"; ?>)">Remove</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<!----------------------------------------------------------------------->
            </div>
        </div>
    </div>

    <form method="POST" style="display: none;" id="remove-form">
        <input type="hidden" id="remove-input" name="delete_index">
        <!-- <button type="submit">Remove</button> -->
    </form>

    <script>

        function removeHomework(index) {
            document.getElementById('remove-input').value = index;
            document.getElementById('remove-form').submit();
        }
        function sortHomework() {
            const sortBy = document.getElementById('sort').value;
            window.location.href = `?sort_by=${sortBy}`;
        }

        document.body.onmousemove = e => {
        for(const box of document.getElementsByClassName("box")) {
            const rect = box.getBoundingClientRect(),
                x = e.clientX - rect.left,
                y = e.clientY - rect.top;

            box.style.setProperty("--mouse-x", `${x}px`);
            box.style.setProperty("--mouse-y", `${y}px`);
        }
    };
    </script>
</body>
</html>
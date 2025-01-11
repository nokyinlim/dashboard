<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading...</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
            padding: 0 20px; /* Add padding for wider layout */
        }
        .loading-container {
            text-align: center;
            width: 80%; /* Set a wider width for the container */
        }
        .loading-bar {
            width: 100%;
            background: #e0e0e0;
            border-radius: 25px;
            overflow: hidden;
            margin: 20px 0;
        }
        .loading-progress {
            height: 20px;
            background: #76c7c0;
            width: 0;
            border-radius: 25px;
            animation: loading 1s linear forwards;
        }
        @keyframes loading {
            0% { width: 0; }
            75% { width: 35%; }
            100% { width: 100%; }
        }
        .loading-text {
            font-size: 2em; /* Increase font size for better visibility */
            color: #333;
        }
    </style>
</head>
<body>

<div class="loading-container">
    <div class="loading-text" id="loading-text">Parsing Data...</div>
    <div class="loading-bar">
        <div class="loading-progress"></div>
    </div>
</div>

<script>
    element = document.getElementById('loading-text');
    setTimeout(() => {
        element.innerHTML = "Finishing Up...";
    }, 750)
    setTimeout(() => {
        window.location.href = 'index.php';
    }, 1000);
</script>

</body>
</html>
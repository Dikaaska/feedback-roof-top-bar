<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Guest Feedback</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">


<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<form id="feedbackForm" method="post" action="save.php">

    <div class="logo-header">
        <img src="logo_hic.png">
    </div>

    <h2>ROOFTOP POOL BAR</h2>

    <div class="guest-info">
        <input type="text" name="guest_name" placeholder="Guest Name (optional)">
        <input type="text" name="room_number" placeholder="Room Number (optional)">
    </div>

    <div class="progress-wrap">
        <div class="progress-bar" id="progressBar"></div>
    </div>

    <!-- QUESTION TEMPLATE -->
    <div class="question">
        <div class="emoji-box">
            <h3>How was your swimming experience?</h3>
            <span class="emoji">🙂</span>
            <span class="super-text">Super!!!</span>
            <span class="emoji-label"></span>
        </div>
        <div class="scale" data-name="swimming"></div>
        <input type="hidden" name="swimming">
        <div class="error">Please select score</div>
    </div>

    <div class="question">
        <div class="emoji-box">
            <h3>How was the food experience?</h3>
            <span class="emoji">🙂</span>
            <span class="super-text">Super!!!</span>
            <span class="emoji-label"></span>
        </div>
        <div class="scale" data-name="food"></div>
        <input type="hidden" name="food">
        <div class="error">Please select score</div>
    </div>

    <div class="question">
        <div class="emoji-box">
            <h3>How was the beverage experience?</h3>
            <span class="emoji">🙂</span>
            <span class="super-text">Super!!!</span>
            <span class="emoji-label"></span>
        </div>
        <div class="scale" data-name="beverage"></div>
        <input type="hidden" name="beverage">
        <div class="error">Please select score</div>
    </div>

    <div class="question">
        <div class="emoji-box">
            <h3>How was the Wi-Fi connection?</h3>
            <span class="emoji">🙂</span>
            <span class="super-text">Super!!!</span>
            <span class="emoji-label"></span>
        </div>
        <div class="scale" data-name="wifi"></div>
        <input type="hidden" name="wifi">
        <div class="error">Please select score</div>
    </div>

    <div class="question">
        <div class="emoji-box">
            <h3>How was the music?</h3>
            <span class="emoji">🙂</span>
            <span class="super-text">Super!!!</span>
            <span class="emoji-label"></span>
        </div>
        <div class="scale" data-name="music"></div>
        <input type="hidden" name="music">
        <div class="error">Please select score</div>
    </div>

    <div class="note-box">
        <label for="note">Additional Note (optional)</label>
            <textarea 
            name="note" 
            id="note" 
            placeholder="Write your comment here..."
            rows="3"></textarea>
    </div>
    <div class="submit-wrap">
        <button type="submit" id="submitBtn" class="submit-btn">Submit Feedback</button>
    </div>


    <div class="subtitle" style="margin-top:30px">
        Thank you for sharing your experience with us.
    </div>




</form>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<audio id="popSound">
  <source src="https://assets.mixkit.co/sfx/preview/mixkit-select-click-1109.mp3">
</audio>


<script src="assets/scale.js"></script>





</body>
</html>

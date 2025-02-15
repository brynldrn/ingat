<?php
session_start();
?>
    <script>
        setTimeout(function() {
            window.location.href = 'index.php'; 
        }, 2000);
    </script>

<div class="container">
    <div class="alert alert-success" style="margin-top: 20px;">
        <?php
        if (isset($_SESSION['errmsg'])) {
            echo htmlentities($_SESSION['errmsg']);
            unset($_SESSION['errmsg']); // Clear the message after displaying
        }
        ?>
    </div>
</div>

</body>
</html>

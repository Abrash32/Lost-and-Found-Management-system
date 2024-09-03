<?php
    session_unset();
    session_abort();
    session_destroy();
    header("location: admin_login.php?out");
?>
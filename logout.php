<?php
session_start();
session_destroy();
header("Location: frontend/html/iniciosesion.html"); 
exit;

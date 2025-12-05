<?php

session_start();

echo json_encode(['session'=> $_SESSION['user_id']]);
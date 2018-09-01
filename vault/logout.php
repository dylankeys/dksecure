<?php 
// firstly open the session
session_start(); 

// remove all the variables in the session 
session_unset(); 

// destroy the session 
session_destroy();  

// back to home
header("Location: ../");
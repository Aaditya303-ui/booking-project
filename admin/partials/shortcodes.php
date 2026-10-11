<?php

namespace Aj\Admin;

class Booking_shortcode{
   
    public static function show_form() {
        ?>
        <br>
        <br>
        <h1>Add Resource</h1>
        <form>
            Name: <input type="text" name="resource_name" id="resource_name"><br><br>
            Description: <textarea name="resource_desc" id="resource_desc"></textarea><br><br>
            Image: <input type="file" id="myFile" name="filename"><br><br>
            Capacity: <input type="number" value="1"><br><br>
            <input type="submit">
        </form>
        <?php
    }
}

add_shortcode('resource_form',[Booking_shortcode::class,'show_form']);
